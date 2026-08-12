<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

class NormalizeUrl
{
    /**
     * サブデリミタのうちクエリの区切り文字(&, =)を除いたもの。
     */
    private const string QUERY_SAFE_CHARS = "!$'()*+,;:@%";

    /**
     * パスセグメントで許容するサブデリミタ一式。
     */
    private const string PATH_SAFE_CHARS = "!$&'()*+,;=:@%";

    /**
     * HTTPリクエストラインに安全に載せられるようURLを正規化する。
     * パス・クエリの非ASCII文字（日本語等）を percent-encode する。
     * 既に正しくpercent-encodeされている部分は二重エンコードしない。
     */
    public function __invoke(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return $url;
        }

        $normalized = $parts['scheme'].'://'.$parts['host'];

        if (isset($parts['port'])) {
            $normalized .= ':'.$parts['port'];
        }

        if (isset($parts['path'])) {
            $normalized .= $this->encodePath($parts['path']);
        }

        if (isset($parts['query'])) {
            $normalized .= '?'.$this->encodeQuery($parts['query']);
        }

        return $normalized;
    }

    private function encodePath(string $path): string
    {
        $segments = array_map(
            fn (string $segment): string => $this->encodeComponent($segment, self::PATH_SAFE_CHARS),
            explode('/', $path)
        );

        return implode('/', $segments);
    }

    /**
     * parse_str()/http_build_query() は「=」なしパラメータへの「=」付与、
     * キー中の「.」「 」の「_」への置換、重複キーの欠落を引き起こすため使わない。
     * 区切り文字(&, =)だけで分割し、各要素を個別にエンコードする。
     */
    private function encodeQuery(string $query): string
    {
        $pairs = explode('&', $query);

        $encoded = array_map(function (string $pair): string {
            if (! str_contains($pair, '=')) {
                return $this->encodeComponent($pair, self::QUERY_SAFE_CHARS);
            }

            [$key, $value] = explode('=', $pair, 2);

            return $this->encodeComponent($key, self::QUERY_SAFE_CHARS).'='.$this->encodeComponent($value, self::QUERY_SAFE_CHARS);
        }, $pairs);

        return implode('&', $encoded);
    }

    /**
     * 未エンコードの非ASCII文字・記号のみをpercent-encodeする。
     * 既存の正しい「%XX」形式はそのまま保持し、二重エンコードや
     * 「%」に偶然続く16進数字風の文字列を壊すことを避ける。
     */
    private function encodeComponent(string $value, string $safeChars): string
    {
        $pattern = '/%(?![0-9A-Fa-f]{2})|[^A-Za-z0-9\-_.~'.preg_quote($safeChars, '/').']/u';

        return preg_replace_callback(
            $pattern,
            static fn (array $matches): string => rawurlencode($matches[0]),
            $value
        ) ?? $value;
    }
}
