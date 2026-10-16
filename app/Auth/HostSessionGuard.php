<?php

declare(strict_types=1);

namespace App\Auth;

use Illuminate\Auth\SessionGuard;

class HostSessionGuard extends SessionGuard
{
    #[\Override]
    public function getRecallerName(): string
    {
        $prefix = config('session.secure') ? '__Host-simutrans_portal_' : 'simutrans_portal_local_';

        return $prefix.'remember_'.$this->name;
    }

    public function getLegacyRecallerName(): string
    {
        return 'remember_'.$this->name.'_'.sha1(SessionGuard::class);
    }
}
