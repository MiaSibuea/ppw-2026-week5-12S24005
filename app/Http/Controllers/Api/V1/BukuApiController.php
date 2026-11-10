<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\BukuResource;
use App\Models\Buku;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BukuApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:100'],
            'kategori_id' => ['sometimes', 'integer', 'exists:kategoris,id'],
            'tersedia' => ['sometimes', 'boolean'],
            'sort' => ['sometimes', 'string', 'max:40'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Buku::query()->with('kategori')->withCount('peminjamans');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $query) use ($search) {
                $query->where('judul', 'like', "%{$search}%")
                    ->orWhere('penulis', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if (isset($filters['kategori_id'])) {
            $query->where('kategori_id', $filters['kategori_id']);
        }

        if (($filters['tersedia'] ?? false) === true || ($filters['tersedia'] ?? null) === '1') {
            $query->where('stok', '>', 0);
        }

        $sort = $filters['sort'] ?? '-created_at';
        $descending = str_starts_with($sort, '-');
        $sortColumn = ltrim($sort, '-');
        if (! in_array($sortColumn, ['judul', 'tahun_terbit', 'created_at', 'stok'], true)) {
            throw ValidationException::withMessages([
                'sort' => 'Pengurutan hanya mendukung judul, tahun_terbit, created_at, atau stok.',
            ]);
        }
        $query->orderBy($sortColumn, $descending ? 'desc' : 'asc');

        $books = $query->paginate($filters['per_page'] ?? 10)->withQueryString();

        return response()->json([
            'data' => BukuResource::collection($books)->resolve($request),
            'message' => 'Katalog buku berhasil diambil.',
            'links' => [
                'first' => $books->url(1),
                'last' => $books->url($books->lastPage()),
                'prev' => $books->previousPageUrl(),
                'next' => $books->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $books->currentPage(),
                'from' => $books->firstItem(),
                'last_page' => $books->lastPage(),
                'per_page' => $books->perPage(),
                'to' => $books->lastItem(),
                'total' => $books->total(),
                'filters' => $filters,
            ],
        ]);
    }

    public function show(int $buku)
    {
        $book = Buku::with(['kategori', 'peminjamans'])->withCount('peminjamans')->findOrFail($buku);

        return $this->apiSuccess((new BukuResource($book))->resolve(request()));
    }

    public function externalReference(int $buku)
    {
        $book = Buku::findOrFail($buku);

        try {
            $response = Http::acceptJson()
                ->timeout(8)
                ->get('https://openlibrary.org/api/books', [
                    'bibkeys' => 'ISBN:'.$book->isbn,
                    'format' => 'json',
                    'jscmd' => 'data',
                ]);
        } catch (ConnectionException) {
            return $this->apiSuccess([
                'available' => false,
                'source' => 'Open Library',
                'detail' => 'Metadata eksternal sementara tidak dapat dijangkau.',
            ], 'Metadata eksternal tidak tersedia.');
        }

        if (! $response->successful()) {
            return $this->apiSuccess([
                'available' => false,
                'source' => 'Open Library',
                'detail' => $response->status() === 404
                    ? 'ISBN tidak ditemukan pada Open Library.'
                    : 'Open Library mengembalikan respons yang tidak berhasil.',
            ], 'Metadata eksternal tidak tersedia.');
        }

        $metadata = $response->json('ISBN:'.$book->isbn);
        if (! is_array($metadata)) {
            return $this->apiSuccess([
                'available' => false,
                'source' => 'Open Library',
                'detail' => 'ISBN tidak ditemukan pada Open Library.',
            ], 'Metadata eksternal tidak tersedia.');
        }

        $publishers = is_array($metadata['publishers'] ?? null) ? $metadata['publishers'] : [];
        $publisherNames = array_values(array_filter(array_map(
            fn ($publisher) => is_array($publisher) && is_string($publisher['name'] ?? null)
                ? $publisher['name']
                : null,
            $publishers,
        ), 'is_string'));

        return $this->apiSuccess([
            'available' => true,
            'source' => 'Open Library',
            'title' => is_string($metadata['title'] ?? null) ? $metadata['title'] : null,
            'publishers' => $publisherNames,
            'number_of_pages' => is_numeric($metadata['number_of_pages'] ?? null)
                ? (int) $metadata['number_of_pages']
                : null,
            'preview_url' => is_string($metadata['preview_url'] ?? null) ? $metadata['preview_url'] : null,
        ], 'Metadata eksternal berhasil diambil.');
    }

    public function store(Request $request)
    {
        $book = Buku::create($this->validatedBook($request));
        $book->load('kategori')->loadCount('peminjamans');

        return $this->apiSuccess(
            (new BukuResource($book))->resolve($request),
            'Buku berhasil ditambahkan.',
            201,
        )->header('Location', url("/api/v1/bukus/{$book->id}"));
    }

    public function update(Request $request, int $buku)
    {
        $book = Buku::findOrFail($buku);
        $book->update($this->validatedBook($request, $book));
        $book->load('kategori')->loadCount('peminjamans');

        return $this->apiSuccess(
            (new BukuResource($book))->resolve($request),
            'Buku berhasil diperbarui.',
        );
    }

    public function destroy(int $buku)
    {
        DB::transaction(function () use ($buku) {
            $book = Buku::query()->whereKey($buku)->lockForUpdate()->firstOrFail();
            $hasActiveLoans = $book->peminjamans()->where('status', 'dipinjam')->exists();

            if ($hasActiveLoans) {
                abort(409, 'Buku tidak dapat dihapus selama masih memiliki peminjaman aktif.');
            }

            $book->delete();
        });

        return response()->noContent();
    }

    private function validatedBook(Request $request, Buku $book = null): array
    {
        $partial = $request->isMethod('patch');
        $required = $partial ? ['sometimes', 'required'] : ['required'];

        return $request->validate([
            'isbn' => [...$required, 'string', 'max:20', Rule::unique('bukus', 'isbn')->ignore($book?->id)],
            'judul' => [...$required, 'string', 'min:5', 'max:200'],
            'penulis' => [...$required, 'string', 'min:3', 'max:100'],
            'penerbit' => [...$required, 'string', 'min:3', 'max:100'],
            'tahun_terbit' => [...$required, 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'kategori_id' => [...$required, 'integer', 'exists:kategoris,id'],
            'stok' => [...$required, 'integer', 'min:0'],
            'sinopsis' => [...($partial ? ['sometimes'] : ['present']), 'nullable', 'string'],
        ]);
    }
}
