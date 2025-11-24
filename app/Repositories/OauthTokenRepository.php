<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OauthToken;

final class OauthTokenRepository
{
    public function __construct(private readonly OauthToken $model) {}

    public function getToken(string $application): OauthToken
    {
        return $this->model->where('application', $application)->firstOrFail();
    }

    /**
     * @param  array<mixed>  $search
     * @param  array<mixed>  $data
     */
    public function updateOrCreate(array $search, array $data = []): OauthToken
    {
        return $this->model->updateOrCreate($search, $data);
    }

    public function delete(OauthToken $oauthToken): void
    {
        $oauthToken->delete();
    }
}
