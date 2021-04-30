<?php

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface BulkZippableInterface
{
    public function bulkZippable(): MorphOne;
}
