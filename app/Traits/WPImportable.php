<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

trait WPImportable
{
    private $conn = null;

    private function getWPConnection()
    {
        if(is_null($this->conn)) {
            $this->conn = DB::connection('wp');
        }
        return $this->conn;
    }

    /**
     * ユーザー一覧取得
     */
    private function fetchWPUsers()
    {
        return $this->getWPConnection()->select(
            "SELECT * FROM wp_users");
    }
    /**
     * ユーザーメタ一覧取得
     */
    private function fetchWPUsermeta($user_id)
    {
        return $this->getWPConnection()->select(
            "SELECT * FROM wp_usermeta WHERE user_id=?", [$user_id]);
    }
    /**
     * ユーザー投稿一覧取得
     */
    private function fetchWPPosts($user_id)
    {
        return $this->getWPConnection()->select(
            "SELECT * FROM wp_posts WHERE post_author=? AND post_type='post'", [$user_id]);
    }

    /**
     * サムネイル投稿取得
     */
    private function fetchWPThumbnail($id)
    {
        return $this->getWPConnection()->select(
            "SELECT
                *
            FROM
                wp_posts p
            WHERE
                p.ID = (SELECT
                        pm.meta_value
                    FROM
                        wp_posts p
                            LEFT JOIN
                        wp_postmeta pm ON pm.post_id = p.ID
                    WHERE
                        p.ID = ?
                            AND pm.meta_key = '_thumbnail_id')
                    AND post_type = 'attachment'", [$id])[0] ?? null;
    }

    /**
     * アドオン投稿取得
     */
    private function fetchWPAddonFile($id)
    {
        return $this->getWPConnection()->select(
            "SELECT
                *
            FROM
                wp_posts p
            WHERE
                p.ID = (SELECT
                        pm.meta_value
                    FROM
                        wp_posts p
                            LEFT JOIN
                        wp_postmeta pm ON pm.post_id = p.ID
                    WHERE
                        p.ID = ?
                            AND pm.meta_key = 'addon-file')
                    AND post_type = 'attachment'", [$id])[0] ?? null;
    }

    /**
     * 指定post_id, meta_keyのmeta_valueを返す
     * @param int $post_id 投稿ID
     * @param string $meta_key キー名
     * @return mixed meta_value
     */
    private function fetchWPPostmetaValueBy($post_id, $meta_key)
    {
        return $this->getWPConnection()->select(
            "SELECT * FROM wp_postmeta WHERE post_id=? AND meta_key=?", [$post_id, $meta_key])[0]->meta_value ?? null;
    }

    /**
     * 指定post_id, taxonomyのターム一覧を返す
     */
    private function fetchWPTerms($post_id, $taxonomy)
    {
        return $this->getWPConnection()->select(
            "SELECT
                t.name, t.slug
            FROM
                wp_terms t
                    LEFT JOIN
                wp_term_taxonomy tx ON tx.term_id = t.term_id
                    LEFT JOIN
                wp_term_relationships tr ON tr.term_taxonomy_id = tx.term_taxonomy_id
                    LEFT JOIN
                wp_posts p ON p.ID = tr.object_id
            WHERE
                p.ID = ?
            AND
                tx.taxonomy = ?", [$post_id, $taxonomy]);
    }

    /**
     * 指定post_idのpost_view一覧を返す。週次は除外
     */
    private function fetchWPPostViews($post_id)
    {
        return $this->getWPConnection()->select(
            "SELECT
                *
            FROM
                wp_post_views
            WHERE
                id = ? AND type IN (0, 2, 3, 4)", [$post_id]);
    }

    /**
     * https -> http
     * シリアライズ不整合の修復
     * 無理やりHTTPS化したときの後遺症
     */
    private static function fixSerializedStr($str)
    {
        return str_replace(
            'https://simutrans.sakura.ne.jp/portal/wp-content/uploads',
            'http://simutrans.sakura.ne.jp/portal/wp-content/uploads', $str);
    }
    /**
     * http -> https
     */
    private static function recoverURL($str)
    {
        return str_replace(
            'http://simutrans.sakura.ne.jp/portal/wp-content/uploads',
            'https://simutrans.sakura.ne.jp/portal/wp-content/uploads', $str);
    }
    private static function searchMetaItem($wp_usermeta, $meta_key)
    {
        return $wp_usermeta->first(function($m) use($meta_key) {
            return $m->meta_key === $meta_key; })->meta_value ?? null;
    }

    private static function saveFromUrl($user_id, $url, $filename = null)
    {
        $filename = $filename ?: hash('md5', $url).'.'.self::getExtention($url);
        $temp = tmpfile();
        fwrite($temp, @file_get_contents($url));
        $path = 'user/'.$user_id.'/'.$filename;
        Storage::put('public/'.$path, $temp);
        return $path;
    }

    private static function getExtention($str)
    {
        $tmp = explode('.', $str);
        return end($tmp);
    }
}
