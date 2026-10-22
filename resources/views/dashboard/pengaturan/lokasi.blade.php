@extends('layouts.dashboard')

@section('title', 'Pengaturan Lokasi & Geofencing SMKN 1 Beringin')
@section('page-title', 'Pengaturan Lokasi & Geofencing')
@section('page-subtitle', 'Tentukan batas area presensi siswa dengan Kotak Pagar Presisi atau Radius')

@push('styles')
{{-- Load Local Leaflet CSS --}}
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}">
<style>
    #map-preview {
        height: 520px;
        width: 100%;
        border-radius: 1rem;
        z-index: 10;
        background-color: #0f172a;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15);
    }
    .box-center-pin {
        cursor: grab !important;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .box-center-pin:active {
        cursor: grabbing !important;
        transform: scale(1.08);
    }
    .corner-pin {
        cursor: move !important;
        transition: transform 0.15s ease;
    }
    .corner-pin:hover {
        transform: scale(1.15);
    }
</style>
@endpush

@section('content')

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2 animate-shake">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>Ada error input: {{ $errors->first() }}</span>
    </div>
@endif

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2 shadow-xs">
        <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="lokasiApp()">
    
    {{-- Form Pengaturan --}}
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold">
                        <i class="fas fa-map-location-dot text-lg"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-slate-800 text-base">Parameter Geofencing</h2>
                        <p class="text-xs text-slate-400">Atur batas area presensi presisi</p>
                    </div>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold tracking-wider"
                      :class="isActive ? 'bg-emerald-100 text-emerald-700 border border-emerald-300' : 'bg-slate-100 text-slate-500'">
                    <span x-text="isActive ? 'GEOFENCE AKTIF' : 'NONAKTIF'"></span>
                </span>
            </div>

            <form action="{{ route('dashboard.lokasi.update') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Status Geofencing Toggle --}}
                <div class="p-4 rounded-2xl border transition-all"
                     :class="isActive ? 'bg-emerald-50/70 border-emerald-300 shadow-xs' : 'bg-slate-50 border-slate-200'">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <span class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                <i class="fas fa-shield-halved" :class="isActive ? 'text-emerald-600' : 'text-slate-400'"></i>
                                Validasi Geofencing Satelit GPS
                            </span>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Siswa wajib berada di dalam area pagar sekolah untuk presensi
                            </p>
                        </div>
                        <input type="checkbox" name="is_geofencing_active" value="1" 
                               x-model="isActive" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-teal-600 relative"></div>
                    </label>
                </div>

                {{-- Nama Sekolah --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Nama Lokasi / Sekolah</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-sm">
                            <i class="fas fa-school"></i>
                        </span>
                        <input type="text" name="school_name" x-model="schoolName" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                </div>

                {{-- Koordinat Lat & Lng Pusat --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Latitude Pusat</label>
                        <input type="number" step="any" name="latitude" x-model.number="lat" @input="updateMapFromInputs()" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Longitude Pusat</label>
                        <input type="number" step="any" name="longitude" x-model.number="lng" @input="updateMapFromInputs()" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                </div>

                {{-- Mode Geofencing: Pagar Poligon / Kotak vs Radius Lingkaran --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">Bentuk Batas Area (Geofence)</label>
                        <span class="text-[10px] font-bold text-teal-600 bg-teal-50 border border-teal-200 px-2 py-0.5 rounded-md">
                            <span x-text="geofenceMode === 'polygon' ? (polygonPoints.length + ' TITIK ARAH / SUDUT') : 'RADIUS LINGKARAN'"></span>
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                        {{-- Mode 1: Pagar Presisi (Kotak / Multi-Arah) --}}
                        <button type="button" @click="setGeofenceMode('polygon')"
                                :class="geofenceMode === 'polygon' ? 'bg-white text-teal-700 shadow-xs font-bold border border-teal-200' : 'text-slate-600 font-medium hover:text-slate-900'"
                                class="py-2.5 px-2 text-xs rounded-lg transition flex items-center justify-center gap-1.5 text-center">
                            <i class="fas fa-draw-polygon text-sm"></i>
                            <span>Pagar Presisi (Multi-Arah)</span>
                        </button>

                        {{-- Mode 2: Radius Lingkaran --}}
                        <button type="button" @click="setGeofenceMode('radius')"
                                :class="geofenceMode === 'radius' ? 'bg-white text-teal-700 shadow-xs font-bold border border-teal-200' : 'text-slate-600 font-medium hover:text-slate-900'"
                                class="py-2.5 px-2 text-xs rounded-lg transition flex items-center justify-center gap-1.5 text-center">
                            <i class="fas fa-circle-notch text-sm"></i>
                            <span>Radius Lingkaran</span>
                        </button>
                    </div>
                    <input type="hidden" name="geofence_mode" :value="geofenceMode">
                    <input type="hidden" name="polygon_coordinates" :value="JSON.stringify(polygonPoints)">
                </div>

                {{-- KONTROL TITIK ARAH / SUDUT PAGAR MANAGER (Tampil jika mode Polygon) --}}
                <div x-show="geofenceMode === 'polygon'" class="space-y-3 bg-gradient-to-br from-teal-50/80 to-emerald-50/80 border border-teal-200 rounded-2xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs">
                                <i class="fas fa-vector-square"></i>
                            </span>
                            <span class="text-xs font-bold text-slate-800">Kontrol Titik Arah & Pagar Sekolah</span>
                        </div>
                        <span class="text-[11px] font-mono font-bold text-teal-700 bg-white border border-teal-200 px-2 py-0.5 rounded-md flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                            <span x-text="polygonPoints.length + ' Titik Arah'"></span>
                        </span>
                    </div>

                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Tarik pin hijau <strong>"GESER AREA"</strong> di tengah peta untuk memindahkan seluruh denah sekaligus. Geser titik nomor untuk menyesuaikan batas/arah sekolah.
                    </p>

                    {{-- Tombol Navigasi Cepat --}}
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <button type="button" @click="focusAndAlignToSmkn1()"
                                class="text-xs font-bold py-2 px-3 rounded-xl bg-teal-600 hover:bg-teal-700 text-white transition flex items-center justify-center gap-1.5 shadow-xs">
                            <i class="fas fa-crosshairs"></i>
                            <span>Arahkan ke SMKN 1</span>
                        </button>
                        <button type="button" @click="alignBoxToCurrentCenter()"
                                class="text-xs font-semibold py-2 px-3 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-arrows-to-dot text-teal-600"></i>
                            <span>Pusatkan ke Layar</span>
                        </button>
                    </div>

                    {{-- TOMBOL KELOLA JUMLAH ARAH / SUDUT (TAMBAH / KURANGI ARAH) --}}
                    <div class="pt-2 border-t border-teal-200/60">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] text-slate-600 font-bold flex items-center gap-1">
                                <i class="fas fa-plus-minus text-teal-600 text-[10px]"></i> Kelola Titik Arah:
                            </span>
                            <span class="text-[10px] text-teal-700 font-semibold" x-text="'Saat ini: ' + polygonPoints.length + ' Arah'"></span>
                        </div>
                        <div class="grid grid-cols-3 gap-1.5">
                            {{-- Tambah Arah --}}
                            <button type="button" @click="addCornerPoint()"
                                    class="py-2 px-2 rounded-xl bg-white border border-teal-300 text-teal-700 hover:bg-teal-50 font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition active:scale-95">
                                <i class="fas fa-plus text-teal-600 text-xs"></i>
                                <span>+ Tambah Arah</span>
                            </button>

                            {{-- Kurangi Arah --}}
                            <button type="button" @click="removeLastCorner()" :disabled="polygonPoints.length <= 3"
                                    class="py-2 px-2 rounded-xl bg-white border border-rose-300 text-rose-700 hover:bg-rose-50 disabled:opacity-40 disabled:pointer-events-none font-bold text-xs flex items-center justify-center gap-1 shadow-xs transition active:scale-95"
                                    title="Kurangi 1 titik arah (minimal 3)">
                                <i class="fas fa-minus text-rose-600 text-xs"></i>
                                <span>- Kurangi</span>
                            </button>

                            {{-- Reset ke Kotak 4 Arah --}}
                            <button type="button" @click="resetDefaultSmkn1Box()"
                                    class="py-2 px-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 font-semibold text-xs flex items-center justify-center gap-1 shadow-xs transition active:scale-95"
                                    title="Kembalikan ke kotak 4 arah standar">
                                <i class="fas fa-rotate text-slate-500 text-xs"></i>
                                <span>Reset 4 Arah</span>
                            </button>
                        </div>
                        <p class="text-[10px] text-slate-500 mt-1.5 flex items-center gap-1">
                            <i class="fas fa-info-circle text-teal-500"></i>
                            <span>Bisa 4 arah jadi 5, 6, 7+ arah, atau klik langsung garis pagar pada peta!</span>
                        </p>
                    </div>

                    {{-- Skala Ukuran Cepat --}}
                    <div class="flex items-center justify-between pt-1 text-xs">
                        <span class="text-slate-500 font-medium">Ubah Ukuran:</span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="scaleBox(1.15)" title="Perluas area 15%"
                                    class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-teal-50 font-bold transition">
                                <i class="fas fa-expand text-[10px] text-teal-600"></i> +15%
                            </button>
                            <button type="button" @click="scaleBox(0.85)" title="Perkecil area 15%"
                                    class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-amber-50 font-bold transition">
                                <i class="fas fa-compress text-[10px] text-amber-600"></i> -15%
                            </button>
                        </div>
                    </div>

                    {{-- Metrics Display --}}
                    <div class="mt-2 bg-white/90 border border-teal-200 rounded-xl p-3 grid grid-cols-2 gap-2 text-center text-xs">
                        <div class="border-r border-slate-100 pr-2">
                            <span class="text-[10px] text-slate-400 block font-medium">Tipe Bentuk</span>
                            <span class="font-bold font-mono text-teal-700 text-xs" x-text="boxDimensions">4 Sudut Presisi</span>
                        </div>
                        <div class="pl-2">
                            <span class="text-[10px] text-slate-400 block font-medium">Perkiraan Luas Area</span>
                            <span class="font-bold font-mono text-emerald-600 text-xs" x-text="boxAreaText">~3.96 Hektar</span>
                        </div>
                    </div>

                    {{-- DAFTAR TITIK ARAH / SUDUT (Bisa Hapus Satuan) --}}
                    <div class="pt-2">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] text-slate-500 font-semibold">Daftar Titik Arah:</span>
                            <span class="text-[10px] text-slate-400">Klik ✕ untuk hapus arah</span>
                        </div>
                        <div class="max-h-28 overflow-y-auto space-y-1 pr-1">
                            <template x-for="(pt, idx) in polygonPoints" :key="idx">
                                <div class="flex items-center justify-between text-[10px] font-mono bg-white px-2 py-1 rounded-md border border-slate-200 shadow-2xs">
                                    <span class="text-teal-700 font-bold" x-text="'Arah ' + (idx + 1) + ':'"></span>
                                    <span class="text-slate-600" x-text="Number(pt.lat).toFixed(6) + ', ' + Number(pt.lng).toFixed(6)"></span>
                                    <button type="button" @click="removePointAt(idx)" :disabled="polygonPoints.length <= 3"
                                            class="text-rose-500 hover:text-rose-700 font-bold px-1.5 py-0.5 rounded hover:bg-rose-50 disabled:opacity-30 disabled:pointer-events-none transition"
                                            title="Hapus titik arah ini">✕</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- MODE RADIUS LINGKARAN (Tampil jika mode = radius) --}}
                <div x-show="geofenceMode === 'radius'" class="space-y-2">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">Radius Toleransi</label>
                        <span class="text-xs font-bold text-teal-600 bg-teal-50 px-2 py-0.5 rounded-lg">
                            <span x-text="radius"></span> Meter
                        </span>
                    </div>
                    <input type="range" min="20" max="1000" step="10" x-model.number="radius" @input="updateMapCircle()"
                           class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-teal-600">
                    <div class="flex justify-between text-[0.65rem] text-slate-400 mt-1 font-mono">
                        <span>20 m</span>
                        <span>100 m</span>
                        <span>250 m</span>
                        <span>500 m</span>
                        <span>1.000 m</span>
                    </div>
                    <div class="mt-2">
                        <input type="number" min="10" max="5000" name="radius_meters" x-model.number="radius" @input="updateMapCircle()"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                </div>

                {{-- ── SECTION 2: VALIDASI JARINGAN WIFI SEKOLAH (IP WHITELIST) ── --}}
                <div class="pt-4 border-t border-slate-200/80 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center font-bold">
                            <i class="fas fa-wifi text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 text-sm">Kunci Jaringan WiFi Sekolah</h3>
                            <p class="text-[11px] text-slate-400">Proteksi ganda: Hanya izinkan presensi dari WiFi resmi sekolah</p>
                        </div>
                    </div>

                    {{-- Toggle Status IP Whitelist --}}
                    <div class="p-4 rounded-2xl border transition-all"
                         :class="isIpActive ? 'bg-blue-50/60 border-blue-200' : 'bg-slate-50 border-slate-200'">
                        <label class="flex items-center justify-between cursor-pointer">
                            <div>
                                <span class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                    <i class="fas fa-network-wired" :class="isIpActive ? 'text-blue-600' : 'text-slate-400'"></i>
                                    Validasi IP Publik WiFi
                                </span>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Tolak otomatis siswa yang memakai data seluler pribadi / WiFi luar
                                </p>
                            </div>
                            <input type="checkbox" name="is_ip_whitelist_active" value="1" 
                                   x-model="isIpActive" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 relative"></div>
                        </label>
                    </div>

                    {{-- Card Deteksi IP Saya Saat Ini --}}
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3.5 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">IP Publik Anda Saat Ini</p>
                            <p class="text-xs font-mono font-bold text-slate-800 truncate flex items-center gap-1.5 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span>{{ $clientIp }}</span>
                            </p>
                        </div>
                        <button type="button" @click="addCurrentIp()"
                                class="shrink-0 text-xs font-bold text-blue-700 bg-white border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg shadow-2xs transition flex items-center gap-1.5">
                            <i class="fas fa-plus-circle text-blue-600"></i>
                            <span>Tambahkan IP Ini</span>
                        </button>
                    </div>

                    {{-- Kolom Input / Textarea Allowed IPs --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">
                                Daftar IP Publik WiFi Sekolah
                            </label>
                            <span class="text-[11px] text-slate-400">Pisahkan koma / baris baru</span>
                        </div>
                        <textarea name="allowed_ips" x-model="allowedIps" rows="3" placeholder="Contoh: 180.252.12.34, 36.88.90.12"
                                  class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition leading-relaxed"></textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-3 space-y-2">
                    <button type="button" @click="getCurrentLocation()"
                            class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center gap-2">
                        <i class="fas fa-crosshairs text-teal-600"></i> Ambil Titik GPS Saya Saat Ini
                    </button>

                    <button type="submit" class="btn-primary w-full justify-center py-3 text-sm shadow-md shadow-teal-500/20">
                        <i class="fas fa-floppy-disk mr-1"></i> Simpan Semua Pengaturan Geofence
                    </button>
                </div>
            </form>
        </div>

        {{-- Petunjuk Penggunaan --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 rounded-2xl p-5 text-white shadow-sm border border-slate-800">
            <div class="flex items-center gap-2 text-cyan-300 font-heading font-bold text-sm mb-3">
                <i class="fas fa-lightbulb"></i> Tips Menyesuaikan Arah & Sudut Pagar Sekolah
            </div>
            <ul class="text-xs text-slate-300 space-y-2.5 leading-relaxed">
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-[11px] shrink-0 mt-0.5">1</span>
                    <span>Klik <strong>"+ Tambah Arah"</strong> untuk menambah titik ke-5, ke-6, dst. Titik baru otomatis muncul di sisi pagar dan siap Anda geser ke denah gedung sekolah.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-[11px] shrink-0 mt-0.5">2</span>
                    <span>Anda juga bisa <strong>mengklik langsung garis hijau pagar</strong> pada peta satelit untuk menyisipkan titik arah baru tepat di posisi tersebut.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-400 flex items-center justify-center font-bold text-[11px] shrink-0 mt-0.5">3</span>
                    <span>Klik sembarang pin nomor arah di peta untuk membuka tombol <strong>"Hapus Titik Arah Ini"</strong> atau klik tombol <strong>✕</strong> di daftar samping.</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Map Display --}}
    <div class="lg:col-span-7">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 sm:p-6 h-full flex flex-col">
            
            {{-- Toolbar Atas Peta --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2 flex-wrap">
                        <span>Peta Geofence SMKN 1 Beringin</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="isActive ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300'"
                              x-text="isActive ? 'Validasi Aktif' : 'Nonaktif'"></span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Area hijau adalah batas wilayah aman presensi siswa</p>
                </div>
                
                {{-- Quick Action Buttons --}}
                <div class="flex items-center flex-wrap gap-2 shrink-0">
                    {{-- Tombol Arahkan ke SMKN 1 Beringin --}}
                    <button type="button" @click="focusAndAlignToSmkn1()"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-500 hover:to-emerald-500 px-3 py-1.5 rounded-xl shadow-xs transition active:scale-95">
                        <i class="fas fa-crosshairs"></i>
                        <span>Arahkan ke SMKN 1</span>
                    </button>

                    {{-- Switcher Tipe Peta: Satelit vs Jalan --}}
                    <div class="inline-flex rounded-xl p-0.5 bg-slate-100 border border-slate-200 text-xs font-semibold">
                        <button type="button" @click="setMapTile('satellite')"
                                :class="currentTile === 'satellite' ? 'bg-white text-teal-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                                class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                            <i class="fas fa-satellite text-teal-600"></i>
                            <span>Satelit</span>
                        </button>
                        <button type="button" @click="setMapTile('osm')"
                                :class="currentTile === 'osm' ? 'bg-white text-teal-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                                class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1">
                            <i class="fas fa-map"></i>
                            <span>Jalan</span>
                        </button>
                    </div>

                    <a :href="'https://www.google.com/maps?q=' + lat + ',' + lng" target="_blank" title="Buka di Google Maps"
                       class="inline-flex items-center gap-1.5 text-xs text-slate-600 hover:text-teal-700 bg-slate-100 hover:bg-teal-50 px-3 py-1.5 rounded-xl font-medium transition border border-slate-200">
                        <i class="fab fa-google text-red-500"></i>
                        <span class="hidden sm:inline">Maps</span>
                    </a>
                </div>
            </div>

            {{-- Container Map with Floating HUD --}}
            <div class="relative flex-1 min-h-[520px] rounded-2xl overflow-hidden border border-slate-200 shadow-inner bg-slate-900">
                <div id="map-preview"></div>

                {{-- Floating Top-Left Mode & Dimension HUD --}}
                <div class="absolute top-4 left-4 z-[400] bg-slate-900/85 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10 text-white shadow-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center font-bold text-sm">
                        <i :class="geofenceMode === 'polygon' ? 'fas fa-draw-polygon' : 'fas fa-circle-notch'"></i>
                    </div>
                    <div class="text-xs">
                        <p class="text-[10px] uppercase font-bold text-slate-400 font-mono tracking-wider">
                            <span x-text="geofenceMode === 'polygon' ? ('Pagar ' + polygonPoints.length + ' Titik Arah') : 'Radius Lingkaran'"></span>
                        </p>
                        <p class="font-bold text-teal-300 font-mono" x-text="geofenceMode === 'polygon' ? boxDimensions + ' (' + boxAreaText + ')' : radius + ' Meter Radius'"></p>
                    </div>
                </div>

                {{-- Floating Bottom-Left Coordinates Badge --}}
                <div class="absolute bottom-4 left-4 z-[400] bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-xl shadow-lg border border-slate-200 text-xs text-slate-700 font-mono flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-teal-500 animate-ping"></span>
                    <span>Pusat: <strong x-text="lat.toFixed(6)"></strong>, <strong x-text="lng.toFixed(6)"></strong></span>
                </div>

                {{-- Floating Bottom-Right Quick Alignment Tool --}}
                <div x-show="geofenceMode === 'polygon'" class="absolute bottom-4 right-4 z-[400] bg-slate-900/85 backdrop-blur-md p-1.5 rounded-xl border border-white/10 shadow-xl flex items-center gap-1 text-xs">
                    <button type="button" @click="alignBoxToCurrentCenter()" title="Pindahkan area ke tengah tampilan peta"
                            class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-teal-300 font-bold flex items-center gap-1.5 transition active:scale-95">
                        <i class="fas fa-arrows-to-dot text-teal-400"></i>
                        <span>Tengah Layar</span>
                    </button>
                    <button type="button" @click="addCornerPoint()" title="Tambah 1 titik arah baru (4 jadi 5, 5 jadi 6, dst)"
                            class="px-2.5 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-500 text-white font-bold flex items-center gap-1 transition active:scale-95">
                        <i class="fas fa-plus text-[10px]"></i> Arah
                    </button>
                    <button type="button" @click="removeLastCorner()" :disabled="polygonPoints.length <= 3" title="Kurangi 1 titik arah (minimal 3)"
                            class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold flex items-center gap-1 transition active:scale-95 disabled:opacity-30 disabled:pointer-events-none">
                        <i class="fas fa-minus text-[10px]"></i> Kurangi
                    </button>
                    <button type="button" @click="scaleBox(1.1)" title="Perbesar area (+10%)"
                            class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition">
                        +
                    </button>
                    <button type="button" @click="scaleBox(0.9)" title="Perkecil area (-10%)"
                            class="w-7 h-7 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-bold flex items-center justify-center transition">
                        -
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
{{-- Load Leaflet Local with unpkg CDN fallback --}}
<script src="{{ asset('vendor/leaflet/leaflet.js') }}" onerror="this.onerror=null;this.src='https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';"></script>
<script>
    function lokasiApp() {
        return {
            schoolName: '{{ addslashes($setting->school_name) }}',
            lat: {{ $setting->latitude }},
            lng: {{ $setting->longitude }},
            radius: {{ $setting->radius_meters }},
            isActive: {{ $setting->is_geofencing_active ? 'true' : 'false' }},
            isIpActive: {{ $setting->is_ip_whitelist_active ? 'true' : 'false' }},
            allowedIps: `{!! addslashes($setting->allowed_ips ?? '') !!}`,
            clientIp: '{{ $clientIp }}',
            geofenceMode: '{{ $setting->geofence_mode ?? "polygon" }}',
            polygonPoints: @json($setting->polygon_coordinates ?? []),
            map: null,
            marker: null,
            circle: null,
            polygonLayer: null,
            polygonMarkers: [],
            boxCenterMarker: null,
            currentTile: 'satellite', // Default satelit agar bangunan sekolah terlihat jelas
            osmLayer: null,
            satelliteLayer: null,
            boxDimensions: '4 Sudut Presisi',
            boxAreaText: '~3.96 Hektar',

            // Titik Koordinat Baku SMKN 1 Beringin
            SMKN1_CENTER_LAT: 3.586600,
            SMKN1_CENTER_LNG: 98.881300,

            init() {
                this.$nextTick(() => {
                    this.startMapInit();
                });
            },

            startMapInit() {
                if (typeof L !== 'undefined') {
                    this.initMap();
                } else {
                    let attempts = 0;
                    const checkInterval = setInterval(() => {
                        attempts++;
                        if (typeof L !== 'undefined') {
                            clearInterval(checkInterval);
                            this.initMap();
                        } else if (attempts > 30) {
                            clearInterval(checkInterval);
                            console.error('Leaflet JS gagal dimuat.');
                        }
                    }, 100);
                }
            },

            initMap() {
                if (this.map) return; // Mencegah double initialization error

                const container = document.getElementById('map-preview');
                if (!container) return;

                this.map = L.map('map-preview', {
                    zoomControl: true,
                    attributionControl: true
                }).setView([this.lat, this.lng], 18);

                // Layer 1: OpenStreetMap (Peta Jalan)
                this.osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                });

                // Layer 2: Google Maps Satellite Hybrid (Foto Satelit dengan Label Jalan & Gedung)
                this.satelliteLayer = L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                    subdomains: ['0', '1', '2', '3'],
                    maxZoom: 20,
                    attribution: '© Google Maps Satellite'
                });

                // Pasang layer default
                if (this.currentTile === 'satellite') {
                    this.satelliteLayer.addTo(this.map);
                } else {
                    this.osmLayer.addTo(this.map);
                }

                // Custom marker icon untuk titik pusat sekolah
                const customIcon = L.divIcon({
                    html: `<div style="background-color:#0d9488;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;box-shadow:0 4px 14px rgba(13,148,136,0.6);border:2.5px solid white;">
                            <i class="fas fa-school" style="font-size:14px;"></i>
                           </div>`,
                    className: '',
                    iconSize: [34, 34],
                    iconAnchor: [17, 17]
                });

                this.marker = L.marker([this.lat, this.lng], {
                    draggable: true,
                    icon: customIcon
                }).addTo(this.map);

                // Lingkaran radius
                this.circle = L.circle([this.lat, this.lng], {
                    color: '#0d9488',
                    fillColor: '#14b8a6',
                    fillOpacity: 0.22,
                    radius: this.radius
                });

                // Cek status poligon / area awal
                if (this.geofenceMode === 'polygon') {
                    if (!this.polygonPoints || this.polygonPoints.length < 3) {
                        this.createBoxAround(this.lat, this.lng, 220, 180);
                    } else {
                        this.renderPolygon();
                    }
                } else {
                    this.circle.addTo(this.map);
                }

                // Event marker center digeser
                this.marker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    this.lat = parseFloat(position.lat.toFixed(7));
                    this.lng = parseFloat(position.lng.toFixed(7));
                    this.circle.setLatLng(position);
                });

                // Event klik pada peta
                this.map.on('click', (e) => {
                    if (this.geofenceMode === 'radius') {
                        this.lat = parseFloat(e.latlng.lat.toFixed(7));
                        this.lng = parseFloat(e.latlng.lng.toFixed(7));
                        this.marker.setLatLng(e.latlng);
                        this.circle.setLatLng(e.latlng);
                    }
                });

                // Kalibrasi ukuran peta setelah layout browser selesai render
                setTimeout(() => {
                    if (this.map) {
                        this.map.invalidateSize();
                    }
                }, 300);
            },

            setMapTile(type) {
                this.currentTile = type;
                if (!this.map) return;
                if (type === 'satellite') {
                    if (this.osmLayer && this.map.hasLayer(this.osmLayer)) this.map.removeLayer(this.osmLayer);
                    if (this.satelliteLayer && !this.map.hasLayer(this.satelliteLayer)) this.satelliteLayer.addTo(this.map);
                } else {
                    if (this.satelliteLayer && this.map.hasLayer(this.satelliteLayer)) this.map.removeLayer(this.satelliteLayer);
                    if (this.osmLayer && !this.map.hasLayer(this.osmLayer)) this.osmLayer.addTo(this.map);
                }
                setTimeout(() => {
                    if (this.map) this.map.invalidateSize();
                }, 100);
            },

            setGeofenceMode(mode) {
                this.geofenceMode = mode;
                if (mode === 'polygon') {
                    if (this.circle && this.map.hasLayer(this.circle)) this.map.removeLayer(this.circle);
                    if (!this.polygonPoints || this.polygonPoints.length < 3) {
                        this.createBoxAround(this.lat, this.lng, 220, 180);
                    } else {
                        this.renderPolygon();
                    }
                } else {
                    if (this.polygonLayer && this.map.hasLayer(this.polygonLayer)) this.map.removeLayer(this.polygonLayer);
                    this.clearPolygonMarkers();
                    if (this.boxCenterMarker && this.map.hasLayer(this.boxCenterMarker)) {
                        this.map.removeLayer(this.boxCenterMarker);
                        this.boxCenterMarker = null;
                    }
                    if (this.circle && !this.map.hasLayer(this.circle)) this.circle.addTo(this.map);
                }
            },

            focusAndAlignToSmkn1() {
                this.lat = this.SMKN1_CENTER_LAT;
                this.lng = this.SMKN1_CENTER_LNG;
                this.setMapTile('satellite');
                if (this.map) {
                    this.map.flyTo([this.lat, this.lng], 18, { animate: true, duration: 1.2 });
                }
                this.updateMapFromInputs();
                this.setGeofenceMode('polygon');
                this.resetDefaultSmkn1Box();
                setTimeout(() => {
                    if (this.map) this.map.invalidateSize();
                }, 400);
            },

            createBoxAround(cLat, cLng, widthMeters = 220, heightMeters = 180) {
                const metersPerDegLat = 110574.0;
                const metersPerDegLng = 111320.0 * Math.cos(cLat * Math.PI / 180.0);

                const halfH = (heightMeters / 2.0) / metersPerDegLat;
                const halfW = (widthMeters / 2.0) / metersPerDegLng;

                // 4 Sudut Kotak: NW, NE, SE, SW
                this.polygonPoints = [
                    { lat: parseFloat((cLat + halfH).toFixed(7)), lng: parseFloat((cLng - halfW).toFixed(7)) },
                    { lat: parseFloat((cLat + halfH).toFixed(7)), lng: parseFloat((cLng + halfW).toFixed(7)) },
                    { lat: parseFloat((cLat - halfH).toFixed(7)), lng: parseFloat((cLng + halfW).toFixed(7)) },
                    { lat: parseFloat((cLat - halfH).toFixed(7)), lng: parseFloat((cLng - halfW).toFixed(7)) }
                ];

                this.renderPolygon();
            },

            resetDefaultSmkn1Box() {
                this.createBoxAround(this.SMKN1_CENTER_LAT, this.SMKN1_CENTER_LNG, 220, 180);
            },

            alignBoxToCurrentCenter() {
                if (!this.map) return;
                const center = this.map.getCenter();
                if (this.polygonPoints && this.polygonPoints.length >= 3) {
                    // Geser seluruh bentuk yang ada ke tengah layar
                    let sumLat = 0, sumLng = 0;
                    this.polygonPoints.forEach(p => { sumLat += p.lat; sumLng += p.lng; });
                    const oldCLat = sumLat / this.polygonPoints.length;
                    const oldCLng = sumLng / this.polygonPoints.length;
                    const dLat = center.lat - oldCLat;
                    const dLng = center.lng - oldCLng;

                    this.polygonPoints = this.polygonPoints.map(p => ({
                        lat: parseFloat((p.lat + dLat).toFixed(7)),
                        lng: parseFloat((p.lng + dLng).toFixed(7))
                    }));
                    this.renderPolygon();
                } else {
                    this.createBoxAround(center.lat, center.lng, 220, 180);
                }
            },

            /**
             * Tambah Sudut Baru (misal 4 jadi 5, 5 jadi 6, dst).
             * Menemukan sisi terpanjang dan menyisipkan titik baru di tengahnya.
             */
            addCornerPoint() {
                if (!this.polygonPoints || this.polygonPoints.length < 3) {
                    this.createBoxAround(this.lat, this.lng, 220, 180);
                    return;
                }

                const n = this.polygonPoints.length;
                let maxDist = -1;
                let maxIdx = 0;

                const metersPerDegLat = 110574.0;
                const metersPerDegLng = 111320.0 * Math.cos(this.polygonPoints[0].lat * Math.PI / 180.0);

                for (let i = 0; i < n; i++) {
                    const j = (i + 1) % n;
                    const dx = (this.polygonPoints[j].lng - this.polygonPoints[i].lng) * metersPerDegLng;
                    const dy = (this.polygonPoints[j].lat - this.polygonPoints[i].lat) * metersPerDegLat;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    if (dist > maxDist) {
                        maxDist = dist;
                        maxIdx = i;
                    }
                }

                const pA = this.polygonPoints[maxIdx];
                const pB = this.polygonPoints[(maxIdx + 1) % n];

                // Buat titik baru di tengah sisi terpanjang
                const newPoint = {
                    lat: parseFloat(((pA.lat + pB.lat) / 2.0).toFixed(7)),
                    lng: parseFloat(((pA.lng + pB.lng) / 2.0).toFixed(7))
                };

                this.polygonPoints.splice(maxIdx + 1, 0, newPoint);
                this.renderPolygon();
            },

            /**
             * Kurangi Sudut Terakhir (minimal 3 sudut).
             */
            removeLastCorner() {
                if (this.polygonPoints.length <= 3) {
                    alert('Minimal 3 sudut diperlukan untuk membentuk area tertutup pagar sekolah.');
                    return;
                }
                this.polygonPoints.pop();
                this.renderPolygon();
            },

            /**
             * Hapus Sudut Tertentu Berdasarkan Indeks.
             */
            removePointAt(index) {
                if (this.polygonPoints.length <= 3) {
                    alert('Minimal 3 sudut diperlukan untuk membentuk area tertutup pagar sekolah.');
                    return;
                }
                this.polygonPoints.splice(index, 1);
                this.renderPolygon();
            },

            /**
             * Sisipkan titik baru di dekat tepi garis poligon saat garis diklik.
             */
            insertPointNearEdge(lat, lng) {
                const n = this.polygonPoints.length;
                if (n < 3) return;

                const metersPerDegLat = 110574.0;
                const metersPerDegLng = 111320.0 * Math.cos(lat * Math.PI / 180.0);

                let minDist = Infinity;
                let bestIdx = 0;

                for (let i = 0; i < n; i++) {
                    const j = (i + 1) % n;
                    const p1 = this.polygonPoints[i];
                    const p2 = this.polygonPoints[j];

                    const x1 = (p1.lng - lng) * metersPerDegLng;
                    const y1 = (p1.lat - lat) * metersPerDegLat;
                    const x2 = (p2.lng - lng) * metersPerDegLng;
                    const y2 = (p2.lat - lat) * metersPerDegLat;

                    const dx = x2 - x1;
                    const dy = y2 - y1;
                    const lenSq = dx * dx + dy * dy;

                    let dist;
                    if (lenSq <= 0.0001) {
                        dist = Math.sqrt(x1 * x1 + y1 * y1);
                    } else {
                        const t = Math.max(0, Math.min(1, -(x1 * dx + y1 * dy) / lenSq));
                        const px = x1 + t * dx;
                        const py = y1 + t * dy;
                        dist = Math.sqrt(px * px + py * py);
                    }

                    if (dist < minDist) {
                        minDist = dist;
                        bestIdx = i;
                    }
                }

                // Sisipkan titik baru tepat di antara segmen terdekat
                this.polygonPoints.splice(bestIdx + 1, 0, {
                    lat: parseFloat(lat.toFixed(7)),
                    lng: parseFloat(lng.toFixed(7))
                });
                this.renderPolygon();
            },

            scaleBox(factor) {
                if (!this.polygonPoints || this.polygonPoints.length < 3) return;
                
                let sumLat = 0, sumLng = 0;
                this.polygonPoints.forEach(p => { sumLat += p.lat; sumLng += p.lng; });
                const cLat = sumLat / this.polygonPoints.length;
                const cLng = sumLng / this.polygonPoints.length;

                this.polygonPoints = this.polygonPoints.map(p => {
                    return {
                        lat: parseFloat((cLat + (p.lat - cLat) * factor).toFixed(7)),
                        lng: parseFloat((cLng + (p.lng - cLng) * factor).toFixed(7))
                    };
                });

                this.renderPolygon();
            },

            calculateBoxMetrics() {
                if (!this.polygonPoints || this.polygonPoints.length < 3) {
                    this.boxDimensions = '-';
                    this.boxAreaText = '-';
                    return;
                }

                const n = this.polygonPoints.length;

                // Hitung luas poligon umum menggunakan Shoelace Formula
                const metersPerDegLat = 110574.0;
                const metersPerDegLng = 111320.0 * Math.cos(this.polygonPoints[0].lat * Math.PI / 180.0);

                let areaM2 = 0;
                for (let i = 0; i < n; i++) {
                    const j = (i + 1) % n;
                    const xi = this.polygonPoints[i].lng * metersPerDegLng;
                    const yi = this.polygonPoints[i].lat * metersPerDegLat;
                    const xj = this.polygonPoints[j].lng * metersPerDegLng;
                    const yj = this.polygonPoints[j].lat * metersPerDegLat;
                    areaM2 += (xi * yj - xj * yi);
                }
                areaM2 = Math.round(Math.abs(areaM2) / 2.0);
                const ha = (areaM2 / 10000.0).toFixed(2);

                if (n === 4) {
                    const p0 = this.polygonPoints[0];
                    const p1 = this.polygonPoints[1];
                    const p2 = this.polygonPoints[2];
                    const width = Math.round(Math.abs(p1.lng - p0.lng) * metersPerDegLng);
                    const height = Math.round(Math.abs(p2.lat - p1.lat) * metersPerDegLat);
                    this.boxDimensions = `~${width}m × ${height}m (Kotak 4 Arah)`;
                } else {
                    this.boxDimensions = `${n} Titik Arah Pagar`;
                }
                this.boxAreaText = `~${ha} Ha (${areaM2.toLocaleString('id-ID')} m²)`;
            },

            clearPolygonMarkers() {
                this.polygonMarkers.forEach(m => {
                    if (this.map.hasLayer(m)) this.map.removeLayer(m);
                });
                this.polygonMarkers = [];
                if (this.boxCenterMarker && this.map.hasLayer(this.boxCenterMarker)) {
                    this.map.removeLayer(this.boxCenterMarker);
                    this.boxCenterMarker = null;
                }
            },

            renderPolygon() {
                if (!this.map) return;

                if (this.polygonLayer && this.map.hasLayer(this.polygonLayer)) {
                    this.map.removeLayer(this.polygonLayer);
                    this.polygonLayer = null;
                }
                this.clearPolygonMarkers();

                if (!this.polygonPoints || this.polygonPoints.length === 0) {
                    this.calculateBoxMetrics();
                    return;
                }

                const latlngs = this.polygonPoints.map(p => [p.lat, p.lng]);

                // Poligon Pagar Berwarna Emerald dengan garis tegas
                this.polygonLayer = L.polygon(latlngs, {
                    color: '#10b981',
                    weight: 4,
                    fillColor: '#10b981',
                    fillOpacity: 0.22,
                    dashArray: '6, 6'
                }).addTo(this.map);

                this.polygonLayer.bindTooltip('➕ Klik garis pagar untuk tambah titik arah baru', {
                    sticky: true,
                    opacity: 0.9
                });

                // Klik pada garis pagar untuk menyisipkan titik arah baru tepat di posisi yang diklik!
                this.polygonLayer.on('click', (e) => {
                    if (e.originalEvent) {
                        e.originalEvent.stopPropagation();
                    }
                    this.insertPointNearEdge(e.latlng.lat, e.latlng.lng);
                });

                // Hitung centroid / titik tengah area untuk Pin Geser
                let sumLat = 0, sumLng = 0;
                this.polygonPoints.forEach(p => { sumLat += p.lat; sumLng += p.lng; });
                const centerLat = sumLat / this.polygonPoints.length;
                const centerLng = sumLng / this.polygonPoints.length;

                // TAMPILKAN PIN "GESER AREA" (Bisa dipakai untuk 3, 4, 5, 6, 7+ titik arah)
                if (this.polygonPoints.length >= 3) {
                    const labelText = this.polygonPoints.length === 4 
                        ? 'GESER KOTAK (4 ARAH)' 
                        : 'GESER AREA (' + this.polygonPoints.length + ' ARAH)';

                    const centerIcon = L.divIcon({
                        html: `<div class="box-center-pin" style="background: linear-gradient(135deg, #0d9488, #10b981); color: white; padding: 5px 12px; border-radius: 9999px; font-size: 11px; font-weight: 800; border: 2.5px solid white; box-shadow: 0 4px 16px rgba(0,0,0,0.4); display: flex; align-items: center; gap: 6px; white-space: nowrap;">
                                <i class="fas fa-arrows-up-down-left-right text-xs"></i>
                                <span>${labelText}</span>
                               </div>`,
                        className: '',
                        iconSize: [this.polygonPoints.length === 4 ? 145 : 160, 30],
                        iconAnchor: [this.polygonPoints.length === 4 ? 72 : 80, 15]
                    });

                    this.boxCenterMarker = L.marker([centerLat, centerLng], {
                        icon: centerIcon,
                        draggable: true,
                        zIndexOffset: 1000
                    }).addTo(this.map);

                    let dragStartCenter = { lat: centerLat, lng: centerLng };
                    let initialPoints = JSON.parse(JSON.stringify(this.polygonPoints));

                    this.boxCenterMarker.on('dragstart', () => {
                        const cur = this.boxCenterMarker.getLatLng();
                        dragStartCenter = { lat: cur.lat, lng: cur.lng };
                        initialPoints = JSON.parse(JSON.stringify(this.polygonPoints));
                    });

                    // Saat pin tengah digeser, geser seluruh sudut poligon secara serentak!
                    this.boxCenterMarker.on('drag', (e) => {
                        const cur = e.target.getLatLng();
                        const dLat = cur.lat - dragStartCenter.lat;
                        const dLng = cur.lng - dragStartCenter.lng;

                        this.polygonPoints = initialPoints.map(p => ({
                            lat: parseFloat((p.lat + dLat).toFixed(7)),
                            lng: parseFloat((p.lng + dLng).toFixed(7))
                        }));

                        if (this.polygonLayer) {
                            this.polygonLayer.setLatLngs(this.polygonPoints.map(p => [p.lat, p.lng]));
                        }

                        // Update juga posisi pin sudut nomor
                        this.polygonMarkers.forEach((m, idx) => {
                            m.setLatLng([this.polygonPoints[idx].lat, this.polygonPoints[idx].lng]);
                        });
                    });

                    this.boxCenterMarker.on('dragend', () => {
                        this.renderPolygon();
                    });
                }

                // Tambahkan marker nomor pada setiap vertex sudut
                this.polygonPoints.forEach((p, idx) => {
                    const vertexIcon = L.divIcon({
                        html: `<div class="corner-pin" style="background-color:#0f766e;width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:11px;font-weight:bold;border:2.5px solid white;box-shadow:0 3px 8px rgba(0,0,0,0.35);">
                                ${idx + 1}
                               </div>`,
                        className: '',
                        iconSize: [26, 26],
                        iconAnchor: [13, 13]
                    });

                    const m = L.marker([p.lat, p.lng], {
                        icon: vertexIcon,
                        draggable: true
                    }).addTo(this.map);

                    // Popup untuk info & tombol hapus arah
                    const popupWrapper = document.createElement('div');
                    popupWrapper.className = 'text-center p-1';
                    popupWrapper.innerHTML = `
                        <div style="font-weight:bold;font-size:12px;color:#1e293b;margin-bottom:2px;">Titik Arah #${idx + 1}</div>
                        <div style="font-size:10px;font-family:monospace;color:#64748b;margin-bottom:8px;">${p.lat.toFixed(6)}, ${p.lng.toFixed(6)}</div>
                        ${this.polygonPoints.length > 3 ? `
                            <button type="button" id="btn-del-corner-${idx}" style="background:#ef4444;color:white;font-size:11px;font-weight:bold;padding:5px 10px;border-radius:6px;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas fa-trash-can"></i> Hapus Titik Arah Ini
                            </button>
                        ` : '<span style="font-size:10px;color:#d97706;font-weight:bold;">Minimal 3 titik arah pagar</span>'}
                    `;
                    m.bindPopup(popupWrapper);

                    m.on('popupopen', () => {
                        const btn = document.getElementById(`btn-del-corner-${idx}`);
                        if (btn) {
                            btn.onclick = () => {
                                m.closePopup();
                                this.removePointAt(idx);
                            };
                        }
                    });

                    m.on('drag', (evt) => {
                        const newPos = evt.target.getLatLng();
                        this.polygonPoints[idx] = {
                            lat: parseFloat(newPos.lat.toFixed(7)),
                            lng: parseFloat(newPos.lng.toFixed(7))
                        };
                        if (this.polygonLayer) {
                            this.polygonLayer.setLatLngs(this.polygonPoints.map(pt => [pt.lat, pt.lng]));
                        }
                    });

                    m.on('dragend', () => {
                        this.renderPolygon();
                    });

                    this.polygonMarkers.push(m);
                });

                this.calculateBoxMetrics();
            },

            updateMapFromInputs() {
                if (this.map && this.marker && this.circle) {
                    const newLatLng = new L.LatLng(this.lat, this.lng);
                    this.marker.setLatLng(newLatLng);
                    this.circle.setLatLng(newLatLng);
                    this.map.panTo(newLatLng);
                }
            },

            updateMapCircle() {
                if (this.circle) {
                    this.circle.setRadius(this.radius);
                }
            },

            addCurrentIp() {
                if (!this.clientIp) return;
                const ip = this.clientIp.trim();
                let current = this.allowedIps ? this.allowedIps.trim() : '';
                if (current.includes(ip)) {
                    alert('IP (' + ip + ') sudah ada di dalam daftar whitelist.');
                    return;
                }
                this.allowedIps = current ? current + '\n' + ip : ip;
            },

            getCurrentLocation() {
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung oleh browser Anda.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.lat = parseFloat(position.coords.latitude.toFixed(7));
                        this.lng = parseFloat(position.coords.longitude.toFixed(7));
                        this.updateMapFromInputs();
                        this.map.setView([this.lat, this.lng], 18);
                    },
                    (error) => {
                        alert('Gagal mengambil lokasi GPS: ' + error.message);
                    },
                    { enableHighAccuracy: true }
                );
            }
        };
    }
</script>
@endpush

@endsection
