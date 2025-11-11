<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ruang;
use Illuminate\Http\Request;

class RuangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ruangs = Ruang::all();
        return view('admin.ruang.index', compact('ruangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ruang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruang' => 'required|string|max:255|unique:ruang',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:kosong,dipakai',
            'pengguna_default' => 'nullable|string|max:255',
            'keterangan_penggunaan' => 'nullable|string|max:1000',
        ]);

        // Jika status dipakai, pengguna_default wajib diisi
        if ($request->status === 'dipakai' && empty($request->pengguna_default)) {
            return back()->withErrors(['pengguna_default' => 'Pengguna default wajib diisi ketika status ruangan adalah "dipakai".'])->withInput();
        }

        Ruang::create($request->all());

        return redirect()->route('admin.ruang.index')->with('success', 'Ruang berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruang $ruang)
    {
        return view('admin.ruang.show', compact('ruang'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ruang $ruang)
    {
        return view('admin.ruang.edit', compact('ruang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruang $ruang)
    {
        // Validasi kondisional berdasarkan field yang dikirim
        $rules = [];

        if ($request->has('nama_ruang')) {
            $rules['nama_ruang'] = 'required|string|max:255|unique:ruang,nama_ruang,' . $ruang->id_ruang . ',id_ruang';
        }

        if ($request->has('kapasitas')) {
            $rules['kapasitas'] = 'required|integer|min:1';
        }

        if ($request->has('status')) {
            $rules['status'] = 'required|in:kosong,dipakai';
        }

        if ($request->has('pengguna_default')) {
            $rules['pengguna_default'] = 'nullable|string|max:255';
        }

        if ($request->has('keterangan_penggunaan')) {
            $rules['keterangan_penggunaan'] = 'nullable|string|max:1000';
        }

        $request->validate($rules);

        // Jika status dipakai, pengguna_default wajib diisi
        if ($request->status === 'dipakai' && empty($request->pengguna_default)) {
            return back()->withErrors(['pengguna_default' => 'Pengguna default wajib diisi ketika status ruangan adalah "dipakai".'])->withInput();
        }

        $ruang->update($request->only(['nama_ruang', 'kapasitas', 'status', 'pengguna_default', 'keterangan_penggunaan']));

        // Redirect berdasarkan dari mana request berasal
        if ($request->has('nama_ruang')) {
            // Dari form edit lengkap
            return redirect()->route('admin.ruang.index')->with('success', 'Ruang berhasil diperbarui.');
        } else {
            // Dari modal jadwal
            return redirect()->route('jadwal.index')->with('success', 'Pengguna default ruangan berhasil diperbarui.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruang $ruang)
    {
        // Cek apakah ruang sedang dipinjam
        if ($ruang->peminjaman()->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->route('admin.ruang.index')->with('error', 'Tidak dapat menghapus ruang yang sedang dipinjam.');
        }

        $ruang->delete();
        return redirect()->route('admin.ruang.index')->with('success', 'Ruang berhasil dihapus.');
    }
}