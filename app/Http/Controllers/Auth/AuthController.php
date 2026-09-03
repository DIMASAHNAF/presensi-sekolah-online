<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
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
        return view('auth.login-siswa');
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
        return view('auth.login-guru');
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

    public function registerSiswa(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'nisn' => ['required', 'string', 'digits:10', 'unique:users,nisn'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'nisn.digits' => 'NISN harus terdiri dari 10 digit angka.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan underscore.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'nisn' => $validated['nisn'],
            'kelas_id' => $validated['kelas_id'],
            'role' => 'siswa',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('siswa.dashboard')->with('success', 'Registrasi berhasil!');
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
