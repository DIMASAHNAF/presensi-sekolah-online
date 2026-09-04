#!/bin/bash
# download_models.sh — Download dlib face recognition model files
# Run: bash python/download_models.sh

set -e

MODELS_DIR="$(dirname "$0")/models"
mkdir -p "$MODELS_DIR"

echo "📦 Downloading dlib face recognition models..."

# shape_predictor_68_face_landmarks.dat (~99 MB)
if [ ! -f "$MODELS_DIR/shape_predictor_68_face_landmarks.dat" ]; then
    echo "⬇  Downloading shape_predictor_68_face_landmarks.dat..."
    wget -q --show-progress -O "$MODELS_DIR/shape_predictor_68_face_landmarks.dat.bz2" \
        "http://dlib.net/files/shape_predictor_68_face_landmarks.dat.bz2"
    bzip2 -d "$MODELS_DIR/shape_predictor_68_face_landmarks.dat.bz2"
    echo "✅ shape_predictor_68 downloaded."
else
    echo "✅ shape_predictor_68 already exists, skipping."
fi

# dlib_face_recognition_resnet_model_v1.dat (~22 MB)
if [ ! -f "$MODELS_DIR/dlib_face_recognition_resnet_model_v1.dat" ]; then
    echo "⬇  Downloading dlib_face_recognition_resnet_model_v1.dat..."
    wget -q --show-progress -O "$MODELS_DIR/dlib_face_recognition_resnet_model_v1.dat.bz2" \
        "http://dlib.net/files/dlib_face_recognition_resnet_model_v1.dat.bz2"
    bzip2 -d "$MODELS_DIR/dlib_face_recognition_resnet_model_v1.dat.bz2"
    echo "✅ dlib_face_recognition_resnet_model downloaded."
else
    echo "✅ dlib_face_recognition_resnet_model already exists, skipping."
fi

echo ""
echo "🎉 Semua model berhasil didownload ke: $MODELS_DIR"
echo ""
echo "🧪 Testing face_service.py..."
python3 "$(dirname "$0")/face_service.py" test
