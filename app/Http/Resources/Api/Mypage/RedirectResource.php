<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\Mypage;

use App\Models\Redirect;
use Illuminate\Http\Resources\Json\JsonResource;

final class RedirectResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array<mixed>
     */
    #[\Override]
    public function toArray($request)
    {
        assert($this->resource instanceof Redirect);

        return [
            'id' => $this->resource->id,
            'from' => $this->resource->from,
            'to' => $this->resource->to,
        ];
    }
}
