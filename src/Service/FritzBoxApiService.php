<?php
namespace Maschinenraum\FbApi\Service;

class FritzBoxApiService
{
    public function request($endpoint)
    {
        $fritz_url = getenv('FRITZBOX_HOST');
        $fritz_pwd = getenv('FRITZBOX_PASSWORD');

        $l = simplexml_load_string(file_get_contents(sprintf('http://%s/login_sid.lua', $fritz_url)));
        $c = $l->Challenge;

        $c_str = sprintf("%s-%s", $c, $fritz_pwd);
        $md_str = md5(iconv("UTF-8", "UTF-16LE", $c_str));
        $l = simplexml_load_string(file_get_contents(sprintf('http://%s/login_sid.lua?user=&response=%s', $fritz_url, $c . '-' . $md_str)));
        $sid = $l->SID;

        $callListString = file_get_contents(sprintf('http://%s:49000/%s?sid=%s', $fritz_url, $endpoint, $sid));
        return $callListString;
    }
}