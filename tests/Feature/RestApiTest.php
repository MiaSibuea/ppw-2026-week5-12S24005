<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Database\Seeders\PustakawanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_supports_filters_pagination_and_dynamic_links(): void
    {
        $category = Kategori::create(['kode_kategori' => 'API-1', 'nama_kategori' => 'API']);
        $book = $this->createBook($category, ['stok' => 2]);
        $this->createBook($category, ['judul' => 'Buku Tanpa Stok', 'isbn' => '978-0000000002', 'stok' => 0]);

        $response = $this->getJson('/api/v1/bukus?tersedia=1&search=API&per_page=1');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $book->id)
            ->assertJsonPath('data.0.kategori.nama_kategori', 'API')
            ->assertJsonPath('data.0._links.pinjam_buku.method', 'POST');
    }

    public function test_book_mutations_distinguish_put_and_patch_and_guard_active_loans(): void
    {
        $user = $this->createLibrarian();
        $category = Kategori::create(['kode_kategori' => 'API-2', 'nama_kategori' => 'API']);
        $book = $this->createBook($category);
        $headers = $this->tokenHeaders($user);

        $this->patchJson("/api/v1/bukus/{$book->id}", ['stok' => 8], $headers)
            ->assertOk()
            ->assertJsonPath('data.stok', 8);

        $this->putJson("/api/v1/bukus/{$book->id}", ['stok' => 3], $headers)
            ->assertUnprocessable()
            ->assertHeader('Content-Type', 'application/problem+json');

        $this->putJson("/api/v1/bukus/{$book->id}", [
            'isbn' => $book->isbn,
            'judul' => 'Buku Penggantian Penuh',
            'penulis' => $book->penulis,
            'penerbit' => $book->penerbit,
            'tahun_terbit' => $book->tahun_terbit,
            'kategori_id' => $category->id,
            'stok' => 3,
            'sinopsis' => null,
        ], $headers)->assertOk()->assertJsonPath('data.judul', 'Buku Penggantian Penuh');

        $this->postJson('/api/v1/peminjamans', [
            'buku_id' => $book->id,
            'nim_peminjam' => '12S24001',
            'nama_peminjam' => 'Mahasiswa Uji',
            'durasi_hari' => 7,
        ], $headers)->assertCreated();

        $this->deleteJson("/api/v1/bukus/{$book->id}", [], $headers)
            ->assertConflict()
            ->assertHeader('Content-Type', 'application/problem+json');
        $this->delete("/buku/{$book->id}")->assertRedirect('/buku')
            ->assertSessionHas('error');
        $this->delete("/kategori/{$category->id}")->assertRedirect('/kategori')
            ->assertSessionHas('error');

        $deletableBook = $this->createBook($category, [
            'isbn' => '978-0000000003',
            'judul' => 'Buku Untuk Dihapus',
        ]);
        $this->deleteJson("/api/v1/bukus/{$deletableBook->id}", [], $headers)->assertNoContent();
    }

    public function test_book_creation_requires_a_token_and_returns_location(): void
    {
        $category = Kategori::create(['kode_kategori' => 'API-6', 'nama_kategori' => 'API']);
        $payload = [
            'isbn' => '978-0000000006',
            'judul' => 'Buku API Baru',
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Del Press',
            'tahun_terbit' => 2025,
            'kategori_id' => $category->id,
            'stok' => 3,
            'sinopsis' => 'Buku yang dibuat melalui API.',
        ];

        $this->postJson('/api/v1/bukus', $payload)
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');

        $user = $this->createLibrarian();
        $response = $this->postJson('/api/v1/bukus', $payload, $this->tokenHeaders($user));

        $response->assertCreated()
            ->assertHeader('Location', url('/api/v1/bukus/'.$response->json('data.id')))
            ->assertJsonPath('data.isbn', $payload['isbn']);
    }

    public function test_loan_transactions_decrement_and_restore_stock_and_reject_duplicates(): void
    {
        $user = $this->createLibrarian();
        $category = Kategori::create(['kode_kategori' => 'API-3', 'nama_kategori' => 'API']);
        $book = $this->createBook($category, ['stok' => 1]);
        $headers = $this->tokenHeaders($user);
        $payload = [
            'buku_id' => $book->id,
            'nim_peminjam' => '12S24002',
            'nama_peminjam' => 'Mahasiswa Uji',
            'durasi_hari' => 7,
        ];

        $loanResponse = $this->postJson('/api/v1/peminjamans', $payload, $headers);
        $loanResponse->assertCreated()
            ->assertHeader('Location')
            ->assertJsonPath('data._links.kembalikan_buku.method', 'PATCH');
        $loanId = $loanResponse->json('data.id');
        $this->getJson("/api/v1/peminjamans/{$loanId}", $headers)
            ->assertOk()
            ->assertJsonPath('data.id', $loanId);
        $this->assertDatabaseHas('bukus', ['id' => $book->id, 'stok' => 0]);

        $this->postJson('/api/v1/peminjamans', $payload, $headers)->assertConflict();

        $this->patchJson("/api/v1/peminjamans/{$loanId}/kembalikan", [], $headers)
            ->assertOk()
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonMissingPath('data._links.kembalikan_buku');
        $this->assertDatabaseHas('bukus', ['id' => $book->id, 'stok' => 1]);
        $this->patchJson("/api/v1/peminjamans/{$loanId}/kembalikan", [], $headers)->assertConflict();

        $emptyBook = $this->createBook($category, [
            'isbn' => '978-0000000004',
            'judul' => 'Buku Stok Habis',
            'stok' => 0,
        ]);
        $this->postJson('/api/v1/peminjamans', [
            ...$payload,
            'buku_id' => $emptyBook->id,
            'nim_peminjam' => '12S24004',
        ], $headers)->assertUnprocessable()
            ->assertJsonPath('detail', 'Stok buku sedang habis.');
    }

    public function test_login_issues_a_token_and_logout_revokes_it(): void
    {
        $this->createLibrarian();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'pustakawan@del.ac.id',
            'password' => 'password123',
        ])->assertOk()->assertJsonPath('data.token_type', 'Bearer');

        $token = $login->json('data.token');
        $headers = ['Authorization' => "Bearer {$token}"];

        $this->getJson('/api/v1/auth/me', $headers)->assertOk();
        $this->postJson('/api/v1/auth/logout', [], $headers)->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/auth/me', $headers)->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_github_repository_consumer_returns_public_metadata_and_problem_errors(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/missing')) {
                return Http::response(['message' => 'Not Found'], 404);
            }

            if (str_contains($request->url(), '/timeout')) {
                throw new ConnectionException('Simulated timeout');
            }

            return Http::response([
                'name' => 'sipus-example',
                'full_name' => 'MiaSibuea/sipus-example',
                'description' => 'Repository test',
                'html_url' => 'https://github.com/MiaSibuea/sipus-example',
                'default_branch' => 'main',
                'stargazers_count' => 3,
                'forks_count' => 1,
                'open_issues_count' => 0,
            ]);
        });
        $this->getJson('/api/v1/github/repos/MiaSibuea/sipus-example')
            ->assertOk()
            ->assertJsonPath('meta.source', 'GitHub REST API')
            ->assertJsonPath('data.full_name', 'MiaSibuea/sipus-example');

        $this->get('/api/v1/github/repos/MiaSibuea/missing')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/problem+json');

        $this->get('/api/v1/github/repos/MiaSibuea/timeout')
            ->assertStatus(502)
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('title', 'Bad Gateway');
    }

    public function test_pustakawan_seeder_can_be_run_repeatedly(): void
    {
        $this->seed(PustakawanSeeder::class);
        $this->seed(PustakawanSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['email' => 'pustakawan@del.ac.id']);
    }

    public function test_soap_clearance_returns_loan_count_and_fault_for_malformed_xml(): void
    {
        $category = Kategori::create(['kode_kategori' => 'API-4', 'nama_kategori' => 'API']);
        $book = $this->createBook($category);
        Peminjaman::create([
            'buku_id' => $book->id,
            'nim_peminjam' => '12S24003',
            'nama_peminjam' => 'Mahasiswa Uji',
            'status' => 'dipinjam',
            'durasi_hari' => 7,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
        ]);

        $soap = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><CheckClearanceStatus><nim_peminjam>12S24003</nim_peminjam></CheckClearanceStatus></soap:Body></soap:Envelope>';
        $soapResponse = $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], $soap);
        $this->assertSame(200, $soapResponse->getStatusCode(), $soapResponse->getContent());
        $soapResponse
            ->assertSee('<tns:active_loans>1</tns:active_loans>', false)
            ->assertSee('<tns:is_cleared>false</tns:is_cleared>', false);

        $clearedSoap = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><CheckClearanceStatus><nim_peminjam>12S24009</nim_peminjam></CheckClearanceStatus></soap:Body></soap:Envelope>';
        $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], $clearedSoap)
            ->assertOk()
            ->assertSee('<tns:active_loans>0</tns:active_loans>', false)
            ->assertSee('<tns:is_cleared>true</tns:is_cleared>', false);

        $emptyNim = '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><CheckClearanceStatus><nim_peminjam></nim_peminjam></CheckClearanceStatus></soap:Body></soap:Envelope>';
        $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], $emptyNim)
            ->assertStatus(500)
            ->assertSee('<soap:Fault>', false);
        $this->call('POST', '/soap/sipus', [], [], [], ['CONTENT_TYPE' => 'text/xml'], '<bad-xml')
            ->assertStatus(500)
            ->assertSee('<soap:Fault>', false);
        $wsdl = $this->get('/soap/sipus.wsdl')->assertOk()->assertSee('CheckClearanceStatus', false);
        $this->assertNotFalse(simplexml_load_string($wsdl->getContent()));
    }

    public function test_api_not_found_errors_use_problem_details(): void
    {
        $this->get('/api/v1/bukus/999999')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('title', 'Not Found')
            ->assertJsonPath('status', 404);
    }

    public function test_openlibrary_reference_handles_success_and_timeout(): void
    {
        $category = Kategori::create(['kode_kategori' => 'API-5', 'nama_kategori' => 'API']);
        $book = $this->createBook($category);

        Http::fake([
            'openlibrary.org/*' => Http::response([
                'ISBN:978-0000000001' => [
                    'title' => 'Metadata Uji',
                    'publishers' => [['name' => 'Penerbit Uji']],
                    'number_of_pages' => 100,
                    'preview_url' => 'https://openlibrary.org/preview',
                ],
            ], 200),
        ]);
        $this->getJson("/api/v1/bukus/{$book->id}/referensi-eksternal")
            ->assertOk()
            ->assertJsonPath('data.available', true)
            ->assertJsonPath('data.title', 'Metadata Uji')
            ->assertJsonPath('data.publishers.0', 'Penerbit Uji')
            ->assertJsonPath('data.preview_url', 'https://openlibrary.org/preview');

        Http::fake(['openlibrary.org/*' => fn () => throw new ConnectionException('Simulated timeout')]);
        $this->getJson("/api/v1/bukus/{$book->id}/referensi-eksternal")
            ->assertOk()
            ->assertJsonPath('data.available', false);
    }

    private function createLibrarian(): User
    {
        return User::create([
            'name' => 'Pustakawan Uji',
            'email' => 'pustakawan@del.ac.id',
            'password' => Hash::make('password123'),
        ]);
    }

    private function tokenHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer '.$user->createToken('feature-test')->plainTextToken];
    }

    private function createBook(Kategori $category, array $overrides = []): Buku
    {
        return Buku::create(array_merge([
            'isbn' => '978-0000000001',
            'judul' => 'Buku API Laravel',
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Del Press',
            'tahun_terbit' => 2024,
            'kategori_id' => $category->id,
            'stok' => 5,
            'sinopsis' => 'Deskripsi singkat buku uji.',
        ], $overrides));
    }
}
