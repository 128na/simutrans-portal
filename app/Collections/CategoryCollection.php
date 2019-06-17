<?php
namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class CategoryCollection extends Collection
{
    public function separateByType()
    {
        return collect($this->reduce(function($separated, $item) {
            if (!isset($separated[$item->type])) {
                $separated[$item->type] = collect([]);
            }
            $separated[$item->type]->push($item);
            return $separated;
        }, []));
    }
}
