<?php

namespace App\Models\Contents;

class AddonPostContent extends Content
{
    protected $attributes = [
        'thumbnail',
        'description',
        'file',
        'author',
        'license',
        'thanks',
    ];
}
