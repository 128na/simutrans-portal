<?php

declare(strict_types=1);

namespace App\Actions\SendSNS\Article;

use App\Actions\SendSNS\Article\Data\XDigestArticle;
use App\Actions\SendSNS\Article\Data\XDigestArticles;
use Twitter\Text\Parser;

/**
 * Xデイリー集約投稿の本文を組み立てる。
 *
 * タイトルは重み付き54文字を上限に省略(超過時は末尾を「…」に置換)し、
 * 本文全体を twitter-text (Twitter\Text\Parser) で重み付き280文字以内に検証する。
 * 超過する場合は、掲載件数を減らさず・投稿を分割せず、タイトルの上限文字数を
 * 段階的に縮めて再構築する(ADR-0005)。
 */
class BuildXDigestText
{
    private const int MAX_WEIGHTED_LENGTH = 280;

    private const int DEFAULT_TITLE_MAX_WEIGHTED_LENGTH = 54;

    private const int TITLE_MAX_WEIGHTED_LENGTH_STEP = 2;

    public function __construct(private Parser $parser, private GetArticleParam $getArticleParam) {}

    public function __invoke(XDigestArticles $digest): string
    {
        $titleMaxWeightedLength = self::DEFAULT_TITLE_MAX_WEIGHTED_LENGTH;
        $text = $this->build($digest, $titleMaxWeightedLength);

        while (
            $this->parser->parseTweet($text)->weightedLength > self::MAX_WEIGHTED_LENGTH
            && $titleMaxWeightedLength > self::TITLE_MAX_WEIGHTED_LENGTH_STEP
        ) {
            $titleMaxWeightedLength -= self::TITLE_MAX_WEIGHTED_LENGTH_STEP;
            $text = $this->build($digest, $titleMaxWeightedLength);
        }

        return $text;
    }

    private function build(XDigestArticles $digest, int $titleMaxWeightedLength): string
    {
        $items = $digest->items
            ->map(fn (XDigestArticle $item): string => __('notification.digest.item', [
                'title' => $this->truncateTitle($item->article->title, $titleMaxWeightedLength),
                'url' => ($this->getArticleParam)($item->article)['url'],
            ]))
            ->implode("\n\n");

        $text = __('notification.digest.header')."\n\n".$items;

        $remaining = $digest->totalCount - $digest->items->count();

        if ($remaining > 0) {
            $text .= "\n\n".__('notification.digest.more', ['count' => $remaining]);
        }

        return $text;
    }

    private function truncateTitle(string $title, int $maxWeightedLength): string
    {
        if ($this->parser->parseTweet($title)->weightedLength <= $maxWeightedLength) {
            return $title;
        }

        $truncated = '';

        foreach (mb_str_split($title) as $char) {
            $candidate = $truncated.$char.'…';

            if ($this->parser->parseTweet($candidate)->weightedLength > $maxWeightedLength) {
                break;
            }

            $truncated .= $char;
        }

        return $truncated.'…';
    }
}
