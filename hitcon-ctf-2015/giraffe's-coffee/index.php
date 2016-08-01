<?php 
    include "config.php";

    mysql_connect($dbhost, $dbuser, $dbpass);
    mysql_select_db($dbname);

    function escape($str){
        $str = strtolower($str);
        $str = str_replace("'", "", $str);
        $str = str_replace("\\", "", $str);
        $str = trim($str);
        return $str;
    }

    function random_str($length){
        $base = '0123456789abcdefghijklmnopqrstuvwxyz';
        $str = '';
        for ($i=0; $i<$length; $i++){
            $str .= $base[ mt_rand(0, strlen($base)-1) ];
        }
        return $str;
    }

    function simple_mail($mail, $msg){
        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        @socket_connect($socket, $mail, 110);
        @socket_write($socket, "token=$msg\n");
        @socket_close($socket);
    }

    function check_user_exists($username){
        $sql    = sprintf("SELECT * FROM users WHERE username='%s'", $username);
        $result = mysql_query($sql);
        $count  = mysql_num_rows($result);

        if ( $count > 0 ){
            return True;
        } else {
            return False;
        }
    }

    function get_user_profile($username){
        $sql = sprintf("SELECT * FROM users WHERE username='%s'", $username);
        $result = mysql_query($sql);
        $record = mysql_fetch_object($result);
        return $record;
    }

    function user_regiseter($username, $password, $mail){
        $sql = sprintf("INSERT INTO users(username, password, mail) VALUES('%s', '%s', '%s')", $username, md5($password), $mail );
        $result = mysql_query($sql);
    }

    function update_user_password($username, $password){
        $sql = sprintf("UPDATE users SET password='%s' WHERE username='%s'", 
                       md5($password), 
                       $username );
        $result = mysql_query($sql);
    }

    function check_token_exists($token){
        $sql = sprintf("SELECT * FROM tokens WHERE ip='%s' and token='%s'", 
                       $_SERVER['REMOTE_ADDR'], 
                       $token);
        $result = mysql_query($sql);
        $count  = mysql_num_rows($result);
        if ( $count > 0 ){
            return True;
        } else {
            return False;
        }
    }

    function get_token($token){
        $sql = sprintf("SELECT * FROM tokens WHERE ip='%s' and token='%s'", 
                       $_SERVER['REMOTE_ADDR'], 
                       $token);
        $result = mysql_query($sql);
        return mysql_fetch_object($result);
    }

    function insert_token($username, $token){
        $sql = sprintf("INSERT INTO tokens(username, token, ip) VALUES('%s', '%s', '%s')", 
                       $username, 
                       $token, 
                       $_SERVER['REMOTE_ADDR'] );
        $result = mysql_query($sql);
    }

    function delete_token($data, $by_what='token'){
        $sql = sprintf("DELETE FROM tokens WHERE %s='%s' and ip='%s'", 
                           $by_what,
                           $data, 
                           $_SERVER['REMOTE_ADDR']);
        $result = mysql_query($sql);
    }

    function insert_fail($ip){
        $sql = sprintf("INSERT INTO fail2ban(ip) VALUES('%s')", $ip);
        $result = mysql_query($sql);
    }

    function get_fail_count($ip){
        $sql = sprintf("SELECT * FROM fail2ban WHERE ip='%s'", $ip);
        $result = mysql_query($sql);
        $count  = mysql_num_rows($result);
        return $count;
    }


    $mode = $_POST['mode'];
    if ( $mode == 'login' ){
        $username = escape( $_POST['username'] );
        $password = escape( $_POST['password'] );

        $record = get_user_profile($username);
        if ( $record->password == md5($password) ){
            if ( $record->username == 'admin' ){
                $new_password = random_str(16);
                update_user_password('admin', $new_password);
                
                print 'Congratulations, the flag is ' . $FLAG;
            } else {
                print 'you are not admin';
            }
        } else {
            print 'login failed';
        }
    } else if ( $mode == 'register' ){
        $username = escape( $_POST['username'] );
        $password = escape( $_POST['password'] );
        $mail    = $_SERVER['REMOTE_ADDR'];

        if (strlen($username) < 3 or strlen($password) < 3){
            print 'too small';
        } else if (strlen($username) > 255 or strlen($password) > 255){
            print 'too large';
        } else if ( check_user_exists($username) ){
            print 'user registed';
        } else {
            user_regiseter($username, $password, $mail);
            print 'register ok';
        }
    } else if ( $mode == 'verify' ){
        $token = escape($_POST['token']);
        $ip    = $_SERVER['REMOTE_ADDR'];

        $token = (int)$token ^ ip2long($ip);
        if ( get_fail_count($ip) > 1000 ){
            print 'dont brute force';
        } else if ( check_token_exists($token) ){
            $record = get_token($token);
            $new_password = random_str(16);

            update_user_password($record->username, $new_password);
            delete_token($token);

            print 'ok. your new password: ' . $new_password;
        } else {
            insert_fail($ip);
            print 'what do you do?';
        }

    } else if ( $mode == 'reset' ){
        $username = escape($_POST['username']);

        if ( !check_user_exists($username) ){
            print 'user not registed';
        } else {
            $record = get_user_profile($username);
            $username = $record->username;
            $mail     = $record->mail;
            $key      = $_SERVER['REMOTE_ADDR'];
            $token = ip2long($key) ^ mt_rand();

            delete_token($username, 'username');
            insert_token($username, $token);
            simple_mail($mail, $token);
        }

    } else {
        readfile('index.tpl.html');
    }
?>