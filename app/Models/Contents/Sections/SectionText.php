<?php

declare(strict_types=1);

namespace App\Models\Contents\Sections;

final class SectionText extends Section
{
    public null|string $text;

    /**
     * @param  array{type:string,caption?:string,text?:string,url?:string,id?:int}  $section
     */
    public function __construct(array $section)
    {
        parent::__construct($section);
        $this->text = $section['text'] ?? null;
    }
}
