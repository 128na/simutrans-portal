<?php

namespace App\Console\Commands;

use Illuminate\Cache\Console\ClearCommand;

class CacheClearCommand extends ClearCommand
{
    public function handle()
    {
        parent::handle();
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
        }

        return 0;
    }
}
