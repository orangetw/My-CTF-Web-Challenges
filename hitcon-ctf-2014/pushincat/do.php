<?php

session_start();
include "config.php";

$action = $_POST['action'];
if ($action == 'logout'){
	session_destroy();

}else if ($action == 'login'){

	$username = $_POST['username'];
	$password = $_POST['password'];

	if ( $username == '' or $password == '' ){
		die( 'parameter error' );
	}

	$username = pg_escape_string($username);
	$password = pg_escape_string($password);
	$sql = "SELECT * FROM users WHERE username='$username' and password='$password'";
	$res = pg_query($sql);
	
	if ( pg_num_rows($res) > 0 ){
			
		$result = pg_fetch_object($res);
		$_SESSION['role'] = $result->role;
		$_SESSION['ip']   = $result->ip;
		$_SESSION['username'] = $result->username;
		$_SESSION['password'] = $result->password;

		die( 'login ok' );
	} else {
		die( 'login failed' );
	}


} else if ($action == 'register'){

	$role = 'user';
	$ip   = $_SERVER['REMOTE_ADDR'];
	$username = $_POST['username'];
	$password = $_POST['password'];	

	if ( $username == '' or $password == '' ){
		die( 'parameter error' );
	}

	$username = pg_escape_string($username);
	$sql = "SELECT * FROM users WHERE username='$username'";
	$res = pg_query($sql);
	
	if ( pg_num_rows($res) != 0 ){
		die( 'registed :(' );
	}

	$sql = "INSERT INTO users(role, username, password, ip) VALUES('user', '%s', '%s', '%s')";
	$sql = sprintf($sql, $username, $password, $ip);
	// $res = pg_query($sql) or die( pg_last_error() );
	$res = pg_query($sql) or die( error($sql) );

	die( 'register ok' );

} else if ($action == 'flag'){

	$username = $_SESSION['username'];
	$role = $_SESSION['role'];
	$ip   = $_SESSION['ip'];

	if ( !isset($username) ){
		die( 'not login' );
	}

	if ($role != 'admin'){
		die( 'You are not admin <br> from ' . $ip );
	} 

	die( 'fake flag here, try another way :P' );

}

?>
