#!/usr/bin/env python3
import os
import sys

# Tambahkan direktori saat ini ke sys.path
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, BASE_DIR)
os.chdir(BASE_DIR)

import uvicorn

if __name__ == "__main__":
    # Jalankan uvicorn dengan 4 workers multi-process untuk utilisasi 16-core CPU
    uvicorn.run("face_api:app", host="127.0.0.1", port=8005, workers=4, log_level="info", access_log=False)
