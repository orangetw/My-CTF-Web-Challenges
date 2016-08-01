<?php

	$dbhost = '127.0.0.1';
	$dbuser = 'root';
	$dbpass = 'root';
	$dbname = 'giraffe';

	$FLAG = 'hitcon{howsgiraffesfeeling?no!youonlythinkofyourself}';



/*

CREATE TABLE users (id int AUTO_INCREMENT, 
username text, 
password text, 
mail text, 
PRIMARY KEY(id)); 

CREATE TABLE fail2ban (id int AUTO_INCREMENT, 
ip text, 
PRIMARY KEY(id)
);

CREATE TABLE tokens (id int AUTO_INCREMENT, 
username text,
ip text,
token text, 
ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
PRIMARY KEY(id));

INSERT INTO users(username, password, mail) VALUES ( 'admin', '3e333ffaac0ff1ae70083a1533787db2', '127.0.0.1' );


*/