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

    private function fetchWPUsers()
    {
        return $this->getWPConnection()->select("select * from wp_users");
    }

    private function fetchWPUsermeta($user_id)
    {
        return $this->getWPConnection()->select("select * from wp_usermeta where user_id=?", [$user_id]);
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
     * シリアライズ不整合の修復
     * 無理やりHTTPS化したときの後遺症
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

    private static function saveFromUrl($user_id, $url, $filename)
    {
        $temp = tmpfile();
        fwrite($temp, @file_get_contents($url));
        $path = 'public/user/'.$user_id.'/'.$filename;
        Storage::put($path, $temp);
        return $path;
    }

    private static function getExtention($str)
    {
        $tmp = explode('.', $str);
        return end($tmp);
    }
}
