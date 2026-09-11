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

# ── threshold: makin kecil makin ketat (0.45 cukup ketat)
THRESHOLD = 0.45

# --- Pydantic Models untuk Validasi Input ---

class ExtractRequest(BaseModel):
    image_b64: str

class EnrollRequest(BaseModel):
    images_b64: List[str]

class CompareRequest(BaseModel):
    stored: List[float]
    image_b64: str

# --- Helper Functions ---

def decode_image(b64_string: str) -> np.ndarray:
    """
    Decode a base64 encoded image string into a numpy array (RGB).
    
    Args:
        b64_string (str): Base64 encoded string of the image.
        
    Returns:
        np.ndarray: The image converted to an RGB numpy array.
        
    Raises:
        ValueError: If decoding or image loading fails.
    """
    try:
        if ',' in b64_string:
            b64_string = b64_string.split(',', 1)[1]
        img_bytes = base64.b64decode(b64_string)
        img = Image.open(io.BytesIO(img_bytes)).convert('RGB')
        return np.array(img)
    except Exception as e:
        raise ValueError(f"Gagal decode gambar: {e}")

def extract_descriptor(image_array: np.ndarray) -> Tuple[Optional[List[float]], Optional[str]]:
    """
    Extract a 128-float face descriptor from a numpy image array.
    
    Args:
        image_array (np.ndarray): The image array containing the face.
        
    Returns:
        Tuple[Optional[List[float]], Optional[str]]: 
            - A list of 128 floats representing the face descriptor, or None if extraction fails.
            - An error message string if extraction fails, or None on success.
    """
    # Deteksi lokasi wajah (model HOG — cepat)
    face_locations = face_recognition.face_locations(image_array, model="hog")
    
    if len(face_locations) == 0:
        return None, "Tidak ada wajah yang terdeteksi dalam gambar."
    
    if len(face_locations) > 1:
        return None, "Terdeteksi lebih dari satu wajah. Pastikan hanya satu wajah dalam frame."
    
    # Extract encoding
    encodings = face_recognition.face_encodings(image_array, face_locations)
    
    if not encodings:
        return None, "Gagal mengekstrak fitur wajah. Coba foto ulang dengan pencahayaan lebih baik."
    
    return encodings[0].tolist(), None

def euclidean_distance(a: List[float], b: List[float]) -> float:
    """
    Calculate the Euclidean distance between two descriptors.
    
    Args:
        a (List[float]): First descriptor.
        b (List[float]): Second descriptor.
        
    Returns:
        float: The computed Euclidean distance.
    """
    return float(np.linalg.norm(np.array(a) - np.array(b)))

# --- Endpoints ---

@app.post("/extract")
def mode_extract(payload: ExtractRequest) -> Dict[str, Any]:
    """
    Endpoint to extract face descriptor from a single image.
    
    Args:
        payload (ExtractRequest): The request payload containing the base64 image.
        
    Returns:
        Dict[str, Any]: A dictionary containing success status, descriptor, and dimensions or error.
    """
    try:
        img_array = decode_image(payload.image_b64)
        descriptor, err = extract_descriptor(img_array)
        
        if err:
            return {"success": False, "error": err}
            
        return {
            "success": True,
            "descriptor": descriptor,
            "dimensions": len(descriptor) if descriptor else 0
        }
    except Exception as e:
        return {"success": False, "error": str(e), "trace": traceback.format_exc()}


@app.post("/enroll")
def mode_enroll(payload: EnrollRequest) -> Dict[str, Any]:
    """
    Endpoint to enroll a face using multiple images and compute the average descriptor.
    
    Args:
        payload (EnrollRequest): The request payload containing multiple base64 images.
        
    Returns:
        Dict[str, Any]: A dictionary containing the averaged descriptor and processing status.
    """
    try:
        images = payload.images_b64
        if len(images) < 3:
            return {"success": False, "error": "Minimal 3 foto wajah diperlukan untuk enrollment."}
        
        descriptors = []
        errors = []
        
        for i, b64 in enumerate(images):
            try:
                img_array = decode_image(b64)
                descriptor, err = extract_descriptor(img_array)
                if err:
                    errors.append(f"Foto {i+1}: {err}")
                    continue
                descriptors.append(descriptor)
            except Exception as e:
                errors.append(f"Foto {i+1}: Gagal decode ({str(e)})")
        
        if len(descriptors) < 3:
            return {"success": False, "error": f"Terlalu banyak foto gagal diproses. Error: {'; '.join(errors)}"}
        
        # Rata-rata semua descriptor yang berhasil
        avg_descriptor = np.mean(np.array(descriptors), axis=0).tolist()
        
        return {
            "success": True,
            "descriptor": avg_descriptor,
            "processed": len(descriptors),
            "total": len(images),
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
        
        distance = euclidean_distance(stored, new_descriptor)
        match = distance < THRESHOLD
        
        if distance < 0.35:
            confidence = "very_high"
        elif distance < 0.45:
            confidence = "high"
        elif distance < 0.55:
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
