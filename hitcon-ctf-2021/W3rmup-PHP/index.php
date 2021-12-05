<?php
    if (!isset($_GET['mail']))
        highlight_file(__FILE__) && exit();

    $mail    = filter_var($_GET['mail'],           FILTER_VALIDATE_EMAIL);
    $addr    = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP);
    $country = geoip_country_code_by_name($addr);
    
    if (!$addr    || strlen($addr)    == 0) die('bad addr');
    if (!$mail    || strlen($mail)    == 0) die('bad mail');
    if (!$country || strlen($country) == 0) die('bad country');

    $yaml = <<<EOF
    - echo          # cmd
    - $addr         # address
    - $country      # country
    - $mail         # mail
    EOF;
    $arr = yaml_parse($yaml);
    if (!$arr) die('bad yaml');

    for ($i=0; $i < count($arr); $i++) { 
        if (!$arr[$i]) {
            unset($arr[$i]);
            continue;
        }
        $arr[$i] = escapeshellarg($arr[$i]);
    }

    system(implode(" ", $arr));