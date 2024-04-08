<?php

declare(strict_types=1);

namespace App\Actions\CreateSitemap;

final class Refresh
{
    public function __construct(
        private readonly Destroy $destroy,
        private readonly Create $create,
    ) {

    }

    public function __invoke(string $siteurl): void
    {
        ($this->destroy)();
        ($this->create)($siteurl);
    }
}
