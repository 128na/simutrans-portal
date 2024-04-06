<?php

declare(strict_types=1);

namespace App\Models\Contents;

use App\Models\Contents\Sections\SectionCaption;
use App\Models\Contents\Sections\SectionImage;
use App\Models\Contents\Sections\SectionText;
use App\Models\Contents\Sections\SectionUrl;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int,Sections\Section>
 */
class PageContentsSections extends Collection
{
    /**
     * @param  array<mixed>  $items
     */
    public function __construct(array $items = [])
    {
        $items = array_map(function (array $item) {
            switch ($item['type']) {
                case 'caption':
                    return new SectionCaption($item);
                case 'text':
                    return new SectionText($item);
                case 'url':
                    return new SectionUrl($item);
                case 'image':
                    return new SectionImage($item);
            }
        }, $items);
        parent::__construct($items);
    }
}
