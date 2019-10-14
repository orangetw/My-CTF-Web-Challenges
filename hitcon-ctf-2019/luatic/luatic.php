<?php
    /* Author: Orange Tsai(@orange_8361) */
    include "config.php";

    foreach($_REQUEST as $k=>$v) {
        if( strlen($k) > 0 && preg_match('/^(FLAG|MY_|TEST_|GLOBALS)/i',$k)  )
            exit('Shame on you');
    }

    foreach(Array('_GET','_POST') as $request) {
        foreach($$request as $k => $v) ${$k} = str_replace(str_split("[]{}=.'\""), "", $v);
    }

    if (strlen($token) == 0) highlight_file(__FILE__) and exit();
    if (!preg_match('/^[a-f0-9-]{36}$/', $token)) die('Shame on you');

    $guess = (int)$guess;
    if ($guess == 0) die('Shame on you');

    // Check team token
    $status = check_team_redis_status($token);
    if ($status == "Invalid token") die('Invalid token');
    if (strlen($status) == 0 || $status == 'Stopped') die('Start Redis first');

    // Get team redis port
    $port = get_team_redis_port($token);
    if ((int)$port < 1024) die('Try again');
    
    // Connect, we rename insecure commands
    // rename-command CONFIG ""
    // rename-command SCRIPT ""
    // rename-command MODULE ""
    // rename-command SLAVEOF ""
    // rename-command REPLICAOF ""
    // rename-command SET $MY_SET_COMMAND
    $redis = new Redis();
    $redis->connect("127.0.0.1", $port);
    if (!$redis->auth($token)) die('Auth fail');

    // Check availability
    $redis->rawCommand($MY_SET_COMMAND, $TEST_KEY, $TEST_VALUE);
    if ($redis->get($TEST_KEY) !== $TEST_VALUE) die('Something Wrong?');

    // Lottery!
    $LUA_LOTTERY = "math.randomseed(ARGV[1]) for i=0, ARGV[2] do math.random() end return math.random(2^31-1)";
    $seed  = random_int(0, 0xffffffff / 2);
    $count = random_int(5, 10);
    $result = $redis->eval($LUA_LOTTERY, array($seed, $count));

    sleep(3); // Slow down...
    if ((int)$result === $guess)
        die("Congratulations, the flag is $FLAG");
    die(":(");
