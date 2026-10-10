<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh akun dan antrian verifikasi akun mandiri.
     */
    public function index(Request $request): View
    {
        $roleFilter = $request->string('role')->toString();
        $statusFilter = $request->string('status')->toString();
        $search = $request->string('search')->toString();

        $query = User::query()->latest();

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

        $pendingCount = User::where('status_akun', 'pending')->count();

        return view('admin.users.index', compact('users', 'roleFilter', 'statusFilter', 'search', 'pendingCount'));
    }

    /**
     * Form tambah akun langsung oleh admin.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Simpan akun baru yang dibuat langsung oleh admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:petugas,pengguna,admin'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status_akun' => 'terverifikasi',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Akun {$validated['role']} untuk {$validated['name']} berhasil didaftarkan langsung oleh Admin.");
    }

    /**
     * Setujui / verifikasi akun registrasi mandiri.
     */
    public function verify(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $user->update([
            'status_akun' => 'terverifikasi',
        ]);

        return back()->with('success', "Akun pengguna '{$user->name}' ({$user->email}) telah berhasil diverifikasi dan kini dapat login.");
    }

    /**
     * Tolak akun registrasi mandiri.
     */
    public function reject(string $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Akun registrasi mandiri '{$name}' telah ditolak dan dihapus dari sistem.");
    }
}
