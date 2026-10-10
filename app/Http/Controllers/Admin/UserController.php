<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Daftar semua akun dengan filter role dan status_akun.
     */
    public function index(Request $request): View
    {
        $roleFilter = $request->string('role')->toString();
        $statusFilter = $request->string('status_akun')->toString();
        $search = $request->string('search')->toString();

        $query = User::latest();

        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter !== '') {
            $query->where('status_akun', $statusFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $pendingUsers = User::where('role', 'pengguna')
            ->where('status_akun', 'pending')
            ->latest()
            ->get();

        $counts = [
            'total' => User::count(),
            'pending' => User::where('status_akun', 'pending')->count(),
            'terverifikasi' => User::where('status_akun', 'terverifikasi')->count(),
            'ditolak' => User::where('status_akun', 'ditolak')->count(),
        ];

        return view('admin.users.index', compact(
            'users',
            'pendingUsers',
            'counts',
            'roleFilter',
            'statusFilter',
            'search'
        ));
    }

    /**
     * Form buat akun baru oleh admin.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Simpan akun baru, langsung terverifikasi.
     * Admin hanya dapat membuat akun role 'pengguna' atau 'petugas'.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['pengguna', 'petugas'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status_akun' => 'terverifikasi',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$validated['name']} ({$validated['role']}) berhasil dibuat dan langsung aktif.");
    }

    /**
     * Verifikasi akun pengguna (set status_akun ke 'terverifikasi').
     */
    public function verify(Request $request, User $user): RedirectResponse
    {
        if ($user->status_akun !== 'pending') {
            return back()->with('error', 'Hanya akun berstatus pending yang dapat diverifikasi.');
        }

        $user->update(['status_akun' => 'terverifikasi']);

        return back()->with('success', "Akun {$user->name} ({$user->email}) berhasil diverifikasi.");
    }

    /**
     * Tolak akun pengguna (set status_akun ke 'ditolak').
     */
    public function reject(Request $request, User $user): RedirectResponse
    {
        if ($user->status_akun !== 'pending') {
            return back()->with('error', 'Hanya akun berstatus pending yang dapat ditolak.');
        }

        $user->update(['status_akun' => 'ditolak']);

        return back()->with('success', "Akun {$user->name} ({$user->email}) telah ditolak.");
    }
}
