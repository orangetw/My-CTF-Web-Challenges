<?php

    if (count($argv) != 4)
        die('arg error');

    $filename = $argv[1];
    $position = (int)$argv[2];
    $bit_pos  = (int)$argv[3];

    // check filename
    $filename = realpath($filename);
    if (substr($filename, 0, 14) != "/var/www/html/")
        die('filename error');
    if (!file_exists($filename))
        die('filename error');
    if (filesize($filename) < $position + 1)
        die('position error');
    if ($bit_pos < 0 || $bit_pos > 7) 
        die('bit error');

    $content = file_get_contents($filename);

    $head = substr($content, 0, $position);
    $byte = substr($content, $position, 1);
    $tail = substr($content, $position + 1);

    $byte = chr( ord($byte) ^ (1<<$bit_pos) );
    
    file_put_contents($filename, $head . $byte . $tail);
    echo 'all good';