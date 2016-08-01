<?php
    header('Content-Type: application/json; charset=utf-8');

    $API = 'http://127.0.0.1:8080/waf/';
    $host = 'localhost';
    $port = 5435;
    $user = 'sa';
    $pass = 'sa';
    $dbname = 'news';

    $conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass") or die("db error");


    $id = $_GET['id'];

    if (isset($id) && is_string($id)){
        $url = sprintf("%s/?query=%s", $API, base64_encode($id));
        $data = @file_get_contents($url);
        $data = json_decode($data);
        if ($data->msg == "legal"){
            $res = pg_query(sprintf("SELECT * FROM news WHERE id='%s'", $id));
            $result = pg_fetch_object($res);

            @pg_query(sprintf("UPDATE news SET hits=hits+1 WHERE id='%s'", $id));

            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode(array("msg"=>"nothing happened"));
            }
        } else {
            echo json_encode($data);
        }
    } else {
        $res = pg_query("SELECT * FROM news");

        $result = array();
        while ($data = pg_fetch_object($res)){
            $result[] = $data;
        }
        
        echo json_encode($result);
    }


    
    // $r = pg_query($_POST[sql]) or die( pg_last_error() );
    // print_r( pg_fetch_object($r) );
    // CREATE TABLE news( id SERIAL PRIMARY KEY, title text NOT NULL, content text NOT NULL, hits int NOT NULL );
    // INSERT INTO news(title, content, hits) values('Hello Wolrd:)', 'This is a example news content.',1)
    