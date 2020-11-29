<?php
    error_reporting(0);
    $fid = 1337;

    function get($name) {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return false;
    }

    function set($name, $value = null) {
        if (empty($name)) return false;
        setcookie($name, $value);
        return true;
    }

    function getVisitor() {
        $sign = get('visitor');
        if (empty($sign)) return false; 
        $sign = base64_decode($sign);
        return $sign;
    }

    function signVisitor($extension = array()) {
        $sign = base64_encode(serialize($extension));
        set('visitor', $sign);
    }

    $vistor = getVisitor();
    if (!$vistor) highlight_file(__FILE__) && die();
    $ext = unserialize($vistor);

    if (isset($ext['currentFid']) && $ext['currentFid'] == $fid) die('GG');
    signVisitor(array('currentFid'=>$fid, 'beforeFid'=>$ext['currentFid']));
