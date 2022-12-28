<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ControllOption;
use Illuminate\Database\Eloquent\Collection;

class ControllOptionController extends Controller
{
    public function __construct(private ControllOption $controllOption)
    {
    }

    /**
     * @return Collection<int, ControllOption>
     */
    public function index(): Collection
    {
        return $this->controllOption->all();
    }

    /**
     * @return Collection<int, ControllOption>
     */
    public function toggle(ControllOption $controllOption): Collection
    {
        $controllOption->update(['value' => ! $controllOption->value]);

        return $this->index();
    }
}
