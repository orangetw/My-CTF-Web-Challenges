<?php
    $salt = $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'];
    $upload_dir = 'upload/' . md5($salt);
    if (!is_dir($upload_dir)) mkdir($upload_dir);

    $file = @$_FILES['file'];
    if (!isset($file)) highlight_file(__FILE__) && exit();

    $filename =$file['name'];
    $tmp_name = $file['tmp_name'];
    $filesize = $file['size'];
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    $ext = strtolower($ext);

    if (!$ext)                        die('🤔');
    if ($filesize > 0x1337)           die('🤔');
    if (strstr($ext, 'x')   != False) die('🤔');
    if (strstr($ext, 'ht')  != False) die('🤔');
    if (strstr($ext, 'ph')  != False) die('🤔');
    if (strstr($ext, 'ini') != False) die('🤔');
    if (strstr($ext, 'htm') != False) die('🤔');
    if (strstr($ext, 'xml') != False) die('🤔');
    if (strstr($ext, 'svg') != False) die('🤔');
    if (strstr($ext, 'app') != False) die('🤔');

    $dst = sprintf('%s/%s.%s', $upload_dir, md5_file($tmp_name), $ext);
    move_uploaded_file($tmp_name, $dst);
    echo htmlentities($dst);