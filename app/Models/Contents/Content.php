<?php

namespace App\Models\Contents;

class Content
{
    protected $content = [];
    public $thumbnail = null;

    public function __construct($content = [])
    {
        $this->content = is_array($content) ? $content : json_decode($content , true);

        $this->thumbnail = $this->content['thumbnail'] ?? null;
    }

    public static function createFromType($post_type, $content)
    {
        switch ($post_type) {
            case 'addon-post':
                return new AddonPostContent($content);
            case 'addon-introduction':
                return new AddonIntroductionContent($content);
            case 'page':
                return new PageContent($content);
        }
    }

    public function __toString()
    {
        return json_encode($this->content);
    }
}
