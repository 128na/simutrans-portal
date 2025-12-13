<?php

declare(strict_types=1);

namespace App\Actions\Redirect;

use App\Models\Redirect;

class DeleteRedirect
{
    public function __invoke(Redirect $redirect): void
    {
        $redirect->delete();
    }
}
