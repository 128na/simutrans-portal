<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\Article\JobUpdateRelated;
use Illuminate\Support\Facades\Cache;

class DebugController extends Controller
{
    public function flushCache()
    {
        JobUpdateRelated::dispatchSync();
        Cache::flush();

        return response('');
    }

    public function error($level = 'error')
    {
        switch ($level) {
            case 'notice':
                return trigger_error('Notice was created manually.', E_USER_NOTICE);
            case 'warning':
                return trigger_error('Warning was created manually.', E_USER_WARNING);
            case 'error':
            default:
                return trigger_error('Error was created manually.', E_USER_ERROR);
        }
    }

    public function phpinfo()
    {
        ob_start();
        phpinfo();
        $html = ob_get_contents();
        ob_get_clean();

        return $html;
    }
}
