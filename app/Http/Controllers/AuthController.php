<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Session;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            session([
                'user_id' => $user->id,
                'nik' => $user->nik,
                'user_name' => $user->name,
                'role' => $user->role,
                'division' => $user->division,
                'photo' => $user->photo,
            ]);  
            return redirect('/dashboard');
        }
        return back()->with('error', 'Email atau password salah');
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'division' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $latestNik = User::orderBy('nik', 'desc')->value('nik');

        if ($latestNik) {
            $newNik = strval((int)$latestNik + 1);
        } else {
            $newNik = '101201';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 0,
            'photo' => $photoPath,
            'division' => $request->division,
            'nik' => $newNik,
        ]);

        session([
            'user_id' => $user->id,
            'nik' => $user->nik,
            'user_name' => $user->name,
            'role' => $user->role,
            'division' => $user->division,
            'photo' => $user->photo,
        ]);

        return redirect('/dashboard')->with('success', 'Pendaftaran berhasil. Selamat datang!');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/login');
    }
}

