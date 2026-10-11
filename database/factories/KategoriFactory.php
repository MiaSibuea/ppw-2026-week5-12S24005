<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kategori>
 */
class KategoriFactory extends Factory
{
    protected $model = Kategori::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak'],
            ['kode' => 'AI', 'nama' => 'Kecerdasan Buatan & Data Science'],
            ['kode' => 'JARKOM', 'nama' => 'Jaringan Komputer & Keamanan Siber'],
            ['kode' => 'SI', 'nama' => 'Sistem Informasi & Manajemen TI'],
            ['kode' => 'BASDAT', 'nama' => 'Basis Data & Pemrosesan Data'],
            ['kode' => 'ALGO', 'nama' => 'Algoritma & Struktur Data'],
            ['kode' => 'WEB', 'nama' => 'Pemrograman & Pengujian Web'],
            ['kode' => 'MOBILE', 'nama' => 'Pengembangan Aplikasi Mobile'],
        ];

        $selected = fake()->unique()->randomElement($categories);

        return [
            'kode_kategori' => $selected['kode'] . '-' . fake()->unique()->numerify('###'),
            'nama_kategori' => $selected['nama'],
        ];
    }
}
