#!/usr/bin/env python3
"""
face_api.py — FastAPI Server untuk Sistem Presensi Sekolah (Solusi 1)
Menggantikan face_service.py berbasis CLI untuk performa real-time dan memory-resident.
"""

from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
from typing import List, Optional, Tuple, Dict, Any
import base64
import io
import traceback
import sys

# Load AI libraries di luar endpoint agar diload sekali saat server menyala
try:
    import face_recognition
    import numpy as np
    from PIL import Image
except ImportError as e:
    print(f"Error: {e}. Pastikan face_recognition, numpy, dan Pillow sudah terinstall.")
    sys.exit(1)

app = FastAPI(title="Face Recognition API", description="API Service untuk presensi")

# ── threshold: 0.50 adalah standar optimal untuk dlib 128D (wajah sama: 0.15-0.40, orang lain: > 0.60)
THRESHOLD = 0.50

# --- Pydantic Models untuk Validasi Input ---

class ExtractRequest(BaseModel):
    image_b64: str

class EnrollRequest(BaseModel):
    images_b64: List[str]

class CompareRequest(BaseModel):
    stored: List[float]
    image_b64: str

class Candidate(BaseModel):
    id: int
    descriptor: List[float]

class SyncCandidatesRequest(BaseModel):
    candidates: List[Candidate]

class IdentifyRequest(BaseModel):
    image_b64: str
    candidates: Optional[List[Candidate]] = None
    threshold: Optional[float] = None

# --- Helper Functions ---

def decode_image(b64_string: str, max_dimension: int = 720) -> np.ndarray:
    """
    Decode a base64 encoded image string into an RGB numpy array.
    Supports adjustable max_dimension (e.g. 1024 for high-precision enrollment, 720 for realtime).
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

def extract_descriptor(
    image_array: np.ndarray,
    high_model: bool = False,
    num_jitters: int = 1
) -> Tuple[Optional[List[float]], Optional[str]]:
    """
    Ekstrak 128-float face descriptor menggunakan High Model 68-point landmarks.
    Mendukung deteksi multi-scale dan toleransi sudut miring saat pendaftaran.
    """
    # 1. Deteksi lokasi wajah awal
    face_locations = face_recognition.face_locations(image_array, model="hog")

    # 1b. Jika mode high_model aktif dan wajah belum ditemukan (misal toleh kiri/kanan), coba upsample 1x
    if len(face_locations) == 0 and high_model:
        face_locations = face_recognition.face_locations(image_array, number_of_times_to_upsample=1, model="hog")

    if len(face_locations) == 0:
        return None, "Tidak ada wajah yang terdeteksi dalam frame. Pastikan wajah tepat di depan kamera."

    if len(face_locations) > 1:
        # Jika ada orang lain di latar belakang, pilih wajah terbesar yang berada di depan kamera
        face_locations = sorted(
            face_locations,
            key=lambda b: (b[2] - b[0]) * (b[1] - b[3]),
            reverse=True
        )
        face_locations = [face_locations[0]]

    # 2. Ekstrak encoding dengan model 'large' (68 landmark biometrik penuh)
    jitters = num_jitters if high_model else 1
    encodings = face_recognition.face_encodings(
        image_array,
        face_locations,
        num_jitters=jitters,
        model="large"
    )

    if not encodings:
        return None, "Gagal mengekstrak fitur wajah. Pastikan pencahayaan cukup dan wajah terlihat jelas."

    return encodings[0].tolist(), None

def euclidean_distance(a: List[float], b: List[float]) -> float:
    """
    Calculate the Euclidean distance between two descriptors.
    """
    return float(np.linalg.norm(np.array(a) - np.array(b)))

# --- Endpoints ---

@app.post("/extract")
def mode_extract(payload: ExtractRequest) -> Dict[str, Any]:
    try:
        img_array = decode_image(payload.image_b64, max_dimension=720)
        descriptor, err = extract_descriptor(img_array, high_model=True, num_jitters=1)

        if err:
            return {"success": False, "error": err}

        return {
            "success": True,
            "descriptor": descriptor,
            "dimensions": len(descriptor) if descriptor else 0,
            "model_type": "high-68p"
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/enroll")
def mode_enroll(payload: EnrollRequest) -> Dict[str, Any]:
    """
    Endpoint High Model Pendaftaran Wajah (Biometrik 68-Point Konsensus Multi-Sudut).
    """
    try:
        images = payload.images_b64
        if len(images) < 3:
            return {"success": False, "error": "Minimal 3 foto wajah diperlukan untuk enrollment."}

        descriptors = []
        errors = []

        for i, b64 in enumerate(images):
            try:
                # Gunakan resolusi 1024 untuk ekstraksi detail maksimal
                img_array = decode_image(b64, max_dimension=1024)
                # Gunakan model 'large' 68-point dengan multi-pass jittering
                descriptor, err = extract_descriptor(img_array, high_model=True, num_jitters=2)
                if err:
                    errors.append(f"Foto {i+1}: {err}")
                    continue
                descriptors.append(descriptor)
            except Exception as e:
                errors.append(f"Foto {i+1}: Gagal decode ({str(e)})")

        if len(descriptors) < 3:
            return {
                "success": False,
                "error": f"Hanya {len(descriptors)}/{len(images)} foto yang memenuhi syarat biometrik. Error: {'; '.join(errors)}"
            }

        desc_array = np.array(descriptors, dtype=np.float32)

        # Robust filtering: Abaikan outlier jika ada 4 atau 5 sampel foto
        if len(descriptors) >= 4:
            median_vec = np.median(desc_array, axis=0)
            dists_to_median = np.linalg.norm(desc_array - median_vec, axis=1)
            clean_mask = dists_to_median < 0.40
            if np.sum(clean_mask) >= 3:
                desc_array = desc_array[clean_mask]

        # Rata-rata konsensus biometrik (Gunakan skala asli dlib space ~1.41-1.45, JANGAN dinormalisasi L2)
        avg_descriptor = np.mean(desc_array, axis=0).tolist()

        return {
            "success": True,
            "descriptor": avg_descriptor,
            "processed": len(desc_array),
            "total_submitted": len(images),
            "model_version": "high-68p-jitters2",
            "errors": errors
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/compare")
def mode_compare(payload: CompareRequest) -> Dict[str, Any]:
    """
    Endpoint to compare an uploaded image against a stored face descriptor.
    
    Args:
        payload (CompareRequest): The request payload containing the stored descriptor and new base64 image.
        
    Returns:
        Dict[str, Any]: A dictionary containing the match result, distance, and confidence level.
    """
    try:
        stored = payload.stored
        if len(stored) != 128:
            return {"success": False, "error": f"Stored descriptor harus 128 elemen, dapat {len(stored)}."}
        
        img_array = decode_image(payload.image_b64)
        new_descriptor, err = extract_descriptor(img_array)
        
        if err:
            return {
                "success": True,
                "match": False,
                "distance": None,
                "reason": err
            }
        
        stored_vec = np.array(stored, dtype=np.float32)
        new_vec = np.array(new_descriptor, dtype=np.float32)

        # Self-healing safeguard: jika stored descriptor dinormalisasi L2 (norm ~1.0)
        # sedangkan new_vec adalah raw dlib (norm ~1.43), kalibrasi magnitudonya secara dinamis
        stored_norm = float(np.linalg.norm(stored_vec))
        new_norm = float(np.linalg.norm(new_vec))
        if stored_norm > 0 and stored_norm < 1.15 and new_norm > 1.30:
            stored_vec = stored_vec * new_norm

        distance = float(np.linalg.norm(stored_vec - new_vec))
        match = distance < THRESHOLD
        
        if distance < 0.35:
            confidence = "very_high"
        elif distance < 0.45:
            confidence = "high"
        elif distance <= THRESHOLD:
            confidence = "medium"
        else:
            confidence = "low"
        
        return {
            "success": True,
            "match": match,
            "distance": round(distance, 4),
            "threshold": THRESHOLD,
            "confidence": confidence,
            "reason": "Wajah dikenali." if match else f"Wajah tidak cocok (jarak: {distance:.4f}, threshold: {THRESHOLD})."
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.get("/test")
def mode_test() -> Dict[str, Any]:
    """
    Test endpoint to check the environment status and dependencies.
    
    Returns:
        Dict[str, Any]: A dictionary containing the status of required packages.
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
        
    all_ok = all(v not in ('NOT INSTALLED',) for v in results.values())
    
    return {
        "success": all_ok,
        "packages": results,
        "threshold": THRESHOLD,
        "ready": all_ok
    }


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
            if len(c.descriptor) == 128:
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
            "total": len(valid_ids)
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.api_route("/candidates-stats", methods=["GET", "POST"])
def mode_candidates_stats() -> Dict[str, Any]:
    """
    Cek status data wajah yang ter-cache di RAM server.
    """
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
    Endpoint 1:N Facial Recognition (Face ID Kiosk Gerbang).
    Menerima foto snapshot dari tablet gerbang, mencari siswa paling cocok di antara 1.400+ siswa.
    """
    global CANDIDATE_IDS, CANDIDATE_MATRIX

    try:
        # Tentukan database kandidat yang digunakan
        ids = CANDIDATE_IDS
        matrix = CANDIDATE_MATRIX

        # Jika caller mengirimkan daftar kandidat ad-hoc, gunakan itu
        if payload.candidates is not None and len(payload.candidates) > 0:
            custom_ids = []
            custom_vectors = []
            for c in payload.candidates:
                if len(c.descriptor) == 128:
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

        # Decode gambar & ekstrak descriptor
        img_array = decode_image(payload.image_b64)
        query_descriptor, err = extract_descriptor(img_array)

        if err:
            return {
                "success": True,
                "match": False,
                "distance": None,
                "reason": err
            }

        query_vec = np.array(query_descriptor, dtype=np.float32)

        # Safeguard: jika candidate matrix memiliki vektor yang ternormalisasi (norm < 1.15),
        # kalibrasikan magnitudonya agar konsisten dengan dlib query space
        if matrix.ndim == 2 and matrix.shape[0] > 0:
            norms = np.linalg.norm(matrix, axis=1)
            bad_mask = norms < 1.15
            if np.any(bad_mask):
                matrix = matrix.copy()
                query_norm = float(np.linalg.norm(query_vec))
                if query_norm > 1.30:
                    matrix[bad_mask] = matrix[bad_mask] * query_norm

        # Hitung jarak Euclidean ke seluruh kandidat sekaligus (vektorisasi NumPy)
        # Sangat cepat (< 4ms untuk 1.400 siswa)
        diff = matrix - query_vec
        distances = np.linalg.norm(diff, axis=1)

        best_idx = int(np.argmin(distances))
        best_dist = float(distances[best_idx])
        matched_id = ids[best_idx]

        # Threshold untuk 1:N di Kiosk Gerbang (default 0.45 agar akurat dan tidak salah orang)
        eff_threshold = payload.threshold if payload.threshold is not None else 0.45
        match = best_dist <= eff_threshold

        # Hitung margin dengan urutan ke-2 jika ada lebih dari 1 kandidat
        margin = None
        if len(distances) > 1:
            sorted_distances = np.partition(distances, 1)
            margin = round(float(sorted_distances[1] - best_dist), 4)

        if best_dist < 0.32:
            confidence = "very_high"
        elif best_dist < 0.38:
            confidence = "high"
        elif best_dist <= eff_threshold:
            confidence = "medium"
        else:
            confidence = "low"

        return {
            "success": True,
            "match": match,
            "user_id": matched_id if match else None,
            "distance": round(best_dist, 4),
            "threshold": eff_threshold,
            "confidence": confidence,
            "margin": margin,
            "total_candidates": len(ids),
            "reason": "Wajah siswa berhasil dikenali." if match else f"Wajah tidak terdaftar atau tidak cocok (jarak terdekat: {best_dist:.4f}, batas: {eff_threshold})."
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}
