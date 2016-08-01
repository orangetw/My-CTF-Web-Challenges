<?php
  header('Content-Type: application/json; charset=utf-8');
  $API = 'http://127.0.0.1:8080/waf/';

  $query = $_GET['query'];
  if ( isset($query) && is_string($query) ){

    $url = sprintf("%s/?query=%s", $API, base64_encode($query));
    $data = @file_get_contents($url);
    echo $data;

  } else {
    echo '{"msg": "nothing happened"}';
  }

?>