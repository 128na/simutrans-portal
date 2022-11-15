<?php

namespace App\Http\Controllers\Api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Models\ControllOption;

class ControllOptionController extends Controller
{
    public function __construct(private ControllOption $controllOption)
    {
    }

    public function index()
    {
        return $this->controllOption->all();
    }

    public function toggle(ControllOption $controllOption)
    {
        $controllOption->update(['value' => !$controllOption->value]);

        return $this->index();
    }
}
