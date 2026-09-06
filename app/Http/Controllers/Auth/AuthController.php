<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\User;
use App\Services\FaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ==== HALAMAN PILIHAN ====
    public function chooseRole()
    {
        return view('auth.index'); // halaman gabungan siswa & guru
    }

    // ==== LOGIN SISWA ====
    public function showLoginSiswa()
    {
        return view('auth.index', ['initialTab' => 'siswa']);
    }

    public function loginSiswa(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'], // username / email / NISN
            'password' => ['required', 'string'],
        ], [
            'identifier.required' => 'Username, email, atau NISN wajib diisi.',
        ]);

        $login = $request->input('identifier');

        // Deteksi field berdasarkan format input
        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : (ctype_digit($login) ? 'nisn' : 'username');

        $user = User::where($field, $login)->where('role', 'siswa')->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'identifier' => 'Username/Email/NISN atau password salah.',
            ])->onlyInput('identifier');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('siswa.dashboard'));
    }

    // ==== LOGIN GURU ====
    public function showLoginGuru()
    {
        return view('auth.index', ['initialTab' => 'guru']);
    }

    public function loginGuru(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Accept both guru and admin (both use NIK)
        $user = User::where('nik', $request->nik)
            ->whereIn('role', ['guru', 'admin'])
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'nik' => 'NIK atau password salah.',
            ])->onlyInput('nik');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // ==== REGISTER SISWA (PUBLIC) ====
    public function showRegisterSiswa()
    {
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();

        return view('auth.register-siswa', compact('kelas'));
    }

    /**
     * Step 1 — simpan data siswa ke session lalu arahkan ke step face scan.
     * Step 2 — terima foto wajah, enroll via Python, buat akun, login.
     */
    public function registerSiswa(Request $request)
    {
        // ── STEP 2: Proses foto wajah + buat akun ──────────────────────────
        if ($request->has('face_images')) {
            // Ambil data yang sudah disimpan di session dari step 1
            $data = $request->session()->get('register_data');
            if (!$data) {
                return redirect()->route('register.siswa')
                    ->withErrors(['general' => 'Session habis. Silakan ulangi pendaftaran.']);
            }

            $images = $request->input('face_images'); // JSON string array base64
            try {
                $imagesArray = json_decode($images, true);
            } catch (\Throwable $e) {
                return back()->withErrors(['face' => 'Data gambar tidak valid.']);
            }

            if (!is_array($imagesArray) || count($imagesArray) < 3) {
                return back()->withErrors(['face' => 'Minimal 3 foto wajah diperlukan. Silakan ulangi scan.']);
            }

            // Panggil Python untuk enroll (rata-rata descriptor dari multi-angle)
            $faceService = new FaceService();
            $result      = $faceService->enroll($imagesArray);

            if (!$result['success']) {
                return back()->withErrors([
                    'face' => 'Gagal mendaftarkan wajah: ' . ($result['error'] ?? 'Coba foto ulang dengan pencahayaan lebih baik.'),
                ]);
            }

            // Buat akun user
            $user = User::create([
                'name'             => $data['name'],
                'username'         => $data['username'],
                'email'            => $data['email'] ?? null,
                'nisn'             => $data['nisn'],
                'kelas_id'         => $data['kelas_id'],
                'role'             => 'siswa',
                'password'         => Hash::make($data['password']),
                'face_descriptor'  => $result['descriptor'],
                'face_enrolled_at' => now(),
            ]);

            // Sinkronkan otomatis ke sesi presensi yang sudah dibuat hari ini untuk kelas ini
            $sesiHariIni = SesiPresensi::where('kelas_id', $user->kelas_id)
                ->where('tanggal', today())
                ->get();

            foreach ($sesiHariIni as $sesi) {
                $exists = Presensi::where('sesi_presensi_id', $sesi->id)
                    ->where('siswa_id', $user->id)
                    ->exists();

                if (!$exists) {
                    Presensi::create([
                        'sesi_presensi_id' => $sesi->id,
                        'siswa_id'         => $user->id,
                        'status'           => 'alpa',
                        'keterangan'       => 'Siswa baru terdaftar',
                    ]);
                }
            }

            $request->session()->forget('register_data');
            Auth::login($user);

            return redirect()->route('siswa.dashboard')
                ->with('success', 'Registrasi berhasil! Wajah Anda telah terdaftar. 👋');
        }

        // ── STEP 1: Validasi data, simpan ke session, redirect ke step scan wajah ─
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email'    => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'nisn'     => ['required', 'string', 'digits:10', 'unique:users,nisn'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'nisn.digits'         => 'NISN harus terdiri dari 10 digit angka.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan underscore.',
        ]);

        // Simpan data form ke session (belum buat user dulu — tunggu wajah berhasil)
        $request->session()->put('register_data', [
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'] ?? null,
            'nisn'     => $validated['nisn'],
            'kelas_id' => $validated['kelas_id'],
            'password' => $validated['password'], // plain — di-hash nanti saat buat user
        ]);

        // Redirect ke halaman yang sama dengan flag step=2 (scan wajah)
        return redirect()->route('register.siswa')->with('step', 2);
    }

    // ==== LOGOUT ====
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('choose-role');
    }
}
