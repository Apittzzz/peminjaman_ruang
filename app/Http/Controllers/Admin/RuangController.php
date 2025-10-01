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
        ]);

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
        $request->validate([
            'nama_ruang' => 'required|string|max:255|unique:ruang,nama_ruang,' . $ruang->id_ruang . ',id_ruang',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:kosong,dipakai',
        ]);

        $ruang->update($request->all());

        return redirect()->route('admin.ruang.index')->with('success', 'Ruang berhasil diperbarui.');
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