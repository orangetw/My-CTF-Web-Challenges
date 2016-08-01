<?php

	$host = 'localhost';
	$port = 5435;

	$user = 'sa';
	$pass = 'sa';

	$dbname = '/www/h2/name';

	$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass") or die('connect error');

	function error($sql){
		$error = pg_last_error();
		$sql .= "\n" . $error . "\n\n\n";
		file_put_contents( '/www/h2/__log__.txt', $sql, FILE_APPEND|LOCK_EX );

		// return $error;
		return 'query error';
	}

	/*
		CREATE TABLE users( id SERIAL PRIMARY KEY, role text NOT NULL, username text NOT NULL, password text NOT NULL, ip text NOT NULL );
		INSERT INTO users(role, username,password,ip) values('user', 'orange','i_am_normal_user_too', '127.0.0.1')
	*/
	// $r = pg_query($_POST[sql]) or die( pg_last_error() );
	// print_r( pg_fetch_object($r) );



?>