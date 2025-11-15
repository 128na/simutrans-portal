<?php

declare(strict_types=1);

namespace App\Models\Contents;

use App\Models\Contents\Sections\Section;
use App\Models\Contents\Sections\SectionCaption;
use App\Models\Contents\Sections\SectionImage;
use App\Models\Contents\Sections\SectionText;
use App\Models\Contents\Sections\SectionUrl;
use Exception;
use Illuminate\Support\Collection;

/**
 * @extends Collection<int,Sections\Section>
 */
final class PageContentsSections extends Collection
{
    /**
     * @param  array<int,array{type:string,caption?:string,text?:string,url?:string,id?:int}>  $items
     */
    public function __construct(array $items = [])
    {
        $items = array_map(fn(array $item): Section => match ($item['type']) {
            'caption' => new SectionCaption($item),
            'text' => new SectionText($item),
            'url' => new SectionUrl($item),
            'image' => new SectionImage($item),
            default => throw new Exception('unsupport type:' . $item['type']),
        }, $items);
        parent::__construct($items);
    }
}
