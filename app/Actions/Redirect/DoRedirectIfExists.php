<?php

declare(strict_types=1);

namespace App\Actions\Redirect;

use App\Repositories\RedirectRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;

final readonly class DoRedirectIfExists
{
    public function __construct(
        private RedirectRepository $redirectRepository,
    ) {}

    public function __invoke(string $url): RedirectResponse
    {
        $path = $this->getRelativePath($url);
        $redirect = $this->redirectRepository->findOrFailByPath($path);
        logger(sprintf('[DoRedirectIfExists]: %s -> %s', $redirect->from, $redirect->to));

        return redirect($redirect->to, Response::HTTP_MOVED_PERMANENTLY);
    }

    private function getRelativePath(string $fullUrl): string
    {
        return str_replace(Config::string('app.url'), '', $fullUrl);
    }
}
