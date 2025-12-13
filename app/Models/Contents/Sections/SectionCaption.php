<?php

declare(strict_types=1);

namespace App\Models\Contents\Sections;

class SectionCaption extends Section
{
    public ?string $caption;

    /**
     * @param  array{type:string,caption?:string,text?:string,url?:string,id?:int}  $section
     */
    public function __construct(array $section)
    {
        parent::__construct($section);
        $this->caption = $section['caption'] ?? null;
    }
}
