<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categoriesData = [
            [
                'kode_kategori' => 'RPL-01',
                'nama_kategori' => 'Rekayasa Perangkat Lunak',
                'bukus' => [
                    [
                        'isbn' => '978-602-1234-01-1',
                        'judul' => 'Clean Architecture: Panduan Praktis Struktur Perangkat Lunak',
                        'penulis' => 'Robert C. Martin',
                        'penerbit' => 'Prentice Hall',
                        'tahun_terbit' => 2021,
                        'stok' => 12,
                        'sinopsis' => 'Buku panduan universal tentang arsitektur perangkat lunak, pemisahan dependensi, domain-driven design, dan modularitas sistem web modern.',
                    ],
                    [
                        'isbn' => '978-602-1234-02-8',
                        'judul' => 'Design Patterns: Elements of Reusable Object-Oriented Software',
                        'penulis' => 'Erich Gamma, Richard Helm, Ralph Johnson, John Vlissides',
                        'penerbit' => 'Addison-Wesley',
                        'tahun_terbit' => 2020,
                        'stok' => 8,
                        'sinopsis' => 'Koleksi 23 pola desain klasik yang menjadi fondasi penting bagi arsitektur berorientasi objek dalam software engineering.',
                    ],
                    [
                        'isbn' => '978-602-1234-03-5',
                        'judul' => 'Refactoring: Improving the Design of Existing Code',
                        'penulis' => 'Martin Fowler',
                        'penerbit' => 'Addison-Wesley Professional',
                        'tahun_terbit' => 2022,
                        'stok' => 15,
                        'sinopsis' => 'Prinsip, teknik, dan resep refactoring kode demi meningkatkan kualitas keterbacaan, efisiensi, dan kemudahan pemeliharaan sistem.',
                    ],
                    [
                        'isbn' => '978-602-1234-04-2',
                        'judul' => 'Test-Driven Development: By Example',
                        'penulis' => 'Kent Beck',
                        'penerbit' => 'Addison-Wesley',
                        'tahun_terbit' => 2021,
                        'stok' => 10,
                        'sinopsis' => 'Metodologi pengembangan TDD (Red-Green-Refactor) untuk menghasilkan kode yang bersih, bebas regresi, dan teruji secara ketat.',
                    ],
                ]
            ],
            [
                'kode_kategori' => 'WEB-02',
                'nama_kategori' => 'Pemrograman & Pengujian Web',
                'bukus' => [
                    [
                        'isbn' => '978-602-2345-01-7',
                        'judul' => 'Mastering Laravel 11: Arsitektur Modern MVC dan Server-Side Rendering',
                        'penulis' => 'Chandro Pardede',
                        'penerbit' => 'Del Press',
                        'tahun_terbit' => 2024,
                        'stok' => 20,
                        'sinopsis' => 'Panduan komprehensif menguasai framework Laravel 11, routing RESTful, Eloquent ORM, Blade components, dan mitigasi N+1 query problem.',
                    ],
                    [
                        'isbn' => '978-602-2345-02-4',
                        'judul' => 'Building Secure Web APIs with PHP & Laravel',
                        'penulis' => 'Taylor Otwell',
                        'penerbit' => 'O\'Reilly Media',
                        'tahun_terbit' => 2023,
                        'stok' => 14,
                        'sinopsis' => 'Membangun antarmuka pemrograman aplikasi web yang tangguh, aman dari serangan CSRF, XSS, SQLi, dan memenuhi standar RESTful API.',
                    ],
                    [
                        'isbn' => '978-602-2345-03-1',
                        'judul' => 'Automated Web Testing: Unit, Integration, & E2E',
                        'penulis' => 'Lisa Crispin',
                        'penerbit' => 'Informatika Bandung',
                        'tahun_terbit' => 2022,
                        'stok' => 7,
                        'sinopsis' => 'Strategi pengujian otomatis aplikasi web mulai dari unit testing PHPUnit hingga pengujian integrasi browser tingkat lanjut.',
                    ],
                    [
                        'isbn' => '978-602-2345-04-8',
                        'judul' => 'Modern CSS & Frontend Frameworks: Dari Bootstrap ke Tailwind',
                        'penulis' => 'Adam Wathan',
                        'penerbit' => 'Packt Publishing',
                        'tahun_terbit' => 2024,
                        'stok' => 18,
                        'sinopsis' => 'Evolusi styling web modern, pemanfaatan sistem grid, utilitas CSS responsif, dan perancangan antarmuka modular.',
                    ],
                ]
            ],
            [
                'kode_kategori' => 'BASDAT-03',
                'nama_kategori' => 'Basis Data & Manajemen Data',
                'bukus' => [
                    [
                        'isbn' => '978-602-3456-01-3',
                        'judul' => 'Database System Concepts (Seventh Edition)',
                        'penulis' => 'Abraham Silberschatz, Henry F. Korth, S. Sudarshan',
                        'penerbit' => 'McGraw-Hill Education',
                        'tahun_terbit' => 2020,
                        'stok' => 16,
                        'sinopsis' => 'Buku teks standar perancangan basis data relasional, normalisasi hingga bentuk BCNF/5NF, indexing B-Tree, dan manajemen transaksi ACID.',
                    ],
                    [
                        'isbn' => '978-602-3456-02-0',
                        'judul' => 'High Performance MySQL: Optimization, Backups, and Replication',
                        'penulis' => 'Silvia Botros, Jeremy Tinley',
                        'penerbit' => 'O\'Reilly Media',
                        'tahun_terbit' => 2022,
                        'stok' => 9,
                        'sinopsis' => 'Teknik optimasi query basis data berskala besar, tuning indeks relasional, profiling latensi, dan mitigasi bottleneck.',
                    ],
                    [
                        'isbn' => '978-602-3456-03-7',
                        'judul' => 'Designing Data-Intensive Applications',
                        'penulis' => 'Martin Kleppmann',
                        'penerbit' => 'O\'Reilly Media',
                        'tahun_terbit' => 2021,
                        'stok' => 22,
                        'sinopsis' => 'Analisis mendalam mengenai penyimpanan data, replikasi, partisi, konsistensi terdistribusi, dan keandalan sistem berskala besar.',
                    ],
                    [
                        'isbn' => '978-602-3456-04-4',
                        'judul' => 'SQL Queries for Mere Mortals: A Hands-On Guide',
                        'penulis' => 'John L. Viescas',
                        'penerbit' => 'Addison-Wesley Professional',
                        'tahun_terbit' => 2023,
                        'stok' => 11,
                        'sinopsis' => 'Kiat menyusun kueri SQL relasional yang efektif, manipulasi set data kompleks, subquery, dan join multi-tabel.',
                    ],
                ]
            ],
            [
                'kode_kategori' => 'AI-04',
                'nama_kategori' => 'Kecerdasan Buatan & Data Science',
                'bukus' => [
                    [
                        'isbn' => '978-602-4567-01-9',
                        'judul' => 'Artificial Intelligence: A Modern Approach (4th Edition)',
                        'penulis' => 'Stuart Russell, Peter Norvig',
                        'penerbit' => 'Pearson',
                        'tahun_terbit' => 2021,
                        'stok' => 10,
                        'sinopsis' => 'Karya fundamental mengenai agen cerdas, teknik pencarian heuristik, penalaran probabilistik, hingga pembelajaran mesin kontemporer.',
                    ],
                    [
                        'isbn' => '978-602-4567-02-6',
                        'judul' => 'Deep Learning: Foundations and Concepts',
                        'penulis' => 'Ian Goodfellow, Yoshua Bengio, Aaron Courville',
                        'penerbit' => 'MIT Press',
                        'tahun_terbit' => 2020,
                        'stok' => 6,
                        'sinopsis' => 'Buku rujukan utama jaringan saraf tiruan (neural networks), backpropagation, convolutional networks, dan generative modeling.',
                    ],
                    [
                        'isbn' => '978-602-4567-03-3',
                        'judul' => 'Hands-On Machine Learning with Scikit-Learn, Keras, and TensorFlow',
                        'penulis' => 'Aurélien Géron',
                        'penerbit' => 'O\'Reilly Media',
                        'tahun_terbit' => 2023,
                        'stok' => 13,
                        'sinopsis' => 'Praktik implementasi pipeline machine learning end-to-end, feature engineering, evaluasi model, dan deployment solusi AI.',
                    ],
                    [
                        'isbn' => '978-602-4567-04-0',
                        'judul' => 'Pattern Recognition and Machine Learning',
                        'penulis' => 'Christopher M. Bishop',
                        'penerbit' => 'Springer',
                        'tahun_terbit' => 2020,
                        'stok' => 5,
                        'sinopsis' => 'Landasan teori matematika dan statistik untuk pengenalan pola, inferensi Bayesian, dan estimasi parameter probabilitas.',
                    ],
                ]
            ],
            [
                'kode_kategori' => 'JARKOM-05',
                'nama_kategori' => 'Jaringan Komputer & Keamanan Siber',
                'bukus' => [
                    [
                        'isbn' => '978-602-5678-01-5',
                        'judul' => 'Computer Networking: A Top-Down Approach (8th Edition)',
                        'penulis' => 'James F. Kurose, Keith W. Ross',
                        'penerbit' => 'Pearson',
                        'tahun_terbit' => 2022,
                        'stok' => 14,
                        'sinopsis' => 'Konsep protokol jaringan dari lapisan aplikasi (HTTP, DNS) hingga transport (TCP, UDP), network (IP), dan data link layer.',
                    ],
                    [
                        'isbn' => '978-602-5678-02-2',
                        'judul' => 'Web Application Hacker\'s Handbook: Finding and Exploiting Security Flaws',
                        'penulis' => 'Dafydd Stuttard, Marcus Pinto',
                        'penerbit' => 'Wiley',
                        'tahun_terbit' => 2021,
                        'stok' => 8,
                        'sinopsis' => 'Eksplorasi mendalam celah kerentanan aplikasi web, teknik audit penetrasi, mitigasi CSRF, SQL Injection, dan session hijacking.',
                    ],
                    [
                        'isbn' => '978-602-5678-03-9',
                        'judul' => 'Cryptography and Network Security: Principles and Practice',
                        'penulis' => 'William Stallings',
                        'penerbit' => 'Pearson',
                        'tahun_terbit' => 2023,
                        'stok' => 12,
                        'sinopsis' => 'Prinsip enkripsi simetris & asimetris, tanda tangan digital, public key infrastructure (PKI), dan keamanan protokol TLS/SSL.',
                    ],
                    [
                        'isbn' => '978-602-5678-04-6',
                        'judul' => 'TCP/IP Illustrated, Volume 1: The Protocols',
                        'penulis' => 'Kevin R. Fall, W. Richard Stevens',
                        'penerbit' => 'Addison-Wesley Professional',
                        'tahun_terbit' => 2020,
                        'stok' => 9,
                        'sinopsis' => 'Bedah arsitektur paket protokol TCP/IP, flow control, congestion control, dan analisis lalu lintas jaringan tingkat rendah.',
                    ],
                ]
            ],
            [
                'kode_kategori' => 'SI-06',
                'nama_kategori' => 'Sistem Informasi & Tata Kelola TI',
                'bukus' => [
                    [
                        'isbn' => '978-602-6789-01-1',
                        'judul' => 'Management Information Systems: Managing the Digital Firm',
                        'penulis' => 'Kenneth C. Laudon, Jane P. Laudon',
                        'penerbit' => 'Pearson',
                        'tahun_terbit' => 2022,
                        'stok' => 17,
                        'sinopsis' => 'Peran strategis sistem informasi enterprise dalam transformasi digital organisasi, efisiensi operasional, dan pengambilan keputusan.',
                    ],
                    [
                        'isbn' => '978-602-6789-02-8',
                        'judul' => 'Enterprise Architecture as Strategy',
                        'penulis' => 'Jeanne W. Ross, Peter Weill, David C. Robertson',
                        'penerbit' => 'Harvard Business Press',
                        'tahun_terbit' => 2021,
                        'stok' => 11,
                        'sinopsis' => 'Kerangka kerja penyelarasan arsitektur teknologi informasi dengan visi strategis bisnis perusahaan multinasional.',
                    ],
                ]
            ],
        ];

        foreach ($categoriesData as $catItem) {
            $bukus = $catItem['bukus'];
            unset($catItem['bukus']);

            $kategori = Kategori::create($catItem);

            foreach ($bukus as $bukuData) {
                $bukuData['kategori_id'] = $kategori->id;
                Buku::create($bukuData);
            }
        }
    }
}
