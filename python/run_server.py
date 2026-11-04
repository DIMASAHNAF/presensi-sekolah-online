#!/usr/bin/env python3
import os
import sys

# Tambahkan direktori saat ini ke sys.path
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, BASE_DIR)
os.chdir(BASE_DIR)

import uvicorn
from face_api import app

if __name__ == "__main__":
    # Jalankan uvicorn single-process async (stabil, bebas fork C++ race condition)
    uvicorn.run(app, host="127.0.0.1", port=8005, log_level="info", access_log=False)
