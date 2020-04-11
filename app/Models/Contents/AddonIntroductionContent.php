<?php

namespace App\Models\Contents;

class AddonIntroductionContent extends Content
{
    protected $attributes = [
        'thumbnail',
        'description',
        'link',
        'author',
        'license',
        'thanks',
        'agreement' => false,
        'exclude_link_check' => false,
    ];

    public function getDescription()
    {
        return $this->description;
    }
}
