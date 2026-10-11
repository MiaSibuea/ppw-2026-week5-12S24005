<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BukuController extends Controller
{
    /**
     * 1. Menampilkan daftar buku dengan Eager Loading (with('kategori')) & Pagination
     * Mencegah N+1 Query Problem secara optimal.
     */
    public function index()
    {
        $bukus = Buku::with('kategori')->latest()->paginate(10);

        return view('buku.index', compact('bukus'));
    }

    /**
     * 2. Menampilkan form pembuatan buku baru
     */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('buku.create', compact('kategoris'));
    }

    /**
     * 3. Menyimpan buku baru dengan Validasi Ketat Sisi Server
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'isbn'         => 'required|string|max:20|unique:bukus,isbn',
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|min:3|max:100',
            'penerbit'     => 'required|string|min:3|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ], [
            'isbn.required'         => 'Nomor ISBN wajib diisi.',
            'isbn.unique'           => 'Nomor ISBN ini sudah terdaftar dalam sistem.',
            'judul.required'        => 'Judul buku wajib diisi.',
            'judul.min'             => 'Judul buku minimal harus 5 karakter.',
            'penulis.required'      => 'Nama penulis wajib diisi.',
            'penerbit.required'     => 'Nama penerbit wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'kategori_id.required'  => 'Kategori buku wajib dipilih.',
            'kategori_id.exists'    => 'Kategori yang dipilih tidak valid.',
            'stok.required'         => 'Jumlah stok wajib diisi.',
            'stok.min'              => 'Jumlah stok minimal adalah 0.',
        ]);

        Buku::create($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Buku baru berhasil ditambahkan ke katalog SIPUS-Del!');
    }

    /**
     * 4. Menampilkan detail lengkap buku beserta relasi kategori (Route Model Binding)
     */
    public function show(Buku $buku)
    {
        $buku->load('kategori');

        return view('buku.show', compact('buku'));
    }

    /**
     * 5. Menampilkan form ubah data buku (Route Model Binding)
     */
    public function edit(Buku $buku)
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('buku.edit', compact('buku', 'kategoris'));
    }

    /**
     * 6. Memperbarui data buku dengan validasi ketat server-side
     */
    public function update(Request $request, Buku $buku)
    {
        $validated = $request->validate([
            'isbn' => [
                'required',
                'string',
                'max:20',
                Rule::unique('bukus', 'isbn')->ignore($buku->id),
            ],
            'judul'        => 'required|string|min:5|max:200',
            'penulis'      => 'required|string|min:3|max:100',
            'penerbit'     => 'required|string|min:3|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'kategori_id'  => 'required|exists:kategoris,id',
            'stok'         => 'required|integer|min:0',
            'sinopsis'     => 'nullable|string',
        ], [
            'isbn.required'         => 'Nomor ISBN wajib diisi.',
            'isbn.unique'           => 'Nomor ISBN sudah digunakan oleh buku lain.',
            'judul.required'        => 'Judul buku wajib diisi.',
            'judul.min'             => 'Judul buku minimal harus 5 karakter.',
            'penulis.required'      => 'Nama penulis wajib diisi.',
            'penerbit.required'     => 'Nama penerbit wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'kategori_id.required'  => 'Kategori buku wajib dipilih.',
            'kategori_id.exists'    => 'Kategori yang dipilih tidak valid.',
            'stok.required'         => 'Jumlah stok wajib diisi.',
            'stok.min'              => 'Jumlah stok minimal adalah 0.',
        ]);

        $buku->update($validated);

        return redirect()->route('buku.index')
            ->with('success', 'Data buku berhasil diperbarui!');
    }

    /**
     * 7. Menghapus buku dari database (Route Model Binding)
     */
    public function destroy(Buku $buku)
    {
        $judul = $buku->judul;
        $buku->delete();

        return redirect()->route('buku.index')
            ->with('success', "Buku '{$judul}' berhasil dihapus dari sistem!");
    }
}