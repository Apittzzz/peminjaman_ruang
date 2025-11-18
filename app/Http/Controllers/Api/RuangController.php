<?php

namespace App\Http\Controllers\Api;

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

        return response()->json([
            'success' => true,
            'data' => $ruangs
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only admin can create rooms
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'nama_ruang' => 'required|string|max:255',
            'kapasitas' => 'required|integer|min:1',
            'status' => 'required|in:kosong,dipakai'
        ]);

        $ruang = Ruang::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruang berhasil ditambahkan',
            'data' => $ruang
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Ruang $ruang)
    {
        return response()->json([
            'success' => true,
            'data' => $ruang
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ruang $ruang)
    {
        // Only admin can update rooms
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'nama_ruang' => 'sometimes|string|max:255',
            'kapasitas' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:kosong,dipakai'
        ]);

        $ruang->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Ruang berhasil diupdate',
            'data' => $ruang
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ruang $ruang)
    {
        // Only admin can delete rooms
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $ruang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ruang berhasil dihapus'
        ]);
    }
}