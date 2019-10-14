<?php

    error_reporting(E_ALL^E_NOTICE);

    $FLAG = 'hitcon{Lua^H Red1s 1s m4g1c!!!}';
    $MY_SET_COMMAND = sha1("orangenoggn0ggN0gg><");
    $TEST_KEY   = bin2hex(random_bytes(32)); 
    $TEST_VALUE = bin2hex(random_bytes(32));

    function check_team_redis_status($token) {
        $status = exec("sudo /redis/cmd.py " . escapeshellarg($token) . " status");
        return trim($status);
    }

    function get_team_redis_port($token) {
        $status = exec("sudo /redis/cmd.py " . escapeshellarg($token) . " port");
        return (int)$status;
    }
