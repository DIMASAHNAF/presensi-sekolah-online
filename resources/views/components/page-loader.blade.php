{{-- Global Page Transition Loader --}}
<div id="global-page-loader"
     class="fixed inset-0 z-[9999] bg-slate-950/85 backdrop-blur-xl flex flex-col items-center justify-center transition-opacity duration-300 opacity-0 pointer-events-none">
    <x-loading-school
        :texts="[
            'Menyiapkan Halaman...',
            'Menghubungkan ke Sistem...',
            'Memuat Data Presensi...',
            'Sebentar Lagi...'
        ]"
        :interval="1400"
    />
</div>

<script>
    (function() {
        const loader = document.getElementById('global-page-loader');
        if (!loader) return;

        let safetyTimer = null;

        function showLoader() {
            loader.classList.remove('opacity-0', 'pointer-events-none');
            loader.classList.add('opacity-100');

            // Safety timeout: jika navigasi batal / dicegah, sembunyikan otomatis setelah 8 detik
            if (safetyTimer) clearTimeout(safetyTimer);
            safetyTimer = setTimeout(hideLoader, 8000);
        }

        function hideLoader() {
            loader.classList.remove('opacity-100');
            loader.classList.add('opacity-0', 'pointer-events-none');
            if (safetyTimer) clearTimeout(safetyTimer);
        }

        // Tangkap klik pada link internal untuk animasi perpindahan halaman
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            // Abaikan link hash (#), javascript, link target blank, dan link download
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || link.target === '_blank' || link.hasAttribute('download')) {
                return;
            }

            try {
                const targetUrl = new URL(link.href, window.location.origin);
                // Hanya aktifkan jika link internal dan URL berbeda dari halaman saat ini
                if (targetUrl.origin === window.location.origin && (targetUrl.pathname !== window.location.pathname || targetUrl.search !== window.location.search)) {
                    showLoader();
                }
            } catch(err) {}
        });

        // Tangkap submit form (misal login, filter, simpan data)
        document.addEventListener('submit', function(e) {
            if (e.target && e.target.target === '_blank') return;
            showLoader();
        });

        // Pastikan loading tertutup jika user menekan tombol Back / Forward di browser (bfcache)
        window.addEventListener('pageshow', function() {
            hideLoader();
        });
    })();
</script>
