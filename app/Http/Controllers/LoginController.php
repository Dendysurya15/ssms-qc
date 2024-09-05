<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pengguna;

class LoginController extends Controller
{
    //
    public function index(Request $request)
    {
        return view('Admin.index');
    }
    // public function authenticate(Request $request)
    // {
    //     $this->validate($request, [
    //         'email_or_nama_lengkap' => 'required',
    //         'password' => 'required'
    //     ]);

    //     $user = DB::table('pengguna')
    //         ->where('email', $request->email_or_nama_lengkap)
    //         ->orWhere('nama_lengkap', $request->email_or_nama_lengkap)
    //         ->first();

    //     if (!$user) {
    //         return back()->with('error', 'Email atau Password Salah');
    //     }

    //     if ($request->password != $user->password) {
    //         return back()->with('error', 'Email atau Password Salah');
    //     }

    //     session([
    //         'user_id' => $user->user_id,
    //         'user_name' => $user->nama_lengkap,
    //         'departemen' => $user->departemen,
    //         'lok' => $user->lokasi_kerja,
    //         'jabatan' => $user->jabatan,
    //     ]);
    //     // dd(session());

    //     return redirect()->intended('rekap');
    // }
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email_or_nama_lengkap' => 'required',
            'password' => 'required'
        ]);


        // Retrieve the user based on the email
        $pengguna = Pengguna::where('email', $request->email_or_nama_lengkap)->first();

        // dd($pengguna);
        // Check if the user exists and the password is correct
        if (!$pengguna || $request->password != $pengguna->password) {
            return back()->with('error', 'Email atau Password Salah');
        }

        // Store user details in session
        session([
            'user_id' => $pengguna->user_id,
            'user_name' => $pengguna->nama_lengkap,
            'departemen' => $pengguna->departemen,
            'lok' => $pengguna->lokasi_kerja,
            'jabatan' => $pengguna->jabatan,
        ]);

        // Login the user using their email
        auth()->login($pengguna);

        // Check if the user is authorized to access the 'rekap' route
        if (!auth()->check()) {
            // Redirect the user back with an error message if not authorized
            return back()->with('error', 'Unauthorized access');
        }

        // Redirect the user to the intended route after successful login
        return redirect()->intended(route('rekap'));
        // return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
        // $request->session()->flush();
        // return redirect('/');
    }
}
