<?php

declare(strict_types=1);

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface BulkZippableInterface
{
    /**
     * @return MorphOne<\App\Models\BulkZip,$this>
     */
    public function bulkZippable(): MorphOne;
}
