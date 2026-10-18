<?php
namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with('buku')->latest();

        // Pencarian nama, NIM, atau kode peminjaman
        if ($request->filled('q')) {
            $keyword = trim($request->input('q'));

            $query->where(function ($subQuery) use ($keyword) {
                $subQuery->where('nama_peminjam', 'like', '%' . $keyword . '%')
                    ->orWhere('nim_peminjam', 'like', '%' . $keyword . '%')
                    ->orWhere('kode_pinjam', 'like', '%' . $keyword . '%');
            });
        }

        // Filter status peminjaman
        $status = $request->input('status', '');

        if (!in_array($status, ['', 'dipinjam', 'terlambat', 'dikembalikan'], true)) {
            $status = '';
        }

        if ($status === 'dikembalikan') {
            $query->where('status', 'dikembalikan');
        } elseif ($status === 'terlambat') {
            $query->where('status', 'dipinjam')
                ->whereNotNull('tanggal_jatuh_tempo')
                ->whereDate('tanggal_jatuh_tempo', '<', today());
        } elseif ($status === 'dipinjam') {
            $query->where('status', 'dipinjam')
                ->where(function ($subQuery) {
                    $subQuery->whereNull('tanggal_jatuh_tempo')
                        ->orWhereDate('tanggal_jatuh_tempo', '>=', today());
                });
        }

        // Pagination tetap membawa parameter pencarian dan filter
        $peminjamans = $query->paginate(10)->withQueryString();

        return view('peminjaman.index', compact('peminjamans'));
    }

    public function pinjam(Request $request, $id)
    {
        $data = $request->validate([
            'nim_peminjam' => 'required|string|max:15',
            'nama_peminjam' => 'required|string|max:150',
        ]);

        try {
            DB::transaction(function () use ($data, $id) {
                $buku = Buku::where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($buku->stok < 1) {
                    throw ValidationException::withMessages([
                        'stok' => 'Stok buku habis.',
                    ]);
                }

                $sudahMeminjam = Peminjaman::where('buku_id', $buku->id)
                    ->where('nim_peminjam', $data['nim_peminjam'])
                    ->where('status', 'dipinjam')
                    ->exists();

                if ($sudahMeminjam) {
                    throw ValidationException::withMessages([
                        'nim_peminjam' => 'NIM ini masih meminjam buku yang sama.',
                    ]);
                }

                $buku->decrement('stok');

                Peminjaman::create([
                    'kode_pinjam' => 'PJ-' . Str::upper(Str::random(12)),
                    'buku_id' => $buku->id,
                    'nim_peminjam' => $data['nim_peminjam'],
                    'nama_peminjam' => $data['nama_peminjam'],
                    'tanggal_pinjam' => today()->toDateString(),
                    'tanggal_jatuh_tempo' => today()->addDays(7)->toDateString(),
                    'tanggal_kembali' => null,
                    'status' => 'dipinjam',
                ]);
            });

            return back()->with('success', 'Peminjaman berhasil dicatat.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            Log::error('Gagal mencatat peminjaman buku.', [
                'buku_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Peminjaman gagal karena masalah database.');
        }
    }

    public function kembalikan($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $peminjaman = Peminjaman::where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($peminjaman->status !== 'dipinjam') {
                    throw ValidationException::withMessages([
                        'status' => 'Buku ini sudah dikembalikan.',
                    ]);
                }

                $buku = Buku::where('id', $peminjaman->buku_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $peminjaman->update([
                    'status' => 'dikembalikan',
                    'tanggal_kembali' => today()->toDateString(),
                ]);

                $buku->increment('stok');
            });

            return back()->with('success', 'Buku berhasil dikembalikan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (QueryException $e) {
            Log::error('Gagal memproses pengembalian buku.', [
                'peminjaman_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Pengembalian gagal karena masalah database.'
            );
        }
    }
}