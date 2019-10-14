<?php
    $config = json_decode(file_get_contents("/bot/config.json"));
    $ip = $_SERVER['REMOTE_ADDR'];
    if ($_SERVER['REMOTE_ADDR'] == $config->server_ip) {
        setcookie("flag", $config->flag);
    }