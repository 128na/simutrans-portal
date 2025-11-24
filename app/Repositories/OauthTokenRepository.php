<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OauthToken;

final readonly class OauthTokenRepository
{
    public function __construct(private OauthToken $oauthToken) {}

    public function getToken(string $application): OauthToken
    {
        return $this->oauthToken->where('application', $application)->firstOrFail();
    }

    /**
     * @param  array<mixed>  $search
     * @param  array<mixed>  $data
     */
    public function updateOrCreate(array $search, array $data = []): OauthToken
    {
        return $this->oauthToken->updateOrCreate($search, $data);
    }

    public function delete(OauthToken $oauthToken): void
    {
        $oauthToken->delete();
    }
}
