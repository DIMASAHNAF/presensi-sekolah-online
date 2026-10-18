@extends('layouts.dashboard')

@section('title', 'Pengaturan Lokasi & Radius')
@section('page-title', 'Pengaturan Lokasi & Radius')
@section('page-subtitle', 'Tentukan titik koordinat sekolah dan batas radius presensi siswa')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map-preview {
        height: 480px;
        width: 100%;
        border-radius: 1.25rem;
        z-index: 10;
    }
    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <span>Ada error input: {{ $errors->first() }}</span>
    </div>
@endif

@if(session('success'))
    <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-4 py-3 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check text-emerald-500"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-data="lokasiApp()">
    
    {{-- Form Pengaturan --}}
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center gap-3 pb-4 mb-5 border-b border-slate-100">
                <div class="w-10 h-10 rounded-2xl bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold">
                    <i class="fas fa-map-location-dot text-lg"></i>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-base">Parameter Geofencing</h2>
                    <p class="text-xs text-slate-400">Atur batas area presensi siswa</p>
                </div>
            </div>

            <form action="{{ route('dashboard.lokasi.update') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Status Geofencing Toggle --}}
                <div class="p-4 rounded-2xl border transition-all"
                     :class="isActive ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200'">
                    <label class="flex items-center justify-between cursor-pointer">
                        <div>
                            <span class="font-bold text-sm text-slate-800 flex items-center gap-2">
                                <i class="fas fa-shield-halved" :class="isActive ? 'text-emerald-600' : 'text-slate-400'"></i>
                                Validasi Geofencing GPS
                            </span>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Kunci presensi hanya untuk siswa di dalam radius
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

                {{-- Koordinat Lat & Lng --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Latitude</label>
                        <input type="number" step="any" name="latitude" x-model.number="lat" @input="updateMapFromInputs()" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5 uppercase tracking-wider">Longitude</label>
                        <input type="number" step="any" name="longitude" x-model.number="lng" @input="updateMapFromInputs()" required
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                </div>

                {{-- Mode Geofencing: Radius vs Polygon --}}
                <div class="space-y-2">
                    <label class="block text-xs font-semibold text-slate-600 uppercase tracking-wider">Metode Batas Wilayah (Geofence Mode)</label>
                    <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 rounded-xl">
                        <button type="button" @click="setGeofenceMode('radius')"
                                :class="geofenceMode === 'radius' ? 'bg-white text-teal-700 shadow-xs font-bold' : 'text-slate-600 font-medium hover:text-slate-900'"
                                class="py-2 text-xs rounded-lg transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-circle-notch"></i>
                            <span>Radius Lingkaran</span>
                        </button>
                        <button type="button" @click="setGeofenceMode('polygon')"
                                :class="geofenceMode === 'polygon' ? 'bg-white text-teal-700 shadow-xs font-bold' : 'text-slate-600 font-medium hover:text-slate-900'"
                                class="py-2 text-xs rounded-lg transition flex items-center justify-center gap-1.5">
                            <i class="fas fa-draw-polygon"></i>
                            <span>Polygon Pagar (Presisi)</span>
                        </button>
                    </div>
                    <input type="hidden" name="geofence_mode" :value="geofenceMode">
                    <input type="hidden" name="polygon_coordinates" :value="JSON.stringify(polygonPoints)">
                </div>

                {{-- Mode 1: Radius Meters (Tampil jika mode = radius) --}}
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
                        <span>100 m (Standar)</span>
                        <span>500 m</span>
                        <span>1.000 m</span>
                    </div>
                    <div class="mt-2">
                        <input type="number" min="10" max="5000" name="radius_meters" x-model.number="radius" @input="updateMapCircle()"
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500 transition">
                    </div>
                </div>

                {{-- Mode 2: Polygon Manager (Tampil jika mode = polygon) --}}
                <div x-show="geofenceMode === 'polygon'" class="space-y-3 bg-teal-50/50 border border-teal-200/80 rounded-2xl p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs">
                                <i class="fas fa-draw-polygon"></i>
                            </span>
                            <span class="text-xs font-bold text-slate-800">Area Polygon Sekolah</span>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-md"
                              :class="polygonPoints.length >= 3 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'">
                            <span x-text="polygonPoints.length"></span> Titik Batas
                        </span>
                    </div>
                    
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Klik langsung pada peta sebelah kanan untuk menambahkan titik sudut pagar sekolah (minimal 3 titik untuk membentuk polygon tertutup).
                    </p>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="button" @click="undoLastPoint()" :disabled="polygonPoints.length === 0"
                                class="text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 disabled:opacity-50 transition flex items-center gap-1">
                            <i class="fas fa-rotate-left text-slate-400"></i> Undo Titik
                        </button>
                        <button type="button" @click="clearPolygon()" :disabled="polygonPoints.length === 0"
                                class="text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-white border border-red-200 text-red-600 hover:bg-red-50 disabled:opacity-50 transition flex items-center gap-1">
                            <i class="fas fa-trash-can"></i> Hapus Poligon
                        </button>
                        <button type="button" @click="loadDefaultSchoolPolygon()"
                                class="text-xs font-semibold px-2.5 py-1.5 rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition flex items-center gap-1 shadow-2xs">
                            <i class="fas fa-wand-magic-sparkles"></i> Contoh Poligon SMKN 1
                        </button>
                    </div>

                    {{-- Vertex list display --}}
                    <div x-show="polygonPoints.length > 0" class="max-h-24 overflow-y-auto space-y-1 pr-1">
                        <template x-for="(pt, idx) in polygonPoints" :key="idx">
                            <div class="flex items-center justify-between text-[10px] font-mono bg-white px-2 py-1 rounded-md border border-slate-200">
                                <span class="text-slate-500 font-bold" x-text="'P' + (idx + 1) + ':'"></span>
                                <span class="text-slate-700" x-text="Number(pt.lat).toFixed(6) + ', ' + Number(pt.lng).toFixed(6)"></span>
                                <button type="button" @click="removePointAt(idx)" class="text-red-500 hover:text-red-700 font-bold px-1">✕</button>
                            </div>
                        </template>
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
                            <p class="text-[11px] text-slate-400">Batasi presensi hanya dari WiFi resmi sekolah</p>
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
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
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
                        <p class="text-[11px] text-slate-500 mt-1 leading-normal">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i> Jika router WiFi sekolah di-restart dan provider (Indihome/Biznet) memberikan IP baru, cukup klik tombol <strong>Tambahkan IP Ini</strong> lalu simpan.
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="pt-3 space-y-2">
                    <button type="button" @click="getCurrentLocation()"
                            class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center gap-2">
                        <i class="fas fa-crosshairs text-teal-600"></i> Ambil Titik GPS Saya Saat Ini
                    </button>

                    <button type="submit" class="btn-primary w-full justify-center py-3 text-sm">
                        <i class="fas fa-floppy-disk mr-1"></i> Simpan Semua Pengaturan
                    </button>
                </div>
            </form>
        </div>

        {{-- Petunjuk Penggunaan --}}
        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 rounded-xl p-5 text-white shadow-sm border border-slate-800">
            <div class="flex items-center gap-2 text-blue-300 font-heading font-bold text-sm mb-3">
                <i class="fas fa-lightbulb"></i> Panduan Kalibrasi & Keamanan Jaringan
            </div>
            <ul class="text-xs text-slate-300 space-y-2.5 leading-relaxed">
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-blue-400 mt-0.5 shrink-0"></i>
                    <span><strong>Geser Pin di Peta:</strong> Klik atau geser marker di peta sebelah kanan untuk menentukan titik pusat gerbang SMKN 1 Beringin.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-blue-400 mt-0.5 shrink-0"></i>
                    <span><strong>Rekomendasi Radius 80–150 meter:</strong> Di dalam gedung/kelas, akurasi GPS HP memiliki toleransi 15–30 meter. Radius 100m adalah titik ideal untuk mencegah false-lock.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-check text-blue-400 mt-0.5 shrink-0"></i>
                    <span><strong>Mode PJJ / Luar Sekolah:</strong> Nonaktifkan toggle di atas jika sekolah sedang mengadakan pembelajaran daring (PJJ) atau kunjungan industri.</span>
                </li>
                <li class="flex items-start gap-2">
                    <i class="fas fa-shield-halved text-emerald-400 mt-0.5 shrink-0"></i>
                    <span><strong>Proteksi Ganda (GPS + WiFi):</strong> Aktifkan kedua validasi untuk memastikan siswa hadir fisik di sekolah dan benar-benar terhubung ke WiFi sekolah.</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Map Display --}}
    <div class="lg:col-span-7">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 h-full flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-base flex items-center gap-2">
                        <span>Peta Area Presensi</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="isActive ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                              x-text="isActive ? 'Geofencing Aktif' : 'Geofencing Nonaktif'"></span>
                    </h3>
                    <p class="text-xs text-slate-400">Lingkaran hijau adalah zona aman siswa diperbolehkan scan presensi</p>
                </div>
                
                <div class="flex items-center gap-2">
                    {{-- Switcher Tipe Peta: Jalanan vs Satelit --}}
                    <div class="inline-flex rounded-xl p-0.5 bg-slate-100 border border-slate-200 text-xs font-semibold">
                        <button type="button" @click="setMapTile('osm')"
                                :class="currentTile === 'osm' ? 'bg-white text-teal-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                                class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <i class="fas fa-map"></i>
                            <span>Peta Jalan</span>
                        </button>
                        <button type="button" @click="setMapTile('satellite')"
                                :class="currentTile === 'satellite' ? 'bg-white text-teal-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900'"
                                class="px-2.5 py-1.5 rounded-lg transition flex items-center gap-1.5">
                            <i class="fas fa-satellite"></i>
                            <span>Foto Satelit</span>
                        </button>
                    </div>

                    <a :href="'https://www.google.com/maps?q=' + lat + ',' + lng" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs text-teal-600 hover:text-teal-700 bg-teal-50 hover:bg-teal-100 px-3 py-1.5 rounded-xl font-medium transition">
                        <i class="fab fa-google"></i> Google Maps
                    </a>
                </div>
            </div>

            {{-- Container Map --}}
            <div class="relative flex-1 min-h-[480px]">
                <div id="map-preview"></div>
                <div class="absolute bottom-4 left-4 z-[400] bg-white/95 backdrop-blur-sm px-3.5 py-2 rounded-xl shadow-lg border border-slate-200 text-xs text-slate-700 font-mono flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-teal-500 animate-ping"></span>
                    <span>Lat: <strong x-text="lat.toFixed(6)"></strong> | Lng: <strong x-text="lng.toFixed(6)"></strong></span>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""></script>
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
            geofenceMode: '{{ $setting->geofence_mode ?? "radius" }}',
            polygonPoints: @json($setting->polygon_coordinates ?? []),
            map: null,
            marker: null,
            circle: null,
            polygonLayer: null,
            polygonMarkers: [],
            currentTile: 'osm',
            osmLayer: null,
            satelliteLayer: null,

            setMapTile(type) {
                this.currentTile = type;
                if (!this.map) return;
                if (type === 'satellite') {
                    if (this.osmLayer) this.map.removeLayer(this.osmLayer);
                    if (!this.satelliteLayer) {
                        this.satelliteLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                            maxZoom: 20,
                            attribution: '© Google Maps Satellite'
                        });
                    }
                    this.satelliteLayer.addTo(this.map);
                } else {
                    if (this.satelliteLayer) this.map.removeLayer(this.satelliteLayer);
                    if (this.osmLayer) this.osmLayer.addTo(this.map);
                }
            },

            setGeofenceMode(mode) {
                this.geofenceMode = mode;
                if (mode === 'polygon') {
                    if (this.circle) this.map.removeLayer(this.circle);
                    this.renderPolygon();
                } else {
                    if (this.polygonLayer) this.map.removeLayer(this.polygonLayer);
                    this.clearPolygonMarkers();
                    if (this.circle) this.circle.addTo(this.map);
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

            init() {
                this.$nextTick(() => {
                    this.initMap();
                });
            },

            initMap() {
                // Inisialisasi peta Leaflet
                this.map = L.map('map-preview').setView([this.lat, this.lng], 17);

                this.osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(this.map);

                // Buat custom marker icon untuk pusat sekolah
                const customIcon = L.divIcon({
                    html: `<div style="background-color:#0d9488;width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;box-shadow:0 4px 12px rgba(13,148,136,0.5);border:2px solid white;">
                            <i class="fas fa-school" style="font-size:14px;"></i>
                           </div>`,
                    className: '',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                // Buat marker draggable untuk titik koordinat pusat
                this.marker = L.marker([this.lat, this.lng], {
                    draggable: true,
                    icon: customIcon
                }).addTo(this.map);

                // Tambahkan lingkaran radius
                this.circle = L.circle([this.lat, this.lng], {
                    color: '#0d9488',
                    fillColor: '#14b8a6',
                    fillOpacity: 0.2,
                    radius: this.radius
                });

                if (this.geofenceMode === 'radius') {
                    this.circle.addTo(this.map);
                }

                // Render Polygon jika ada data sebelumnya
                if (this.geofenceMode === 'polygon' && this.polygonPoints && this.polygonPoints.length > 0) {
                    this.renderPolygon();
                }

                // Event listener saat marker digeser
                this.marker.on('dragend', (event) => {
                    const position = event.target.getLatLng();
                    this.lat = position.lat;
                    this.lng = position.lng;
                    this.circle.setLatLng(position);
                });

                // Event listener saat peta diklik
                this.map.on('click', (e) => {
                    if (this.geofenceMode === 'polygon') {
                        // Tambahkan titik sudut polygon
                        this.addPolygonPoint(e.latlng.lat, e.latlng.lng);
                    } else {
                        // Mode radius: ubah titik pusat
                        this.lat = e.latlng.lat;
                        this.lng = e.latlng.lng;
                        this.marker.setLatLng(e.latlng);
                        this.circle.setLatLng(e.latlng);
                    }
                });
            },

            addPolygonPoint(lat, lng) {
                this.polygonPoints.push({
                    lat: parseFloat(lat.toFixed(7)),
                    lng: parseFloat(lng.toFixed(7))
                });
                this.renderPolygon();
            },

            undoLastPoint() {
                if (this.polygonPoints.length > 0) {
                    this.polygonPoints.pop();
                    this.renderPolygon();
                }
            },

            clearPolygon() {
                if (confirm('Yakin ingin mengosongkan seluruh titik polygon?')) {
                    this.polygonPoints = [];
                    this.renderPolygon();
                }
            },

            removePointAt(index) {
                this.polygonPoints.splice(index, 1);
                this.renderPolygon();
            },

            loadDefaultSchoolPolygon() {
                // Polygon referensi SMKN 1 Beringin (4 sudut area kampus)
                const cLat = this.lat;
                const cLng = this.lng;
                const deltaLat = 0.0009;
                const deltaLng = 0.0011;

                this.polygonPoints = [
                    { lat: parseFloat((cLat + deltaLat).toFixed(7)), lng: parseFloat((cLng - deltaLng).toFixed(7)) },
                    { lat: parseFloat((cLat + deltaLat).toFixed(7)), lng: parseFloat((cLng + deltaLng).toFixed(7)) },
                    { lat: parseFloat((cLat - deltaLat).toFixed(7)), lng: parseFloat((cLng + deltaLng).toFixed(7)) },
                    { lat: parseFloat((cLat - deltaLat).toFixed(7)), lng: parseFloat((cLng - deltaLng).toFixed(7)) }
                ];
                this.renderPolygon();
            },

            clearPolygonMarkers() {
                this.polygonMarkers.forEach(m => this.map.removeLayer(m));
                this.polygonMarkers = [];
            },

            renderPolygon() {
                if (!this.map) return;

                if (this.polygonLayer) {
                    this.map.removeLayer(this.polygonLayer);
                    this.polygonLayer = null;
                }
                this.clearPolygonMarkers();

                if (!this.polygonPoints || this.polygonPoints.length === 0) return;

                const latlngs = this.polygonPoints.map(p => [p.lat, p.lng]);

                // Gambar poligon tertutup
                this.polygonLayer = L.polygon(latlngs, {
                    color: '#0d9488',
                    weight: 3,
                    fillColor: '#14b8a6',
                    fillOpacity: 0.25,
                    dashArray: '4, 6'
                }).addTo(this.map);

                // Tambahkan marker kecil pada setiap vertex sudut
                this.polygonPoints.forEach((p, idx) => {
                    const vertexIcon = L.divIcon({
                        html: `<div style="background-color:#0f766e;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:9px;font-weight:bold;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.3);">
                                ${idx + 1}
                               </div>`,
                        className: '',
                        iconSize: [18, 18],
                        iconAnchor: [9, 9]
                    });

                    const m = L.marker([p.lat, p.lng], {
                        icon: vertexIcon,
                        draggable: true
                    }).addTo(this.map);

                    m.on('dragend', (evt) => {
                        const newPos = evt.target.getLatLng();
                        this.polygonPoints[idx] = {
                            lat: parseFloat(newPos.lat.toFixed(7)),
                            lng: parseFloat(newPos.lng.toFixed(7))
                        };
                        this.renderPolygon();
                    });

                    this.polygonMarkers.push(m);
                });
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

            getCurrentLocation() {
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung oleh browser Anda.');
                    return;
                }

                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.lat = position.coords.latitude;
                        this.lng = position.coords.longitude;
                        this.updateMapFromInputs();
                        this.map.setView([this.lat, this.lng], 17);
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
