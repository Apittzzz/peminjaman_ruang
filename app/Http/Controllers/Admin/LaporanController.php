<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // TODO: Implement laporan index
        return view('admin.laporan.index');
    }

    public function generate(Request $request)
    {
        // TODO: Implement laporan generate
        return back()->with('success', 'Laporan berhasil digenerate');
    }
}
