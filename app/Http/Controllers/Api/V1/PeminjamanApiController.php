<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'status' => ['sometimes', 'in:dipinjam,dikembalikan'],
            'nim' => ['sometimes', 'string', 'max:30'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $loans = Peminjaman::query()->with(['buku.kategori']);

        if (isset($filters['status'])) {
            $loans->where('status', $filters['status']);
        }
        if (isset($filters['nim'])) {
            $loans->where('nim_peminjam', $filters['nim']);
        }

        $paginator = $loans->latest()->paginate($filters['per_page'] ?? 10)->withQueryString();

        return response()->json([
            'data' => PeminjamanResource::collection($paginator)->resolve($request),
            'message' => 'Daftar peminjaman berhasil diambil.',
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    public function show(int $peminjaman)
    {
        $loan = Peminjaman::with(['buku.kategori'])->findOrFail($peminjaman);

        return $this->apiSuccess((new PeminjamanResource($loan))->resolve(request()));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'buku_id' => ['required', 'integer', 'exists:bukus,id'],
            'nim_peminjam' => ['required', 'string', 'max:30'],
            'nama_peminjam' => ['required', 'string', 'max:100'],
            'durasi_hari' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $loan = DB::transaction(function () use ($validated) {
            $book = Buku::query()->whereKey($validated['buku_id'])->lockForUpdate()->firstOrFail();

            $alreadyBorrowed = Peminjaman::query()
                ->where('buku_id', $book->id)
                ->where('nim_peminjam', $validated['nim_peminjam'])
                ->where('status', 'dipinjam')
                ->exists();
            if ($alreadyBorrowed) {
                abort(409, 'Mahasiswa masih meminjam buku ini.');
            }

            if ($book->stok < 1) {
                abort(422, 'Stok buku sedang habis.');
            }

            $borrowedAt = now()->startOfDay();
            $book->decrement('stok');

            return Peminjaman::create([
                ...$validated,
                'status' => 'dipinjam',
                'tanggal_pinjam' => $borrowedAt->toDateString(),
                'tanggal_jatuh_tempo' => $borrowedAt->copy()->addDays($validated['durasi_hari'])->toDateString(),
            ]);
        });

        $loan->load('buku.kategori');

        return $this->apiSuccess(
            (new PeminjamanResource($loan))->resolve($request),
            'Peminjaman berhasil dicatat.',
            201,
        )->header('Location', url("/api/v1/peminjamans/{$loan->id}"));
    }

    public function returnBook(Request $request, int $peminjaman)
    {
        $loan = DB::transaction(function () use ($peminjaman) {
            $loan = Peminjaman::query()->whereKey($peminjaman)->lockForUpdate()->firstOrFail();

            if ($loan->status !== 'dipinjam') {
                abort(409, 'Peminjaman ini sudah dikembalikan.');
            }

            $book = Buku::query()->whereKey($loan->buku_id)->lockForUpdate()->firstOrFail();
            $loan->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => now()->toDateString(),
            ]);
            $book->increment('stok');

            return $loan->fresh(['buku.kategori']);
        });

        return $this->apiSuccess(
            (new PeminjamanResource($loan))->resolve($request),
            'Buku berhasil dikembalikan.',
        );
    }
}
