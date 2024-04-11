<?php

declare(strict_types=1);

namespace App\Models\Contents;

final class PageContent extends Content
{
    public PageContentsSections $sections;

    /**
     * @param  array{sections?:array<int,array{type:string,caption?:string,text?:string,url?:string,id?:int}>}  $contents
     */
    public function __construct(array $contents)
    {
        $this->sections = new PageContentsSections($contents['sections'] ?? []);
        parent::__construct($contents);
    }

    #[\Override]
    public function getDescription(): string
    {
        return collect($this->sections)->pluck('text')->implode("\n");
    }
}
