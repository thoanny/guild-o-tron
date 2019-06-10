<?php

namespace App\Utils;

class Gw2Api {

  private $url = 'https://api.guildwars2.com/v2';
  private $lang = 'fr';

  public function get($endpoint = null, $token = null, $data = null, $attr = null, $lang) {
    if(!$endpoint) {
      return;
    }

    if($data) {
      foreach($data as $k => $v) {
        $v = str_replace(' ', '%20', $v);
        $endpoint = str_replace( ":$k", $v, $endpoint);
      }
    }

    if($attr) {
      $attr = http_build_query($attr);
    }

    $lang = (isset($lang)) ? $lang : $this->lang;

    $url = $this->url . $endpoint . '?lang=' . $lang . (($token) ? '&access_token=' . $token : '') . (($attr) ? '&' . $attr : '');
    $url = iconv("UTF-8", "ISO-8859-1", $url);

    $result = false;
    $json = @file_get_contents( $url );

    if($json) {
      $result = json_decode($json);
    }

    return $result;
  }
}
