<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Buku>
 */
class BukuFactory extends Factory
{
    protected $model = Buku::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $publishers = [
            'Informatika Bandung',
            'Penerbit Andi Offset',
            'Elex Media Komputindo',
            'Gava Media',
            'O\'Reilly Media',
            'Packt Publishing',
            'Addison-Wesley Professional',
            'Del Press'
        ];

        return [
            'isbn' => fake()->unique()->numerify('978-602-####-##-#'),
            'judul' => fake()->sentence(fake()->numberBetween(3, 7)),
            'penulis' => fake()->name(),
            'penerbit' => fake()->randomElement($publishers),
            'tahun_terbit' => fake()->numberBetween(2018, 2026),
            'kategori_id' => Kategori::factory(),
            'stok' => fake()->numberBetween(1, 35),
            'sinopsis' => fake()->paragraphs(2, true),
        ];
    }
}
