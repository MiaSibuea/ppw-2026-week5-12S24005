<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GithubConsumerController extends Controller
{
    use ApiResponse;

    public function showRepository(string $owner, string $repository)
    {
        try {
            $response = Http::acceptJson()
                ->withUserAgent('SIPUS-Del')
                ->timeout(8)
                ->get('https://api.github.com/repos/'.rawurlencode($owner).'/'.rawurlencode($repository));
        } catch (ConnectionException) {
            abort(502, 'GitHub API sementara tidak dapat dijangkau.');
        }

        if ($response->status() === 404) {
            abort(404, 'Repository GitHub tidak ditemukan atau tidak bersifat publik.');
        }

        if (! $response->successful()) {
            abort(502, 'GitHub API mengembalikan respons yang tidak berhasil.');
        }

        $repositoryData = $response->json();
        if (! is_array($repositoryData)) {
            abort(502, 'Respons GitHub API tidak berisi data repository yang valid.');
        }

        return $this->apiSuccess([
            'name' => $repositoryData['name'] ?? null,
            'full_name' => $repositoryData['full_name'] ?? null,
            'description' => $repositoryData['description'] ?? null,
            'html_url' => $repositoryData['html_url'] ?? null,
            'default_branch' => $repositoryData['default_branch'] ?? null,
            'stars' => $repositoryData['stargazers_count'] ?? null,
            'forks' => $repositoryData['forks_count'] ?? null,
            'open_issues' => $repositoryData['open_issues_count'] ?? null,
        ], 'Metadata repository berhasil diambil dari GitHub.', meta: [
            'source' => 'GitHub REST API',
        ]);
    }
}
