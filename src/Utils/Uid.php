<?php

namespace App\Utils;

class Uid {

  public function generate($limit = 10) {

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $max = strlen($characters) - 1;
    $uid = '';

    for ($i = 0; $i < $limit; $i++) {
      $uid .= $characters[rand(0, $max)];
    }

    return $uid;
  }
}
