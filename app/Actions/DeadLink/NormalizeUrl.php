<?php

declare(strict_types=1);

namespace App\Actions\DeadLink;

class NormalizeUrl
{
    /**
     * HTTPリクエストラインに安全に載せられるようURLを正規化する。
     * パス・クエリの非ASCII文字（日本語等）を percent-encode し、
     * 非ASCIIホストは punycode に変換する。
     */
    public function __invoke(string $url): string
    {
        $parts = parse_url($url);

        if ($parts === false || ! isset($parts['scheme'], $parts['host'])) {
            return $url;
        }

        $normalized = $parts['scheme'].'://';

        if (isset($parts['user'])) {
            $normalized .= $parts['user'];
            if (isset($parts['pass'])) {
                $normalized .= ':'.$parts['pass'];
            }
            $normalized .= '@';
        }

        $normalized .= $this->encodeHost($parts['host']);

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

    private function encodeHost(string $host): string
    {
        if (mb_check_encoding($host, 'ASCII')) {
            return $host;
        }

        if (! function_exists('idn_to_ascii')) {
            return $host;
        }

        $ascii = idn_to_ascii($host, IDNA_DEFAULT, INTL_IDNA_VARIANT_UTS46);

        return $ascii !== false ? $ascii : $host;
    }

    private function encodePath(string $path): string
    {
        $segments = array_map(
            static fn (string $segment): string => rawurlencode(rawurldecode($segment)),
            explode('/', $path)
        );

        return implode('/', $segments);
    }

    private function encodeQuery(string $query): string
    {
        parse_str($query, $params);

        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
