import os
import glob

# Update manifest.json
manifest_path = "/www/wwwroot/presensi.tiksmkn1beringin.my.id/public/manifest.json"
if os.path.exists(manifest_path):
    with open(manifest_path, 'r') as f:
        content = f.read()
    
    new_content = content.replace(".png", ".webp")
    new_content = new_content.replace("image/png", "image/webp")
    
    with open(manifest_path, 'w') as f:
        f.write(new_content)
    print(f"Updated {manifest_path}")

# Update blade files for icon-192x192.png
directory = "/www/wwwroot/presensi.tiksmkn1beringin.my.id/resources/views/**/*.blade.php"
files = glob.glob(directory, recursive=True)

count = 0
for file in files:
    with open(file, 'r') as f:
        content = f.read()
    
    new_content = content.replace("icon-192x192.png", "icon-192x192.webp")
    
    if new_content != content:
        with open(file, 'w') as f:
            f.write(new_content)
        print(f"Updated {file}")
        count += 1

print(f"Total blade files updated for icons: {count}")
