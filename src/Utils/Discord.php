<?php

namespace App\Utils;

class Discord {

  public function post_event($channel = null, $data = null, $link) {
    if(!$channel || !$data || !$link) {
      return;
    }

    $url = $_ENV['DISCORD_BOT_URL'];

    $postdata = http_build_query(
  		array(
        'channel' => $channel,
  			'title' => $data->getName(),
  			'description' => $data->getDescription(),
        'link' => $link,
        'start_at' => $data->getStartAt()->format('Y-m-d H:i:s'),
        'duration' => $data->getDuration() . ' heure(s)',
        'size' => (($size = $data->getGroupSize()) == 99) ? '50+' : $size,
        'join' => ($data->getGroupSize() > 5) ? '/sqjoin ' . $data->getUser()->getAccountName() : '/join ' . $data->getUser()->getAccountName()
  		)
  	);

  	$opts = array('http' =>
  		array (
  			'method' => 'POST',
  			'header' => 'Content-type: application/x-www-form-urlencoded',
  			'content' => $postdata
  		)
  	);

  	$context  = stream_context_create($opts);

  	$url = "{$url}/event/add";
  	$result = file_get_contents($url, false, $context);

    return $result;
  }
}
