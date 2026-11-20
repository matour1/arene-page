<?php

namespace App\Services\GameHub;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class GameHubClient
{
    public function __construct(private HttpFactory $http) {}

    public function configure(): bool
    {
        return filled(config('services.gamehub.token'));
    }

    /**
     * Récupère une page de tournois publics.
     *
     * @return array<string, mixed>|null
     */
    public function tournois(int $page = 1, int $parPage = 20): ?array
    {
        return $this->get('/tournois', [
            'page' => $page,
            'per_page' => $parPage,
        ]);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>|null
     */
    public function get(string $chemin, array $query = []): ?array
    {
        if (! $this->configure()) {
            Log::warning('GameHub: GAMEHUB_TOKEN absent, appel ignoré.', ['chemin' => $chemin]);

            return null;
        }

        try {
            $response = $this->request()->get($chemin, $query);

            if ($response->status() === 429) {
                Log::warning('GameHub: quota API atteint (429).', [
                    'reinitialisation' => $response->header('X-RateLimit-Reset'),
                ]);

                return null;
            }

            if ($response->failed()) {
                Log::warning('GameHub: échec de l\'appel API.', [
                    'chemin' => $chemin,
                    'statut' => $response->status(),
                    'corps' => str($response->body())->limit(300),
                ]);

                return null;
            }

            $this->journaliserQuota($response);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('GameHub: exception lors de l\'appel API.', [
                'chemin' => $chemin,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function request(): PendingRequest
    {
        return $this->http
            ->baseUrl((string) config('services.gamehub.base_url'))
            ->withToken((string) config('services.gamehub.token'))
            ->acceptJson()
            ->timeout((int) config('services.gamehub.timeout', 15))
            ->retry(2, 500, throw: false)
            ->withHeaders(['User-Agent' => 'AreneDesGamers/1.0 (+Laravel)']);
    }

    private function journaliserQuota(Response $response): void
    {
        $restant = $response->header('X-RateLimit-Remaining');

        if ($restant !== null && (int) $restant <= 50) {
            Log::warning('GameHub: quota API bas.', ['restant' => $restant]);
        }
    }
}
