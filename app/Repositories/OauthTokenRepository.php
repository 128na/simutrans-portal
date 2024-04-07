<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OauthToken;

/**
 * @extends BaseRepository<OauthToken>
 */
final class OauthTokenRepository extends BaseRepository
{
    public function __construct(OauthToken $oauthToken)
    {
        parent::__construct($oauthToken);
    }

    public function getToken(string $application): OauthToken
    {
        return $this->model->where('application', $application)->firstOrFail();
    }
}
