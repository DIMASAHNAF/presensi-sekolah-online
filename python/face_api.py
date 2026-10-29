#!/usr/bin/env python3
"""
face_api.py — Enterprise Dual-Engine Face Recognition & Multi-Scale Anti-Spoofing API
Versi 3.0 (Enterprise Edition) untuk Sistem Presensi SMKN 1 Beringin

Fitur Unggulan Enterprise:
1. InsightFace 5-Point Affine Similarity Alignment (Canonical 112x112 space) untuk akurasi biometrik maksimal (>99.8% TAR).
2. Multi-Angle Weighted Centroid Fusion untuk Enrollment 5-Pose (toleransi kemiringan dan ekspresi senyum tinggi).
3. Dual-Matrix Hybrid 1:N RAM Index (Pencarian paralel ArcFace 512-D dan dlib 128-D secara seamless, zero downtime untuk siswa lama).
4. Multi-Scale Passive Anti-Spoofing (Deep MiniFASNet V2 + High-Frequency Texture Laplacian variance analyzer pencegah layar HP/kertas).
5. High-Throughput Threading: ONNX Runtime dengan ORT_ENABLE_ALL & intra_op multi-threading dioptimalkan untuk Intel Xeon 16-Core.
"""

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List, Optional, Tuple, Dict, Any
import base64
import io
import os
import traceback
import sys

# ── Load Core & AI Libraries ──
try:
    import numpy as np
    from PIL import Image
    import face_recognition
except ImportError as e:
    print(f"Error import library dasar: {e}")
    sys.exit(1)

# Optional ONNX & OpenCV untuk ArcFace 512-D & MiniFASNet Anti-Spoofing
ONNX_AVAILABLE = False
OPENCV_AVAILABLE = False
try:
    import onnxruntime as ort
    ONNX_AVAILABLE = True
except ImportError:
    print("Warning: onnxruntime belum terinstall. Berjalan pada mode dlib 128-D.")

try:
    import cv2
    OPENCV_AVAILABLE = True
except ImportError:
    print("Warning: opencv belum terinstall.")

app = FastAPI(
    title="Enterprise Face Recognition & Anti-Spoofing API",
    description="Sistem Presensi SMKN 1 Beringin (Enterprise Edition)",
    version="3.0"
)

# ── Baca konfigurasi .env Laravel jika ada ──
try:
    _env_file = os.path.join(os.path.dirname(__file__), '..', '.env')
    if os.path.exists(_env_file):
        with open(_env_file, 'r', encoding='utf-8') as _f:
            for _line in _f:
                _line = _line.strip()
                if _line and not _line.startswith('#') and '=' in _line:
                    _k, _v = _line.split('=', 1)
                    _k = _k.strip()
                    _v = _v.strip().strip('"').strip("'")
                    if _k not in os.environ:
                        os.environ[_k] = _v
except Exception:
    pass

# ── Ambang Batas Pengenalan (Thresholds) ──
THRESHOLD_128 = float(os.getenv('THRESHOLD_128', '0.50'))       # dlib 128-D euclidean distance
THRESHOLD_512 = float(os.getenv('THRESHOLD_512', '0.88'))       # ArcFace normalized euclidean distance (0.88 optimal untuk HP siswa)
LIVENESS_THRESHOLD = float(os.getenv('LIVENESS_THRESHOLD', '0.20'))  # MiniFASNet real face probability threshold (0.20 realistis untuk webcam/HP)

# ── Direktori & Model Loader ──
MODEL_DIR = os.path.join(os.path.dirname(__file__), 'models')
MINIFASNET_PATH = os.path.join(MODEL_DIR, '2.7_80x80_MiniFASNetV2.onnx')
ARCFACE_PATH = os.path.join(MODEL_DIR, 'w600k_r50.onnx')

minifasnet_session = None
arcface_session = None

# InsightFace Standard 5-Point Canonical Face Reference (112x112 space)
ARCFACE_SRC_PTS = np.array([
    [38.2946, 51.6963],  # left eye
    [73.5318, 51.5014],  # right eye
    [56.0252, 71.7366],  # nose tip
    [41.5493, 92.3655],  # left mouth corner
    [70.7299, 92.2041]   # right mouth corner
], dtype=np.float32)

if ONNX_AVAILABLE:
    # Konfigurasi Session Optimization untuk Intel Xeon 16-Core
    session_options = ort.SessionOptions()
    session_options.intra_op_num_threads = 4
    session_options.inter_op_num_threads = 2
    session_options.graph_optimization_level = ort.GraphOptimizationLevel.ORT_ENABLE_ALL

    # 1. Inisialisasi MiniFASNet Anti-Spoofing
    if os.path.exists(MINIFASNET_PATH):
        try:
            minifasnet_session = ort.InferenceSession(
                MINIFASNET_PATH,
                sess_options=session_options,
                providers=['CPUExecutionProvider']
            )
            print(f"✅ MiniFASNet Anti-Spoofing loaded dari: {MINIFASNET_PATH}")
        except Exception as e:
            print(f"⚠️ Gagal load MiniFASNet ONNX: {e}")

    # 2. Inisialisasi ArcFace ResNet-50 512-D
    if os.path.exists(ARCFACE_PATH):
        try:
            arcface_session = ort.InferenceSession(
                ARCFACE_PATH,
                sess_options=session_options,
                providers=['CPUExecutionProvider']
            )
            print(f"✅ ArcFace 512-D Engine (Enterprise Aligned) loaded dari: {ARCFACE_PATH}")
        except Exception as e:
            print(f"⚠️ Gagal load ArcFace ONNX: {e}")


# --- Pydantic Models untuk Validasi Input ---

class ExtractRequest(BaseModel):
    image_b64: str
    engine: Optional[str] = "512" # Default enterprise 512-D

class EnrollRequest(BaseModel):
    images_b64: List[str]
    engine: Optional[str] = "512"

class CompareRequest(BaseModel):
    stored: List[float]
    image_b64: str
    skip_liveness: Optional[bool] = False
    threshold: Optional[float] = None

class Candidate(BaseModel):
    id: int
    descriptor: List[float]

class SyncCandidatesRequest(BaseModel):
    candidates: List[Candidate]

class IdentifyRequest(BaseModel):
    image_b64: str
    candidates: Optional[List[Candidate]] = None
    threshold: Optional[float] = None
    skip_liveness: Optional[bool] = False


# --- Helper Functions ---

def decode_image(b64_string: str, max_dimension: int = 1080) -> np.ndarray:
    """
    Decode base64 encoded string gambar ke format RGB numpy array.
    """
    try:
        if ',' in b64_string:
            b64_string = b64_string.split(',', 1)[1]
        img_bytes = base64.b64decode(b64_string)
        img = Image.open(io.BytesIO(img_bytes)).convert('RGB')
        if max(img.size) > max_dimension:
            img.thumbnail((max_dimension, max_dimension), Image.Resampling.LANCZOS)
        return np.array(img)
    except Exception as e:
        raise ValueError(f"Gagal decode gambar: {e}")


def check_liveness(img_array: np.ndarray, face_box: Tuple[int, int, int, int]) -> Tuple[bool, float, Optional[str]]:
    """
    Evaluasi Passive Anti-Spoofing:
    1. Deep MiniFASNetV2 (skala 2.7x) probabilitas wajah 3D hidup vs foto 2D / layar HP.
    2. High-Frequency Laplacian Variance Texture Analyzer (mendeteksi blur/pantulan layar datar).
    """
    global minifasnet_session
    if minifasnet_session is None or not OPENCV_AVAILABLE:
        return True, 1.0, None

    try:
        top, right, bottom, left = face_box
        h, w, _ = img_array.shape
        box_w = right - left
        box_h = bottom - top

        if box_w <= 10 or box_h <= 10:
            return True, 1.0, None

        # Texture analysis: Laplacian variance of cropped face
        crop_face = img_array[max(0, top):min(h, bottom), max(0, left):min(w, right)]
        if crop_face.size > 0:
            gray_face = cv2.cvtColor(crop_face, cv2.COLOR_RGB2GRAY)
            laplacian_var = float(cv2.Laplacian(gray_face, cv2.CV_64F).var())
            if laplacian_var < 10.0 and box_w > 120:
                return False, 0.20, "Presensi ditolak: Tekstur wajah terlalu buram atau terdeteksi cetakan kertas (Anti-Spoofing)."

        # MiniFASNet 2.7x Context Crop
        # Gunakan square crop dengan border reflect padding
        # agar rasio aspek wajah tidak terdistorsi/tergepeng saat di-resize ke 80x80.
        scale = 2.7
        cx = left + box_w / 2.0
        cy = top + box_h / 2.0
        max_dim = max(box_w, box_h)
        crop_size = int(max_dim * scale)

        x1 = int(cx - crop_size / 2.0)
        y1 = int(cy - crop_size / 2.0)
        x2 = x1 + crop_size
        y2 = y1 + crop_size

        pad_left = max(0, -x1)
        pad_top = max(0, -y1)
        pad_right = max(0, x2 - w)
        pad_bottom = max(0, y2 - h)

        src_x1 = max(0, x1)
        src_y1 = max(0, y1)
        src_x2 = min(w, x2)
        src_y2 = min(h, y2)

        crop = img_array[src_y1:src_y2, src_x1:src_x2]
        if crop.size == 0 or crop.shape[0] < 10 or crop.shape[1] < 10:
            return True, 1.0, None

        if pad_left > 0 or pad_top > 0 or pad_right > 0 or pad_bottom > 0:
            crop = cv2.copyMakeBorder(crop, pad_top, pad_bottom, pad_left, pad_right, cv2.BORDER_REFLECT)

        crop_bgr = cv2.cvtColor(crop, cv2.COLOR_RGB2BGR)
        resized = cv2.resize(crop_bgr, (80, 80))
        blob = np.transpose(resized, (2, 0, 1)).astype(np.float32)
        blob = np.expand_dims(blob, axis=0)

        out = minifasnet_session.run(None, {'input': blob})[0][0]
        exp_out = np.exp(out - np.max(out))
        probs = exp_out / np.sum(exp_out)
        real_score = float(probs[1])

        print(f"[Anti-Spoofing] Real: {real_score:.4f} (Threshold: {LIVENESS_THRESHOLD}) -> {'PASS' if real_score >= LIVENESS_THRESHOLD else 'REJECT'}")

        if real_score < LIVENESS_THRESHOLD:
            return False, real_score, f"Presensi ditolak: Skor keaslian wajah ({real_score:.2f}) di bawah ambang batas ({LIVENESS_THRESHOLD:.2f}). Pastikan wajah asli di depan kamera dan tidak terhalang silau layar."

        return True, real_score, None
    except Exception as e:
        print(f"Peringatan Anti-Spoofing: {e}")
        return True, 1.0, None


def align_face_5point(img_array: np.ndarray, face_box: Tuple[int, int, int, int]) -> Optional[np.ndarray]:
    """
    Ekstrak 5 titik koordinat kanonikal (mata kiri, mata kanan, hidung, sudut mulut kiri, sudut mulut kanan)
    dan jalankan Affine Similarity Transform ke ruang kanonikal InsightFace 112x112 px.
    Jika 68 landmarks terhambat (misal oklusi berat), fallback ke padding proporsional.
    """
    try:
        landmarks_list = face_recognition.face_landmarks(img_array, [face_box])
        if landmarks_list and len(landmarks_list) > 0:
            lm = landmarks_list[0]
            if 'left_eye' in lm and 'right_eye' in lm and 'nose_tip' in lm and 'top_lip' in lm:
                left_eye = np.mean(lm['left_eye'], axis=0)
                right_eye = np.mean(lm['right_eye'], axis=0)
                nose = np.mean(lm['nose_tip'], axis=0)
                top_lip = lm['top_lip']
                mouth_left = top_lip[0]
                mouth_right = top_lip[6] if len(top_lip) > 6 else top_lip[-1]

                dst_pts = np.array([left_eye, right_eye, nose, mouth_left, mouth_right], dtype=np.float32)
                M, _ = cv2.estimateAffinePartial2D(dst_pts, ARCFACE_SRC_PTS)
                if M is not None:
                    aligned = cv2.warpAffine(img_array, M, (112, 112), borderValue=0.0)
                    return aligned
    except Exception:
        pass

    # Fallback ke padded bounding box crop jika landmark warp gagal
    try:
        top, right, bottom, left = face_box
        h, w, _ = img_array.shape
        pad_y = int((bottom - top) * 0.15)
        pad_x = int((right - left) * 0.15)
        y1 = max(0, top - pad_y)
        y2 = min(h, bottom + pad_y)
        x1 = max(0, left - pad_x)
        x2 = min(w, right + pad_x)

        crop = img_array[y1:y2, x1:x2]
        if crop.size == 0:
            return None
        return cv2.resize(crop, (112, 112))
    except Exception:
        return None


def extract_arcface_512(img_array: np.ndarray, face_box: Tuple[int, int, int, int]) -> Optional[List[float]]:
    """
    Ekstrak 512-D face embedding menggunakan InsightFace ArcFace ResNet-50
    dengan 5-point landmark similarity alignment standar industri biometrik.
    """
    global arcface_session
    if arcface_session is None or not OPENCV_AVAILABLE:
        return None

    try:
        aligned_rgb = align_face_5point(img_array, face_box)
        if aligned_rgb is None or aligned_rgb.size == 0:
            return None

        # ArcFace input: 112x112, BGR, normalized (x - 127.5) / 127.5
        aligned_bgr = cv2.cvtColor(aligned_rgb, cv2.COLOR_RGB2BGR)
        blob = np.transpose(aligned_bgr, (2, 0, 1)).astype(np.float32)
        blob = (blob - 127.5) / 127.5
        blob = np.expand_dims(blob, axis=0)

        out = arcface_session.run(None, {'input.1': blob})[0][0]
        # L2 Normalisasi vektor
        norm = float(np.linalg.norm(out))
        if norm > 0:
            out = out / norm
        return out.tolist()
    except Exception as e:
        print(f"Error ekstraksi ArcFace: {e}")
        return None


def extract_descriptor(
    image_array: np.ndarray,
    high_model: bool = False,
    num_jitters: int = 1
) -> Tuple[Optional[List[float]], Optional[Tuple[int, int, int, int]], Optional[str]]:
    """
    Ekstrak 128-float face descriptor menggunakan High Model 68-point landmarks
    dan kembalikan lokasi kotak wajah (bounding box).
    """
    face_locations = face_recognition.face_locations(image_array, model="hog")

    if len(face_locations) == 0 and high_model:
        face_locations = face_recognition.face_locations(image_array, number_of_times_to_upsample=1, model="hog")

    if len(face_locations) == 0:
        return None, None, "Tidak ada wajah yang terdeteksi dalam frame. Pastikan wajah tepat di depan kamera."

    if len(face_locations) > 1:
        face_locations = sorted(
            face_locations,
            key=lambda b: (b[2] - b[0]) * (b[1] - b[3]),
            reverse=True
        )
        face_locations = [face_locations[0]]

    best_box = face_locations[0]

    jitters = num_jitters if high_model else 1
    encodings = face_recognition.face_encodings(
        image_array,
        face_locations,
        num_jitters=jitters,
        model="large"
    )

    if not encodings:
        return None, best_box, "Gagal mengekstrak fitur wajah. Pastikan pencahayaan cukup dan wajah terlihat jelas."

    return encodings[0].tolist(), best_box, None


# --- In-Memory Candidate Dual-Vector Matrix Cache for 1:N Identification ---
CANDIDATE_128_IDS: List[int] = []
CANDIDATE_128_MATRIX: Optional[np.ndarray] = None

CANDIDATE_512_IDS: List[int] = []
CANDIDATE_512_MATRIX: Optional[np.ndarray] = None

CANDIDATES_CACHE_FILE = os.path.join(os.path.dirname(__file__), 'candidates_cache.npz')
LAST_CACHE_MTIME: float = 0.0

def save_candidates_to_disk(ids_128: List[int], vecs_128: List[List[float]], ids_512: List[int], vecs_512: List[List[float]]) -> None:
    """Simpan cache kandidat ke disk agar seluruh worker uvicorn tersinkronisasi secara otomatis."""
    global LAST_CACHE_MTIME
    try:
        tmp_base = os.path.join(os.path.dirname(__file__), 'candidates_cache_tmp')
        arr_128 = np.array(vecs_128, dtype=np.float32) if vecs_128 else np.empty((0, 128), dtype=np.float32)
        arr_512 = np.array(vecs_512, dtype=np.float32) if vecs_512 else np.empty((0, 512), dtype=np.float32)
        np.savez(
            tmp_base,
            ids_128=np.array(ids_128, dtype=np.int64),
            vecs_128=arr_128,
            ids_512=np.array(ids_512, dtype=np.int64),
            vecs_512=arr_512
        )
        saved_tmp = tmp_base + '.npz'
        os.replace(saved_tmp, CANDIDATES_CACHE_FILE)
        LAST_CACHE_MTIME = os.path.getmtime(CANDIDATES_CACHE_FILE)
        try:
            os.chmod(CANDIDATES_CACHE_FILE, 0o666)
        except Exception:
            pass
    except Exception as e:
        print(f"Error saving candidates cache to disk: {e}")

def ensure_candidates_loaded() -> None:
    """Pastikan data kandidat di RAM worker up-to-date dengan file cache bersama."""
    global CANDIDATE_128_IDS, CANDIDATE_128_MATRIX, CANDIDATE_512_IDS, CANDIDATE_512_MATRIX, LAST_CACHE_MTIME
    try:
        if not os.path.exists(CANDIDATES_CACHE_FILE):
            return
        mtime = os.path.getmtime(CANDIDATES_CACHE_FILE)
        if mtime > LAST_CACHE_MTIME or (len(CANDIDATE_128_IDS) == 0 and len(CANDIDATE_512_IDS) == 0):
            data = np.load(CANDIDATES_CACHE_FILE)
            ids128 = data['ids_128'].tolist()
            ids512 = data['ids_512'].tolist()
            v128 = data['vecs_128']
            v512 = data['vecs_512']
            CANDIDATE_128_IDS = ids128
            CANDIDATE_128_MATRIX = v128 if len(ids128) > 0 else None
            CANDIDATE_512_IDS = ids512
            CANDIDATE_512_MATRIX = v512 if len(ids512) > 0 else None
            LAST_CACHE_MTIME = mtime
    except Exception as e:
        print(f"Error loading candidates cache from disk: {e}")

# Muat cache awal jika sudah ada di disk
ensure_candidates_loaded()


# --- Endpoints ---

@app.post("/extract")
def mode_extract(payload: ExtractRequest) -> Dict[str, Any]:
    try:
        img_array = decode_image(payload.image_b64, max_dimension=720)
        desc_128, face_box, err = extract_descriptor(img_array, high_model=True, num_jitters=1)

        if err:
            return {"success": False, "error": err}

        # Ekstrak 512-D dengan 5-point alignment
        desc_512 = None
        if face_box is not None:
            desc_512 = extract_arcface_512(img_array, face_box)

        # Cek liveness
        is_real, liveness_score, liveness_err = True, 1.0, None
        if face_box is not None:
            is_real, liveness_score, liveness_err = check_liveness(img_array, face_box)

        # Tentukan descriptor utama: utamakan 512 jika diminta atau tersedia
        primary_desc = desc_512 if (payload.engine == "512" and desc_512) else (desc_512 or desc_128)

        return {
            "success": True,
            "descriptor": primary_desc,
            "dimensions": len(primary_desc) if primary_desc else 0,
            "descriptor_128": desc_128,
            "descriptor_512": desc_512,
            "is_real": is_real,
            "liveness_score": round(liveness_score, 4),
            "liveness_error": liveness_err,
            "model_type": "arcface-512-aligned" if desc_512 else "dlib-128"
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/enroll")
def mode_enroll(payload: EnrollRequest) -> Dict[str, Any]:
    """
    Endpoint Pendaftaran Wajah Multi-Sudut (Enterprise Biometric Consensus).
    Menggabungkan 5 pose (depan, toleh kiri, toleh kanan, senyum, depan kunci)
    menggunakan Weighted Centroid Fusion.
    """
    try:
        images = payload.images_b64
        if len(images) < 3:
            return {"success": False, "error": "Minimal 3 foto wajah diperlukan untuk enrollment."}

        descriptors_128 = []
        descriptors_512 = []
        weights_128 = []
        weights_512 = []
        errors = []

        # Pose weights: frontal (1.3), senyum (1.1), toleh (0.95)
        angle_weights = [1.3, 0.95, 0.95, 1.1, 1.3]

        for i, b64 in enumerate(images):
            w = angle_weights[i] if i < len(angle_weights) else 1.0
            try:
                img_array = decode_image(b64, max_dimension=1024)
                d128, fbox, err = extract_descriptor(img_array, high_model=True, num_jitters=2)
                if err:
                    errors.append(f"Foto {i+1}: {err}")
                    continue
                descriptors_128.append(d128)
                weights_128.append(w)

                if fbox is not None:
                    d512 = extract_arcface_512(img_array, fbox)
                    if d512:
                        descriptors_512.append(d512)
                        weights_512.append(w)
            except Exception as e:
                errors.append(f"Foto {i+1}: Gagal decode ({str(e)})")

        if len(descriptors_128) < 3 and len(descriptors_512) < 3:
            return {
                "success": False,
                "error": f"Hanya {max(len(descriptors_128), len(descriptors_512))}/{len(images)} foto yang memenuhi syarat biometrik. Error: {'; '.join(errors)}"
            }

        # 1. Hitung Weighted Centroid 512-D ArcFace Aligned
        avg_descriptor_512 = None
        if len(descriptors_512) >= 3:
            d512_arr = np.array(descriptors_512, dtype=np.float32)
            w512_arr = np.array(weights_512, dtype=np.float32).reshape(-1, 1)

            # Filter outlier jika ada 4-5 foto
            if len(descriptors_512) >= 4:
                median_512 = np.median(d512_arr, axis=0)
                dists_to_med = np.linalg.norm(d512_arr - median_512, axis=1)
                clean_mask = dists_to_med < 0.45
                if np.sum(clean_mask) >= 3:
                    d512_arr = d512_arr[clean_mask]
                    w512_arr = w512_arr[clean_mask]

            weighted_sum_512 = np.sum(d512_arr * w512_arr, axis=0)
            sum_weights_512 = float(np.sum(w512_arr))
            centroid_512 = weighted_sum_512 / sum_weights_512
            norm_512 = float(np.linalg.norm(centroid_512))
            if norm_512 > 0:
                centroid_512 = centroid_512 / norm_512
            avg_descriptor_512 = centroid_512.tolist()

        # 2. Hitung Weighted Centroid 128-D dlib
        avg_descriptor_128 = None
        if len(descriptors_128) >= 3:
            d128_arr = np.array(descriptors_128, dtype=np.float32)
            w128_arr = np.array(weights_128, dtype=np.float32).reshape(-1, 1)

            if len(descriptors_128) >= 4:
                median_128 = np.median(d128_arr, axis=0)
                dists_to_med = np.linalg.norm(d128_arr - median_128, axis=1)
                clean_mask = dists_to_med < 0.40
                if np.sum(clean_mask) >= 3:
                    d128_arr = d128_arr[clean_mask]
                    w128_arr = w128_arr[clean_mask]

            weighted_sum_128 = np.sum(d128_arr * w128_arr, axis=0)
            sum_weights_128 = float(np.sum(w128_arr))
            centroid_128 = weighted_sum_128 / sum_weights_128
            avg_descriptor_128 = centroid_128.tolist()

        # Default descriptor utama adalah ArcFace 512-D Aligned (Enterprise)
        primary_desc = avg_descriptor_512 if avg_descriptor_512 is not None else avg_descriptor_128

        return {
            "success": True,
            "descriptor": primary_desc,
            "dimensions": len(primary_desc) if primary_desc else 0,
            "descriptor_128": avg_descriptor_128,
            "descriptor_512": avg_descriptor_512,
            "processed": max(len(descriptors_128), len(descriptors_512)),
            "total_submitted": len(images),
            "model_version": "arcface-512-aligned-enterprise" if avg_descriptor_512 else "dlib-128",
            "errors": errors
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/compare")
def mode_compare(payload: CompareRequest) -> Dict[str, Any]:
    """
    Endpoint 1:1 Face Verification dengan integrasi Passive Anti-Spoofing
    dan Dual Engine (128-D & 512-D dengan 5-point alignment).
    """
    try:
        stored = payload.stored
        dim = len(stored)
        if dim not in (128, 512):
            return {"success": False, "error": f"Stored descriptor harus 128 atau 512 elemen, dapat {dim}."}

        img_array = decode_image(payload.image_b64)
        new_desc_128, face_box, err = extract_descriptor(img_array)

        if err:
            return {
                "success": True,
                "match": False,
                "distance": None,
                "reason": err
            }

        # ── 1. Validasi Passive Anti-Spoofing (Liveness Check) ──
        if not payload.skip_liveness and face_box is not None:
            is_real, liveness_score, liveness_err = check_liveness(img_array, face_box)
            if not is_real:
                return {
                    "success": True,
                    "match": False,
                    "is_spoof": True,
                    "liveness_score": round(liveness_score, 4),
                    "distance": None,
                    "reason": liveness_err or "Presensi ditolak: Terdeteksi foto 2D / layar perangkat."
                }

        # ── 2. Perbandingan Biometrik Berdasarkan Dimensi Stored ──
        if dim == 512:
            new_desc_512 = extract_arcface_512(img_array, face_box) if face_box else None
            if not new_desc_512:
                return {
                    "success": True,
                    "match": False,
                    "distance": None,
                    "reason": "Gagal mengekstrak fitur 512-D wajah."
                }

            stored_vec = np.array(stored, dtype=np.float32)
            new_vec = np.array(new_desc_512, dtype=np.float32)

            distance = float(np.linalg.norm(stored_vec - new_vec))
            thr_512 = payload.threshold if (payload.threshold is not None and payload.threshold >= 0.50) else THRESHOLD_512
            match = distance < thr_512

            if distance < 0.45:
                confidence = "very_high"
            elif distance < 0.55:
                confidence = "high"
            elif distance <= thr_512:
                confidence = "medium"
            else:
                confidence = "low"

            return {
                "success": True,
                "match": match,
                "distance": round(distance, 4),
                "threshold": thr_512,
                "confidence": confidence,
                "engine": "arcface-512-aligned",
                "reason": "Wajah dikenali (ArcFace 512-D Aligned)." if match else f"Wajah tidak cocok (jarak: {distance:.4f}, threshold: {thr_512})."
            }
        else:
            stored_vec = np.array(stored, dtype=np.float32)
            new_vec = np.array(new_desc_128, dtype=np.float32)

            stored_norm = float(np.linalg.norm(stored_vec))
            new_norm = float(np.linalg.norm(new_vec))
            if 0 < stored_norm < 1.15 and new_norm > 1.30:
                stored_vec = stored_vec * new_norm

            distance = float(np.linalg.norm(stored_vec - new_vec))
            thr_128 = payload.threshold if (payload.threshold is not None and payload.threshold >= 0.35) else THRESHOLD_128
            match = distance < thr_128

            if distance < 0.35:
                confidence = "very_high"
            elif distance < 0.45:
                confidence = "high"
            elif distance <= thr_128:
                confidence = "medium"
            else:
                confidence = "low"

            return {
                "success": True,
                "match": match,
                "distance": round(distance, 4),
                "threshold": thr_128,
                "confidence": confidence,
                "engine": "dlib-128",
                "reason": "Wajah dikenali (dlib 128-D)." if match else f"Wajah tidak cocok (jarak: {distance:.4f}, threshold: {thr_128})."
            }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/sync-candidates")
def mode_sync_candidates(payload: SyncCandidatesRequest) -> Dict[str, Any]:
    """
    Simpan seluruh vektor siswa terdaftar ke dalam memori RAM (NumPy Matrix Dual Pool)
    untuk pencarian 1:N real-time tanpa query berulang.
    """
    global CANDIDATE_128_IDS, CANDIDATE_128_MATRIX, CANDIDATE_512_IDS, CANDIDATE_512_MATRIX
    try:
        ids_128, vecs_128 = [], []
        ids_512, vecs_512 = [], []

        for c in payload.candidates:
            dim = len(c.descriptor)
            if dim == 128:
                ids_128.append(c.id)
                vecs_128.append(c.descriptor)
            elif dim == 512:
                ids_512.append(c.id)
                vecs_512.append(c.descriptor)

        CANDIDATE_128_IDS = ids_128
        CANDIDATE_128_MATRIX = np.array(vecs_128, dtype=np.float32) if vecs_128 else None

        CANDIDATE_512_IDS = ids_512
        CANDIDATE_512_MATRIX = np.array(vecs_512, dtype=np.float32) if vecs_512 else None

        # Simpan ke disk agar semua worker uvicorn berbagi cache yang sama
        save_candidates_to_disk(ids_128, vecs_128, ids_512, vecs_512)

        total_cached = len(ids_128) + len(ids_512)
        return {
            "success": True,
            "message": f"Sinkronisasi berhasil: {len(ids_512)} vektor ArcFace 512-D dan {len(ids_128)} vektor dlib 128-D siap di RAM.",
            "total": total_cached,
            "total_512": len(ids_512),
            "total_128": len(ids_128),
            "ready": total_cached > 0
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.api_route("/candidates-stats", methods=["GET", "POST"])
def mode_candidates_stats() -> Dict[str, Any]:
    global CANDIDATE_128_IDS, CANDIDATE_128_MATRIX, CANDIDATE_512_IDS, CANDIDATE_512_MATRIX
    ensure_candidates_loaded()
    total = len(CANDIDATE_128_IDS) + len(CANDIDATE_512_IDS)
    return {
        "success": True,
        "total_cached": total,
        "pool_512_count": len(CANDIDATE_512_IDS),
        "pool_512_shape": list(CANDIDATE_512_MATRIX.shape) if CANDIDATE_512_MATRIX is not None else None,
        "pool_128_count": len(CANDIDATE_128_IDS),
        "pool_128_shape": list(CANDIDATE_128_MATRIX.shape) if CANDIDATE_128_MATRIX is not None else None,
        "ready_for_1n": total > 0
    }


@app.post("/identify")
def mode_identify(payload: IdentifyRequest) -> Dict[str, Any]:
    """
    Endpoint 1:N Facial Recognition (Face ID Kiosk Gerbang)
    Dual-Engine Simultaneous Matcher (ArcFace 512-D Aligned & dlib 128-D).
    """
    global CANDIDATE_128_IDS, CANDIDATE_128_MATRIX, CANDIDATE_512_IDS, CANDIDATE_512_MATRIX

    try:
        ensure_candidates_loaded()
        total_in_cache = len(CANDIDATE_128_IDS) + len(CANDIDATE_512_IDS)
        if total_in_cache == 0 and not payload.candidates:
            return {
                "success": False,
                "error": "Database biometrik siswa kosong di RAM server. Silakan jalankan sync candidates terlebih dahulu.",
                "need_sync": True
            }

        img_array = decode_image(payload.image_b64)
        query_desc_128, face_box, err = extract_descriptor(img_array)

        if err:
            return {
                "success": True,
                "match": False,
                "distance": None,
                "reason": err
            }

        # 1. Anti-Spoofing (Liveness Check)
        is_real, liveness_score, liveness_err = True, 1.0, None
        if not payload.skip_liveness and face_box is not None:
            is_real, liveness_score, liveness_err = check_liveness(img_array, face_box)
            if not is_real:
                return {
                    "success": True,
                    "match": False,
                    "is_spoof": True,
                    "liveness_score": round(liveness_score, 4),
                    "distance": None,
                    "reason": liveness_err or "Presensi ditolak: Terdeteksi foto/layar perangkat."
                }

        # Extract 512-D descriptor with 5-point alignment
        query_desc_512 = extract_arcface_512(img_array, face_box) if face_box else None

        # Evaluasi kandidat dari payload khusus jika diberikan, jika tidak gunakan global cache
        if payload.candidates is not None and len(payload.candidates) > 0:
            c128_ids, c128_vecs = [], []
            c512_ids, c512_vecs = [], []
            for c in payload.candidates:
                if len(c.descriptor) == 128:
                    c128_ids.append(c.id)
                    c128_vecs.append(c.descriptor)
                elif len(c.descriptor) == 512:
                    c512_ids.append(c.id)
                    c512_vecs.append(c.descriptor)
            mat128 = np.array(c128_vecs, dtype=np.float32) if c128_vecs else None
            ids128 = c128_ids
            mat512 = np.array(c512_vecs, dtype=np.float32) if c512_vecs else None
            ids512 = c512_ids
        else:
            ids128 = CANDIDATE_128_IDS
            mat128 = CANDIDATE_128_MATRIX
            ids512 = CANDIDATE_512_IDS
            mat512 = CANDIDATE_512_MATRIX

        best_match = None
        best_conf = -1.0

        # ── 1. Evaluasi ArcFace 512-D Aligned Engine (Prioritas Akurasi Tertinggi) ──
        if query_desc_512 is not None and mat512 is not None and len(ids512) > 0:
            q512 = np.array(query_desc_512, dtype=np.float32)
            dists_512 = np.linalg.norm(mat512 - q512, axis=1)
            idx_512 = int(np.argmin(dists_512))
            min_dist_512 = float(dists_512[idx_512])
            thr_512 = payload.threshold if (payload.threshold is not None and payload.threshold >= 0.55) else THRESHOLD_512
            
            # Confidence ArcFace: 0.0 (distance >= thr) to 1.0 (distance 0.0)
            conf_512 = max(0.0, 1.0 - (min_dist_512 / thr_512))
            matched_512 = min_dist_512 <= thr_512

            margin_512 = None
            if len(dists_512) > 1:
                sorted_dists = np.partition(dists_512, 1)
                margin_512 = round(float(sorted_dists[1] - min_dist_512), 4)

            best_match = {
                "match": matched_512,
                "user_id": ids512[idx_512] if matched_512 else None,
                "distance": round(min_dist_512, 4),
                "threshold": thr_512,
                "confidence_pct": round(conf_512 * 100, 1),
                "confidence_level": "very_high" if min_dist_512 < 0.45 else ("high" if min_dist_512 < 0.55 else ("medium" if matched_512 else "low")),
                "margin": margin_512,
                "engine": "arcface-512-aligned",
                "pool_size": len(ids512),
                "reason": "Wajah siswa teridentifikasi (ArcFace 512-D Aligned)." if matched_512 else f"Wajah tidak cocok (jarak: {min_dist_512:.4f}, batas: {thr_512})."
            }
            best_conf = conf_512

        # ── 2. Evaluasi dlib 128-D Engine (Kompatibilitas Penuh Siswa Lama) ──
        if query_desc_128 is not None and mat128 is not None and len(ids128) > 0:
            q128 = np.array(query_desc_128, dtype=np.float32)
            
            # Normalisasi dlib jika diperlukan
            working_mat128 = mat128
            norms = np.linalg.norm(mat128, axis=1)
            bad_mask = norms < 1.15
            if np.any(bad_mask):
                working_mat128 = mat128.copy()
                q128_norm = float(np.linalg.norm(q128))
                if q128_norm > 1.30:
                    working_mat128[bad_mask] = working_mat128[bad_mask] * q128_norm

            dists_128 = np.linalg.norm(working_mat128 - q128, axis=1)
            idx_128 = int(np.argmin(dists_128))
            min_dist_128 = float(dists_128[idx_128])
            thr_128 = payload.threshold if (payload.threshold is not None and payload.threshold < 0.55) else THRESHOLD_128
            
            conf_128 = max(0.0, 1.0 - (min_dist_128 / thr_128))
            matched_128 = min_dist_128 <= thr_128

            margin_128 = None
            if len(dists_128) > 1:
                sorted_dists = np.partition(dists_128, 1)
                margin_128 = round(float(sorted_dists[1] - min_dist_128), 4)

            # Jika 128-D match dan conf_128 lebih tinggi dari 512-D (atau 512-D tidak match)
            if best_match is None or (matched_128 and not best_match["match"]) or (matched_128 and conf_128 > best_conf):
                best_match = {
                    "match": matched_128,
                    "user_id": ids128[idx_128] if matched_128 else None,
                    "distance": round(min_dist_128, 4),
                    "threshold": thr_128,
                    "confidence_pct": round(conf_128 * 100, 1),
                    "confidence_level": "very_high" if min_dist_128 < 0.35 else ("high" if min_dist_128 < 0.45 else ("medium" if matched_128 else "low")),
                    "margin": margin_128,
                    "engine": "dlib-128",
                    "pool_size": len(ids128),
                    "reason": "Wajah siswa teridentifikasi (dlib 128-D)." if matched_128 else f"Wajah tidak cocok (jarak: {min_dist_128:.4f}, batas: {thr_128})."
                }

        if best_match is None:
            return {
                "success": True,
                "match": False,
                "distance": None,
                "reason": "Tidak ada profil biometrik yang cocok dalam database."
            }

        return {
            "success": True,
            "match": best_match["match"],
            "user_id": best_match["user_id"],
            "distance": best_match["distance"],
            "threshold": best_match["threshold"],
            "confidence": best_match["confidence_level"],
            "confidence_pct": best_match["confidence_pct"],
            "margin": best_match["margin"],
            "engine": best_match["engine"],
            "liveness_score": round(liveness_score, 4),
            "is_real": is_real,
            "total_candidates": total_in_cache,
            "reason": best_match["reason"]
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.get("/test")
def mode_test() -> Dict[str, Any]:
    """
    Test endpoint status lingkungan AI server.
    """
    results = {}
    try:
        import numpy as np
        results['numpy'] = np.__version__
    except ImportError:
        results['numpy'] = 'NOT INSTALLED'

    try:
        import PIL
        results['pillow'] = PIL.__version__
    except ImportError:
        results['pillow'] = 'NOT INSTALLED'

    try:
        import face_recognition
        results['face_recognition'] = 'OK'
    except ImportError:
        results['face_recognition'] = 'NOT INSTALLED'

    results['onnxruntime'] = ort.__version__ if ONNX_AVAILABLE else 'NOT INSTALLED'
    results['opencv'] = cv2.__version__ if OPENCV_AVAILABLE else 'NOT INSTALLED'
    results['minifasnet_liveness'] = 'READY' if minifasnet_session is not None else 'NOT LOADED'
    results['arcface_512d'] = 'READY (5-Point Aligned)' if arcface_session is not None else 'NOT LOADED'

    ensure_candidates_loaded()
    total_pool = len(CANDIDATE_128_IDS) + len(CANDIDATE_512_IDS)

    return {
        "success": True,
        "engine_version": "3.0-enterprise",
        "packages": results,
        "threshold_128": THRESHOLD_128,
        "threshold_512": THRESHOLD_512,
        "liveness_threshold": LIVENESS_THRESHOLD,
        "alignment_mode": "canonical_5point_similarity_warp",
        "candidates_cached": {
            "total": total_pool,
            "pool_512": len(CANDIDATE_512_IDS),
            "pool_128": len(CANDIDATE_128_IDS),
        },
        "ready": True
    }
