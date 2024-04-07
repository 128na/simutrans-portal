<?php

declare(strict_types=1);

namespace App\Models\Contents\Sections;

class SectionImage extends Section
{
    public ?int $id;

    /**
     * @param  array{type:string,caption?:string,text?:string,url?:string,id?:int}  $section
     */
    public function __construct(array $section)
    {
        parent::__construct($section);
        $id = $section['id'] ?? null;
        $this->id = $id ?: null;
    }
}
