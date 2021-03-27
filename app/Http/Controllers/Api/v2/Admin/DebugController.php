<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class DebugController extends Controller
{
    public function flushCache()
    {
        Cache::flush();

        return response('');
    }

    public function error($level = 'error')
    {
        switch ($level) {
            case 'notice':
                trigger_error('Notice was created manually.', E_USER_NOTICE);

                return response('');
        case 'warning':
                trigger_error('Warning was created manually.', E_USER_WARNING);

                return response('');
            case 'error':
            default:
                trigger_error('Error was created manually.', E_USER_ERROR);

                return response('');
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
