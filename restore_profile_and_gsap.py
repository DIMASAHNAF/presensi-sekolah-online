import re

# Read old dashboard for Profile Card
with open("old_dashboard.blade.php", "r", encoding="utf-8") as f:
    old_lines = f.readlines()

profile_start = -1
profile_end = -1
for i, line in enumerate(old_lines):
    if "{{-- KARTU PROFIL SISWA" in line and profile_start == -1:
        profile_start = i
    if "{{-- SESI PRESENSI AKTIF CARD" in line and profile_end == -1:
        profile_end = i
        break

old_profile_html = "".join(old_lines[profile_start:profile_end])

# Read current dashboard
with open("resources/views/siswa/dashboard.blade.php", "r", encoding="utf-8") as f:
    current_lines = f.readlines()

new_profile_start = -1
new_profile_end = -1
for i, line in enumerate(current_lines):
    if "{{-- BENTO GRID: KARTU PROFIL & GAMIFIKASI --}}" in line and new_profile_start == -1:
        new_profile_start = i
    if "{{-- SESI PRESENSI AKTIF CARD" in line and new_profile_end == -1:
        new_profile_end = i
        break

# Inject GSAP into the layout if not present
layout_path = "resources/views/layouts/dashboard.blade.php"
with open(layout_path, "r", encoding="utf-8") as f:
    layout_content = f.read()

if "gsap.min.js" not in layout_content:
    layout_content = layout_content.replace(
        "<!-- Scripts -->",
        "<!-- Scripts -->\n    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js\"></script>\n    <script src=\"https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js\"></script>"
    )
    with open(layout_path, "w", encoding="utf-8") as f:
        f.write(layout_content)


if new_profile_start != -1 and new_profile_end != -1:
    # Wrap the old profile card in a gsap target class
    old_profile_html = old_profile_html.replace('data-aos="fade-down"', 'class="gsap-profile-card ' + old_profile_html.split('data-aos="fade-down"', 1)[0].rsplit('class="', 1)[-1].split('"')[0] + '"')
    # Actually just add gsap classes
    
    # Let's completely replace the bento grid with the old profile card, plus we will add GSAP classes to elements.
    current_lines[new_profile_start:new_profile_end] = [old_profile_html]
    
    # Add GSAP animation script at the bottom of dashboard.blade.php
    gsap_script = """
    <script>
        document.addEventListener('alpine:initialized', () => {
            // Animate Profile Card
            gsap.fromTo(".bg-white.dark\\\\:bg-slate-900.rounded-3xl.border", 
                { y: 30, opacity: 0 }, 
                { y: 0, opacity: 1, duration: 0.8, ease: "power3.out", stagger: 0.2 }
            );

            // Animate Bottom Nav
            gsap.fromTo("nav.lg\\\\:hidden", 
                { y: 100, opacity: 0 }, 
                { y: 0, opacity: 1, duration: 1, delay: 0.5, ease: "elastic.out(1, 0.5)" }
            );
            
            // Animate Tab Buttons
            gsap.fromTo("nav.lg\\\\:hidden button",
                { scale: 0, opacity: 0 },
                { scale: 1, opacity: 1, duration: 0.5, stagger: 0.1, delay: 0.8, ease: "back.out(1.7)" }
            );
        });
        
        // Add subtle hover animations to buttons
        const navButtons = document.querySelectorAll('nav.lg\\\\:hidden button');
        navButtons.forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                gsap.to(btn, { scale: 1.1, duration: 0.2, ease: "power2.out" });
            });
            btn.addEventListener('mouseleave', () => {
                // Alpine will handle the active state scale, so we just reset to 1 if not active
                if(!btn.classList.contains('scale-105')) {
                    gsap.to(btn, { scale: 1, duration: 0.2, ease: "power2.out" });
                }
            });
        });
    </script>
"""
    # Insert gsap_script before the closing push('scripts') or at the end
    script_pushed = False
    for i, line in reversed(list(enumerate(current_lines))):
        if "@endpush" in line:
            current_lines.insert(i, gsap_script)
            script_pushed = True
            break
            
    with open("resources/views/siswa/dashboard.blade.php", "w", encoding="utf-8") as f:
        f.writelines(current_lines)
    print("Reverted profile and added GSAP successfully.")
else:
    print("Error finding boundaries.")
