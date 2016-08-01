<?php
    /*
        sqlpwn by orange
        Don't brute force or you will be banned !
    */

    session_start();
    error_reporting(0);

    include "template.html";
    include "config.php";
    $conn = mysql_connect($dbhost, $dbuser, $dbpass);
    mysql_select_db($dbname);
    mysql_query("SET sql_mode='strict_all_tables'");

    function check_login(){
        if ( !isset($_SESSION['name']) ){
            exit('not login');
        }
    }

    function escape($str){
        $str = mysql_real_escape_string($str);
        return $str;
    }

    $mode = $_GET['mode'];
    if ( $mode == 'admin' ){
        check_login();
        if ( $_SESSION['admin'] != 1 ){
            exit('not admin');
        }

        include getcwd() . '/' . $_GET['boom'] . "php";

    } else if ( $mode == 'post' ){
        check_login();

        $name = $_SESSION['name'];
        $titl = $_POST['title'];
        $note = $_POST['note'];
        

        $note = trim(escape($note));
        $titl = trim(escape($titl));

        if ( strlen($note) < 6 ){
            exit('para error');
        }
        if ( strlen($titl) < 6 ){
            exit('para error');
        }

        if ( strlen($note) > 128 ){
            $note = substr($note, 0, 128);
        }
        if ( strlen($titl) > 32 ){
            $titl = substr($titl, 0, 32);
        }

        mysql_query(sprintf("INSERT INTO notes(name, title, note) VALUES('%s', '%s', '%s')", $name, $titl, $note));

        echo 'ok';


    } else if ( $mode == 'show' ){
        check_login();

        $id = (int)$_GET['id'];
        $r = mysql_query(sprintf("SELECT * FROM notes WHERE id='%d'", $id));
        if ( mysql_num_rows($r) == 0 ){
            exit('id not found');
        } 

        $result = mysql_fetch_object($r);
        if ( $result->name !== $_SESSION['name'] ){
            exit('not posted by you');
        }

        $name = $_SESSION['name'];
        $titl = $result->title;
        $note = $result->note;

        echo "title " . $titl;
        echo "<br>";
        echo "note " . $note;

        exit();

    } else if ( $mode == 'register' ) {
        $name = $_POST['name'];
        $pass = $_POST['pass'];

        $name = trim( escape( $name ) );
        $pass = trim( escape( $pass ) );
        if ( strlen($name) < 6 ){
            exit('para error');
        }
        if ( strlen($pass) < 6 ){
            exit('para error');
        }

        $r = mysql_query(sprintf("SELECT * FROM users WHERE name='%s'", $name));
        if ( mysql_num_rows($r) > 0 ){
            exit('duplicated');
        }

        $sql = sprintf("INSERT INTO users(name, pass) VALUES('%s', '%s')", $name, md5($pass));
        mysql_query($sql);

        $sql = sprintf("INSERT INTO locks(name) VALUES('%s')", $name);
        mysql_query($sql);

        echo 'ok';


    } else if ( $mode == 'login' ) {
        $name = $_POST['name'];
        $pass = $_POST['pass'];

        $name = trim( escape( $name ) );
        $pass = trim( escape( $pass ) );

        $r = mysql_query(sprintf("SELECT * FROM users WHERE name='%s'", $name));
        if ( mysql_num_rows($r) == 0 ){
            exit('user not found');
        }
        
        $result = mysql_fetch_object($r);
        if ( $result->pass !== md5($pass) ){
            exit('pass incorrect');
        }

        $r = mysql_query(sprintf("SELECT * FROM locks WHERE name='%s'", $name));
        if ( mysql_num_rows($r) > 0 ){
            exit('user locked');
        } 

        $_SESSION['id']   = $result->id;
        $_SESSION['name'] = $result->name;
        $_SESSION['pass'] = $result->pass;

        if ( $name == 'orange' ){
            $_SESSION['admin'] = 1;
        }

        echo 'ok';

    } else if ( $mode == 'info' ){
        phpinfo();

    } else if ( $mode == 'flag' ){
        check_login();
        echo $flag;

    } else {

        // I am always on your top >/////<
        $r = mysql_query(sprintf("SELECT * FROM notes WHERE name='orange' UNION SELECT * FROM notes ORDER BY id DESC LIMIT 100"));
        while ($row = mysql_fetch_object($r)) {
            $id   = $row->id;
            $titl = $row->title;

            echo sprintf("<li><a href='./sqlpwn.php?mode=show&id=%d'>%s</a></li>\n", $id, $titl);
            echo "<br>";
        }
    }