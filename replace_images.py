import os
import glob

directory = "/www/wwwroot/presensi.tiksmkn1beringin.my.id/resources/views/**/*.blade.php"
files = glob.glob(directory, recursive=True)

count = 0
for file in files:
    with open(file, 'r') as f:
        content = f.read()
    
    new_content = content.replace("images/logo.png", "images/logo.webp")
    new_content = new_content.replace("images/logo-kolaborasi.png", "images/logo-kolaborasi.webp")
    new_content = new_content.replace("images/bg-sekolah.jpg", "images/bg-sekolah.webp")
    
    if new_content != content:
        with open(file, 'w') as f:
            f.write(new_content)
        print(f"Updated {file}")
        count += 1

print(f"Total files updated: {count}")
