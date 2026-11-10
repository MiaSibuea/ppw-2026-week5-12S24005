<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $links = [
            'self' => ['href' => url("/api/v1/bukus/{$this->id}")],
            'koleksi' => ['href' => url('/api/v1/bukus')],
            'referensi_eksternal' => ['href' => url("/api/v1/bukus/{$this->id}/referensi-eksternal")],
        ];

        if ($this->stok > 0) {
            $links['pinjam_buku'] = ['href' => url('/api/v1/peminjamans'), 'method' => 'POST'];
        }

        return [
            'id' => $this->id,
            'isbn' => $this->isbn,
            'judul' => $this->judul,
            'penulis' => $this->penulis,
            'penerbit' => $this->penerbit,
            'tahun_terbit' => $this->tahun_terbit,
            'stok' => $this->stok,
            'sinopsis' => $this->sinopsis,
            'kategori' => new KategoriResource($this->whenLoaded('kategori')),
            'peminjamans_count' => $this->whenCounted('peminjamans'),
            'peminjamans' => PeminjamanResource::collection($this->whenLoaded('peminjamans')),
            '_links' => $links,
        ];
    }
}
