<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    /**
     * 1. Menampilkan daftar seluruh kategori dengan pagination & relasi count buku
     */
    public function index()
    {
        $kategoris = Kategori::withCount('bukus')->latest()->paginate(10);

        return view('kategori.index', compact('kategoris'));
    }

    /**
     * 2. Menampilkan form pembuatan kategori baru
     */
    public function create()
    {
        return view('kategori.create');
    }

    /**
     * 3. Menyimpan kategori baru ke database dengan validasi ketat
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kategori' => 'required|string|max:20|unique:kategoris,kode_kategori',
            'nama_kategori' => 'required|string|min:3|max:100',
        ], [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique'   => 'Kode kategori sudah terdaftar.',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.min'      => 'Nama kategori minimal 3 karakter.',
        ]);

        Kategori::create($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori buku berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan detail kategori dan koleksi buku di dalamnya (Route Model Binding)
     */
    public function show(Kategori $kategori)
    {
        $kategori->load(['bukus' => function ($query) {
            $query->latest();
        }]);

        return view('kategori.show', compact('kategori'));
    }

    /**
     * 5. Menampilkan form ubah data kategori (Route Model Binding)
     */
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    /**
     * 6. Memperbarui data kategori ke database dengan validasi ketat
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'kode_kategori' => [
                'required',
                'string',
                'max:20',
                Rule::unique('kategoris', 'kode_kategori')->ignore($kategori->id),
            ],
            'nama_kategori' => 'required|string|min:3|max:100',
        ], [
            'kode_kategori.required' => 'Kode kategori wajib diisi.',
            'kode_kategori.unique'   => 'Kode kategori sudah digunakan kategori lain.',
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.min'      => 'Nama kategori minimal 3 karakter.',
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori buku berhasil diperbarui!');
    }

    /**
     * 7. Menghapus kategori beserta buku terkait (Cascade Delete)
     */
    public function destroy(Kategori $kategori)
    {
        $nama = $kategori->nama_kategori;
        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', "Kategori '{$nama}' dan buku terkait berhasil dihapus!");
    }
}