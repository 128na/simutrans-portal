<?php
namespace App\Services;

use App\Models\Article;

class CheckDeadLinkService extends Service
{
    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function getTargetArticles()
    {
        return $this->model
        // ->select('id', 'title', 'post_type', 'contents')
            ->active()
            ->linkCheckTarget()
            ->with('user')
            ->cursor();
    }

    public function isLinkDead(Article $article)
    {
        $link = $article->contents->link ?? null;

        if ($link) {
            return !$this->isStatusOK($link);
        }
    }

    private function isStatusOK($url)
    {
        $info_list = @get_headers($url) ?: [];
        foreach ($info_list as $info) {
            if (stripos($info, ' 200 OK') !== false) {
                return true;
            }
        }
        return false;
    }
}
