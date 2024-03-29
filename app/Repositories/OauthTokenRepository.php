<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\OauthToken;

/**
 * @extends BaseRepository<OauthToken>
 */
class OauthTokenRepository extends BaseRepository
{
    /**
     * @var OauthToken
     */
    protected $model;

    public function __construct(OauthToken $oauthToken)
    {
        $this->model = $oauthToken;
    }

    public function getToken(string $application): OauthToken
    {
        return $this->model->where('application', $application)->firstOrFail();
    }
}
