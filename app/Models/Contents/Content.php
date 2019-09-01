<?php

namespace App\Models\Contents;

class Content
{
    protected $content = [];
    protected $attributes = ['thumbnail'];

    public function __construct($content = [])
    {
        $content = is_array($content) ? $content : json_decode($content , true);

        foreach ($this->attributes as $key => $value) {
            if(is_numeric($key)) {
                $this->content[$value] = $content[$value] ?? null;
            } else {
                $this->content[$key] = $content[$key] ?? $value;
            }
        }
    }

    public static function createFromType($post_type, $content)
    {
        $content = is_array($content) ? $content : json_decode($content , true);
        switch ($post_type) {
            case 'addon-post':
                return new AddonPostContent($content);
            case 'addon-introduction':
                return new AddonIntroductionContent($content);
            case 'page' && array_key_exists('sections', $content):
                return new PageContent($content);
            case 'page' && array_key_exists('data', $content):
                return new MarkdownContent($content);
        }
    }
    public function isAddonIntroductionContent() {
        return $this instanceof AddonIntroductionContent;
    }
    public function isAddonPostContent() {
        return $this instanceof AddonPostContent;
    }
    public function isPageContent() {
        return $this instanceof PageContent;
    }
    public function isMarkdownContent() {
        return $this instanceof MarkdownContent;
    }

    public function __toString()
    {
        return json_encode($this->content);
    }
    public function __get($key)
    {
        if(in_array($key, $this->attributes, true) || array_key_exists($key, $this->attributes)) {
            return $this->content[$key] ?? null;
        }
    }
    public function __set($key, $value)
    {
        if(in_array($key, $this->attributes, true) || array_key_exists($key, $this->attributes)) {
            $this->content[$key] = $value;
            return;
        }
    }
}
