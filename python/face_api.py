#!/usr/bin/env python3
"""
face_api.py — High-Performance Dual-Engine Face Recognition & Anti-Spoofing API
Versi 2.0 untuk Sistem Presensi SMKN 1 Beringin

Fitur Unggulan:
1. Poin 1: Upgrade Mesin Face Recognition (InsightFace ArcFace 512-D + dlib 128-D Dual Engine).
2. Poin 2: Passive Anti-Spoofing / Liveness Detection (MiniFASNetV2) pencegah foto 2D & layar HP.
3. Arsitektur Kompatibel Penuh: Siswa dengan vektor 128-D lama tetap berjalan 100% normal tanpa gangguan.
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

app = FastAPI(title="Face Recognition & Anti-Spoofing API", description="Sistem Presensi SMKN 1 Beringin")

# ── Ambang Batas Pengenalan (Thresholds) ──
THRESHOLD_128 = 0.50       # dlib 128-D euclidean distance
THRESHOLD_512 = 0.65       # ArcFace normalized euclidean distance (ekuivalen cosine similarity > 0.78)
LIVENESS_THRESHOLD = 0.60  # MiniFASNet real face probability threshold

# ── Direktori & Model Loader ──
MODEL_DIR = os.path.join(os.path.dirname(__file__), 'models')
MINIFASNET_PATH = os.path.join(MODEL_DIR, '2.7_80x80_MiniFASNetV2.onnx')
ARCFACE_PATH = os.path.join(MODEL_DIR, 'w600k_r50.onnx')

minifasnet_session = None
arcface_session = None

if ONNX_AVAILABLE:
    # 1. Inisialisasi MiniFASNet Anti-Spoofing
    if os.path.exists(MINIFASNET_PATH):
        try:
            minifasnet_session = ort.InferenceSession(
                MINIFASNET_PATH,
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
                providers=['CPUExecutionProvider']
            )
            print(f"✅ ArcFace 512-D Engine loaded dari: {ARCFACE_PATH}")
        except Exception as e:
            print(f"⚠️ Gagal load ArcFace ONNX: {e}")


# --- Pydantic Models untuk Validasi Input ---

class ExtractRequest(BaseModel):
    image_b64: str
    engine: Optional[str] = "128" # "128" atau "512"

class EnrollRequest(BaseModel):
    images_b64: List[str]
    engine: Optional[str] = "128"

class CompareRequest(BaseModel):
    stored: List[float]
    image_b64: str
    skip_liveness: Optional[bool] = False

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

def decode_image(b64_string: str, max_dimension: int = 720) -> np.ndarray:
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
    Evaluasi Passive Anti-Spoofing menggunakan MiniFASNetV2:
    Membedakan wajah manusia 3D hidup vs foto cetak / layar HP/tablet (2D replay).
    face_box: (top, right, bottom, left)
    """
    global minifasnet_session
    if minifasnet_session is None or not OPENCV_AVAILABLE:
        # Fallback aman jika model tidak aktif: tidak memblokir user
        return True, 1.0, None

    try:
        top, right, bottom, left = face_box
        h, w, _ = img_array.shape
        box_w = right - left
        box_h = bottom - top

        if box_w <= 10 or box_h <= 10:
            return True, 1.0, None

        # Scale 2.7x untuk menangkap konteks layar / batas kertas di sekitar wajah
        scale = 2.7
        cx = left + box_w / 2.0
        cy = top + box_h / 2.0
        crop_w = box_w * scale
        crop_h = box_h * scale

        x1 = max(0, int(cx - crop_w / 2.0))
        y1 = max(0, int(cy - crop_h / 2.0))
        x2 = min(w, int(cx + crop_w / 2.0))
        y2 = min(h, int(cy + crop_h / 2.0))

        crop = img_array[y1:y2, x1:x2]
        if crop.size == 0 or crop.shape[0] < 20 or crop.shape[1] < 20:
            return True, 1.0, None

        # Resize ke 80x80 BGR sesuai spesifikasi model MiniFASNet
        crop_bgr = cv2.cvtColor(crop, cv2.COLOR_RGB2BGR)
        resized = cv2.resize(crop_bgr, (80, 80))
        blob = np.transpose(resized, (2, 0, 1)).astype(np.float32)
        blob = np.expand_dims(blob, axis=0)

        out = minifasnet_session.run(None, {'input': blob})[0][0]
        # Softmax probabilitas
        exp_out = np.exp(out - np.max(out))
        probs = exp_out / np.sum(exp_out)
        real_score = float(probs[1]) # Kelas index 1 = Real human face

        # Jika probabilitas wajah asli < threshold, tolak sebagai spoof
        if real_score < LIVENESS_THRESHOLD:
            return False, real_score, "Presensi ditolak: Terdeteksi foto 2D atau layar perangkat (Anti-Spoofing). Wajib menghadap langsung dengan wajah asli di depan kamera."

        return True, real_score, None
    except Exception as e:
        print(f"Peringatan Anti-Spoofing: {e}")
        return True, 1.0, None


def extract_arcface_512(img_array: np.ndarray, face_box: Tuple[int, int, int, int]) -> Optional[List[float]]:
    """
    Ekstrak 512-D face embedding menggunakan InsightFace ArcFace ResNet-50.
    """
    global arcface_session
    if arcface_session is None or not OPENCV_AVAILABLE:
        return None

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

        # ArcFace input: 112x112, BGR, normalized (x - 127.5) / 127.5
        crop_bgr = cv2.cvtColor(crop, cv2.COLOR_RGB2BGR)
        resized = cv2.resize(crop_bgr, (112, 112))
        blob = np.transpose(resized, (2, 0, 1)).astype(np.float32)
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


# --- Endpoints ---

@app.post("/extract")
def mode_extract(payload: ExtractRequest) -> Dict[str, Any]:
    try:
        img_array = decode_image(payload.image_b64, max_dimension=720)
        desc_128, face_box, err = extract_descriptor(img_array, high_model=True, num_jitters=1)

        if err:
            return {"success": False, "error": err}

        # Ekstrak 512-D jika engine diminta atau tersedia
        desc_512 = None
        if face_box is not None:
            desc_512 = extract_arcface_512(img_array, face_box)

        # Cek liveness
        is_real, liveness_score, liveness_err = True, 1.0, None
        if face_box is not None:
            is_real, liveness_score, liveness_err = check_liveness(img_array, face_box)

        # Tentukan descriptor utama yang dikembalikan
        primary_desc = desc_512 if (payload.engine == "512" and desc_512) else desc_128

        return {
            "success": True,
            "descriptor": primary_desc,
            "dimensions": len(primary_desc) if primary_desc else 0,
            "descriptor_128": desc_128,
            "descriptor_512": desc_512,
            "is_real": is_real,
            "liveness_score": round(liveness_score, 4),
            "liveness_error": liveness_err,
            "model_type": "arcface-512" if (payload.engine == "512" and desc_512) else "dlib-128"
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/enroll")
def mode_enroll(payload: EnrollRequest) -> Dict[str, Any]:
    """
    Endpoint Pendaftaran Wajah Multi-Sudut (Biometrik Konsensus).
    """
    try:
        images = payload.images_b64
        if len(images) < 3:
            return {"success": False, "error": "Minimal 3 foto wajah diperlukan untuk enrollment."}

        descriptors_128 = []
        descriptors_512 = []
        errors = []

        for i, b64 in enumerate(images):
            try:
                img_array = decode_image(b64, max_dimension=1024)
                d128, fbox, err = extract_descriptor(img_array, high_model=True, num_jitters=2)
                if err:
                    errors.append(f"Foto {i+1}: {err}")
                    continue
                descriptors_128.append(d128)

                if fbox is not None:
                    d512 = extract_arcface_512(img_array, fbox)
                    if d512:
                        descriptors_512.append(d512)
            except Exception as e:
                errors.append(f"Foto {i+1}: Gagal decode ({str(e)})")

        if len(descriptors_128) < 3:
            return {
                "success": False,
                "error": f"Hanya {len(descriptors_128)}/{len(images)} foto yang memenuhi syarat biometrik. Error: {'; '.join(errors)}"
            }

        desc_array = np.array(descriptors_128, dtype=np.float32)

        # Filtering outlier jika ada 4-5 sampel
        if len(descriptors_128) >= 4:
            median_vec = np.median(desc_array, axis=0)
            dists_to_median = np.linalg.norm(desc_array - median_vec, axis=1)
            clean_mask = dists_to_median < 0.40
            if np.sum(clean_mask) >= 3:
                desc_array = desc_array[clean_mask]

        avg_descriptor_128 = np.mean(desc_array, axis=0).tolist()

        # Konsensus 512-D jika tersedia
        avg_descriptor_512 = None
        if len(descriptors_512) >= 3:
            d512_arr = np.array(descriptors_512, dtype=np.float32)
            mean_512 = np.mean(d512_arr, axis=0)
            norm_512 = float(np.linalg.norm(mean_512))
            if norm_512 > 0:
                mean_512 = mean_512 / norm_512
            avg_descriptor_512 = mean_512.tolist()

        primary_desc = avg_descriptor_512 if (payload.engine == "512" and avg_descriptor_512) else avg_descriptor_128

        return {
            "success": True,
            "descriptor": primary_desc,
            "descriptor_128": avg_descriptor_128,
            "descriptor_512": avg_descriptor_512,
            "processed": len(desc_array),
            "total_submitted": len(images),
            "model_version": "arcface-512" if (payload.engine == "512" and avg_descriptor_512) else "dlib-128",
            "errors": errors
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/compare")
def mode_compare(payload: CompareRequest) -> Dict[str, Any]:
    """
    Endpoint 1:1 Face Verification dengan integrasi Passive Anti-Spoofing
    dan Dual Engine (128-D & 512-D).
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
            # Mode ArcFace 512-D
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
            match = distance < THRESHOLD_512

            if distance < 0.45:
                confidence = "very_high"
            elif distance < 0.55:
                confidence = "high"
            elif distance <= THRESHOLD_512:
                confidence = "medium"
            else:
                confidence = "low"

            return {
                "success": True,
                "match": match,
                "distance": round(distance, 4),
                "threshold": THRESHOLD_512,
                "confidence": confidence,
                "engine": "arcface-512",
                "reason": "Wajah dikenali (ArcFace 512-D)." if match else f"Wajah tidak cocok (jarak: {distance:.4f}, threshold: {THRESHOLD_512})."
            }
        else:
            # Mode dlib 128-D (Kompatibilitas 100% untuk siswa terdaftar)
            stored_vec = np.array(stored, dtype=np.float32)
            new_vec = np.array(new_desc_128, dtype=np.float32)

            stored_norm = float(np.linalg.norm(stored_vec))
            new_norm = float(np.linalg.norm(new_vec))
            if 0 < stored_norm < 1.15 and new_norm > 1.30:
                stored_vec = stored_vec * new_norm

            distance = float(np.linalg.norm(stored_vec - new_vec))
            match = distance < THRESHOLD_128

            if distance < 0.35:
                confidence = "very_high"
            elif distance < 0.45:
                confidence = "high"
            elif distance <= THRESHOLD_128:
                confidence = "medium"
            else:
                confidence = "low"

            return {
                "success": True,
                "match": match,
                "distance": round(distance, 4),
                "threshold": THRESHOLD_128,
                "confidence": confidence,
                "engine": "dlib-128",
                "reason": "Wajah dikenali (dlib 128-D)." if match else f"Wajah tidak cocok (jarak: {distance:.4f}, threshold: {THRESHOLD_128})."
            }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


# --- In-Memory Candidate Vector Matrix Cache for 1:N Identification ---
CANDIDATE_IDS: List[int] = []
CANDIDATE_MATRIX: Optional[np.ndarray] = None


@app.post("/sync-candidates")
def mode_sync_candidates(payload: SyncCandidatesRequest) -> Dict[str, Any]:
    """
    Simpan seluruh vektor siswa terdaftar ke dalam memori RAM (NumPy Matrix)
    untuk pencarian 1:N real-time tanpa query berulang.
    """
    global CANDIDATE_IDS, CANDIDATE_MATRIX
    try:
        valid_ids = []
        valid_vectors = []
        for c in payload.candidates:
            if len(c.descriptor) in (128, 512):
                valid_ids.append(c.id)
                valid_vectors.append(c.descriptor)

        if valid_vectors:
            CANDIDATE_IDS = valid_ids
            CANDIDATE_MATRIX = np.array(valid_vectors, dtype=np.float32)
        else:
            CANDIDATE_IDS = []
            CANDIDATE_MATRIX = None

        return {
            "success": True,
            "message": f"Berhasil sinkronisasi {len(valid_ids)} kandidat wajah ke memori server.",
            "total": len(valid_ids),
            "dimensions": CANDIDATE_MATRIX.shape[1] if CANDIDATE_MATRIX is not None else 0
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.api_route("/candidates-stats", methods=["GET", "POST"])
def mode_candidates_stats() -> Dict[str, Any]:
    global CANDIDATE_IDS, CANDIDATE_MATRIX
    return {
        "success": True,
        "cached_count": len(CANDIDATE_IDS),
        "matrix_shape": list(CANDIDATE_MATRIX.shape) if CANDIDATE_MATRIX is not None else None,
        "ready_for_1n": CANDIDATE_MATRIX is not None and len(CANDIDATE_IDS) > 0
    }


@app.post("/identify")
def mode_identify(payload: IdentifyRequest) -> Dict[str, Any]:
    """
    Endpoint 1:N Facial Recognition (Face ID Kiosk Gerbang)
    dengan proteksi Passive Anti-Spoofing dan vektorisasi NumPy cepat.
    """
    global CANDIDATE_IDS, CANDIDATE_MATRIX

    try:
        ids = CANDIDATE_IDS
        matrix = CANDIDATE_MATRIX

        if payload.candidates is not None and len(payload.candidates) > 0:
            custom_ids = []
            custom_vectors = []
            for c in payload.candidates:
                if len(c.descriptor) in (128, 512):
                    custom_ids.append(c.id)
                    custom_vectors.append(c.descriptor)
            if custom_vectors:
                ids = custom_ids
                matrix = np.array(custom_vectors, dtype=np.float32)

        if matrix is None or len(ids) == 0:
            return {
                "success": False,
                "error": "Database wajah siswa kosong di RAM server. Silakan jalankan sync candidates terlebih dahulu.",
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

        # ── 1. Validasi Anti-Spoofing (Liveness Check) ──
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

        # ── 2. Cek Dimensi Database Matrix (128 vs 512) ──
        mat_dim = matrix.shape[1]
        if mat_dim == 512:
            query_desc = extract_arcface_512(img_array, face_box) if face_box else None
            default_thr = 0.60
        else:
            query_desc = query_desc_128
            default_thr = 0.45

        if not query_desc:
            return {
                "success": True,
                "match": False,
                "distance": None,
                "reason": f"Gagal mengekstrak query descriptor ({mat_dim}-D)."
            }

        query_vec = np.array(query_desc, dtype=np.float32)

        # Safeguard normalisasi dlib 128-D jika diperlukan
        if mat_dim == 128 and matrix.ndim == 2 and matrix.shape[0] > 0:
            norms = np.linalg.norm(matrix, axis=1)
            bad_mask = norms < 1.15
            if np.any(bad_mask):
                matrix = matrix.copy()
                query_norm = float(np.linalg.norm(query_vec))
                if query_norm > 1.30:
                    matrix[bad_mask] = matrix[bad_mask] * query_norm

        diff = matrix - query_vec
        distances = np.linalg.norm(diff, axis=1)

        best_idx = int(np.argmin(distances))
        best_dist = float(distances[best_idx])
        matched_id = ids[best_idx]

        eff_threshold = payload.threshold if payload.threshold is not None else default_thr
        match = best_dist <= eff_threshold

        margin = None
        if len(distances) > 1:
            sorted_distances = np.partition(distances, 1)
            margin = round(float(sorted_distances[1] - best_dist), 4)

        return {
            "success": True,
            "match": match,
            "user_id": matched_id if match else None,
            "distance": round(best_dist, 4),
            "threshold": eff_threshold,
            "confidence": "high" if best_dist < (eff_threshold * 0.8) else ("medium" if match else "low"),
            "margin": margin,
            "engine": f"matrix-{mat_dim}d",
            "total_candidates": len(ids),
            "reason": "Wajah siswa berhasil dikenali." if match else f"Wajah tidak terdaftar atau tidak cocok (jarak: {best_dist:.4f}, batas: {eff_threshold})."
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
    results['arcface_512d'] = 'READY' if arcface_session is not None else 'NOT LOADED'

    return {
        "success": True,
        "packages": results,
        "threshold_128": THRESHOLD_128,
        "threshold_512": THRESHOLD_512,
        "liveness_threshold": LIVENESS_THRESHOLD,
        "ready": True
    }
