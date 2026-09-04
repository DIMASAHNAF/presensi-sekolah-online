#!/usr/bin/env python3
"""
face_service.py — Face Recognition Service untuk Sistem Presensi Sekolah
Dipanggil oleh Laravel via shell_exec / proc_open

Usage:
  python3 face_service.py extract   '{"image_b64": "<base64>"}'
  python3 face_service.py compare   '{"stored": [128 floats], "image_b64": "<base64>"}'
  python3 face_service.py enroll    '{"images_b64": ["<b64_1>", ..., "<b64_5>"]}'
  python3 face_service.py test

Returns JSON to stdout. Errors go to stderr.
"""

import sys
import json
import base64
import os
import io
import traceback

# ── threshold: makin kecil makin ketat (0.45 cukup ketat, susah ditipu foto)
THRESHOLD = 0.45

MODEL_DIR = os.path.join(os.path.dirname(__file__), 'models')

def load_libs():
    """Lazy-load face_recognition dan numpy agar error import lebih jelas."""
    try:
        import face_recognition
        import numpy as np
        return face_recognition, np
    except ImportError as e:
        error_exit(f"Dependensi belum terinstall: {e}. Jalankan: pip3 install face-recognition numpy")

def error_exit(message: str, code: int = 1):
    print(json.dumps({"success": False, "error": message}))
    sys.exit(code)

def decode_image(b64_string: str):
    """Decode base64 image string ke numpy array (RGB)."""
    face_recognition, np = load_libs()
    try:
        # Hapus prefix data:image/...;base64, jika ada
        if ',' in b64_string:
            b64_string = b64_string.split(',', 1)[1]
        img_bytes = base64.b64decode(b64_string)
        from PIL import Image
        img = Image.open(io.BytesIO(img_bytes)).convert('RGB')
        return np.array(img)
    except Exception as e:
        error_exit(f"Gagal decode gambar: {e}")

def extract_descriptor(image_array):
    """Extract 128-float face descriptor dari numpy image array. Return list atau None."""
    face_recognition, np = load_libs()
    
    # Deteksi lokasi wajah (model HOG — cepat, cukup akurat)
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

def euclidean_distance(a, b):
    """Hitung Euclidean distance antara dua descriptor."""
    face_recognition, np = load_libs()
    return float(np.linalg.norm(np.array(a) - np.array(b)))

# ──────────────────────────────────────────────────────────────
#  MODE: extract — ambil descriptor dari 1 foto
# ──────────────────────────────────────────────────────────────
def mode_extract(payload: dict):
    if 'image_b64' not in payload:
        error_exit("Field 'image_b64' wajib ada.")
    
    img_array = decode_image(payload['image_b64'])
    descriptor, err = extract_descriptor(img_array)
    
    if err:
        print(json.dumps({"success": False, "error": err}))
        return
    
    print(json.dumps({
        "success": True,
        "descriptor": descriptor,
        "dimensions": len(descriptor)
    }))

# ──────────────────────────────────────────────────────────────
#  MODE: enroll — rata-rata descriptor dari 5 foto multi-angle
# ──────────────────────────────────────────────────────────────
def mode_enroll(payload: dict):
    face_recognition, np = load_libs()
    
    if 'images_b64' not in payload or not isinstance(payload['images_b64'], list):
        error_exit("Field 'images_b64' harus berupa array.")
    
    images = payload['images_b64']
    if len(images) < 3:
        error_exit("Minimal 3 foto wajah diperlukan untuk enrollment.")
    
    descriptors = []
    errors = []
    
    for i, b64 in enumerate(images):
        img_array = decode_image(b64)
        descriptor, err = extract_descriptor(img_array)
        if err:
            errors.append(f"Foto {i+1}: {err}")
            continue
        descriptors.append(descriptor)
    
    if len(descriptors) < 3:
        error_exit(f"Terlalu banyak foto gagal diproses. Error: {'; '.join(errors)}")
    
    # Rata-rata semua descriptor yang berhasil (lebih robust)
    avg_descriptor = np.mean(np.array(descriptors), axis=0).tolist()
    
    print(json.dumps({
        "success": True,
        "descriptor": avg_descriptor,
        "processed": len(descriptors),
        "total": len(images),
        "errors": errors
    }))

# ──────────────────────────────────────────────────────────────
#  MODE: compare — bandingkan stored descriptor vs foto baru
# ──────────────────────────────────────────────────────────────
def mode_compare(payload: dict):
    if 'stored' not in payload or 'image_b64' not in payload:
        error_exit("Field 'stored' (array 128 float) dan 'image_b64' wajib ada.")
    
    stored = payload['stored']
    if len(stored) != 128:
        error_exit(f"Stored descriptor harus 128 elemen, dapat {len(stored)}.")
    
    # Extract descriptor dari gambar baru
    img_array = decode_image(payload['image_b64'])
    new_descriptor, err = extract_descriptor(img_array)
    
    if err:
        print(json.dumps({
            "success": True,
            "match": False,
            "distance": None,
            "reason": err
        }))
        return
    
    # Hitung jarak
    distance = euclidean_distance(stored, new_descriptor)
    match = distance < THRESHOLD
    
    # Confidence level
    if distance < 0.35:
        confidence = "very_high"
    elif distance < 0.45:
        confidence = "high"
    elif distance < 0.55:
        confidence = "medium"
    else:
        confidence = "low"
    
    print(json.dumps({
        "success": True,
        "match": match,
        "distance": round(distance, 4),
        "threshold": THRESHOLD,
        "confidence": confidence,
        "reason": "Wajah dikenali." if match else f"Wajah tidak cocok (jarak: {distance:.4f}, threshold: {THRESHOLD})."
    }))

# ──────────────────────────────────────────────────────────────
#  MODE: test — self-check dependencies
# ──────────────────────────────────────────────────────────────
def mode_test():
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
    
    try:
        import dlib
        results['dlib'] = getattr(dlib, '__version__', None) or getattr(dlib, 'DLIB_VERSION', 'installed')
    except ImportError:
        results['dlib'] = 'NOT INSTALLED'
    
    all_ok = all(v not in ('NOT INSTALLED',) for v in results.values())
    
    print(json.dumps({
        "success": all_ok,
        "packages": results,
        "threshold": THRESHOLD,
        "ready": all_ok
    }))

# ──────────────────────────────────────────────────────────────
#  MAIN
# ──────────────────────────────────────────────────────────────
def main():
    if len(sys.argv) < 2:
        error_exit("Usage: face_service.py <mode> [payload_json]\nModes: extract, enroll, compare, test")
    
    mode = sys.argv[1].strip().lower()
    
    if mode == 'test':
        mode_test()
        return
    
    if len(sys.argv) < 3:
        error_exit(f"Mode '{mode}' membutuhkan payload JSON sebagai argumen kedua.")
    
    try:
        payload_arg = sys.argv[2]
        import os
        if os.path.isfile(payload_arg):
            with open(payload_arg, 'r') as f:
                payload = json.load(f)
        else:
            payload = json.loads(payload_arg)
    except json.JSONDecodeError as e:
        error_exit(f"Payload JSON tidak valid: {e}")
    
    if mode == 'extract':
        mode_extract(payload)
    elif mode == 'enroll':
        mode_enroll(payload)
    elif mode == 'compare':
        mode_compare(payload)
    else:
        error_exit(f"Mode '{mode}' tidak dikenal. Mode valid: extract, enroll, compare, test")

if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        print(json.dumps({"success": False, "error": str(e), "trace": traceback.format_exc()}))
        sys.exit(1)
