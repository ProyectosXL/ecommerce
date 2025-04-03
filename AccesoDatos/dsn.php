<?php


$dsn = '1 - CENTRAL';
$user = 'sa';
$pass = 'Axoft1988';


$cid=odbc_connect($dsn, $user, $pass);

if (!$cid){
	exit("<strong>Ha ocurrido un error tratando de conectarse con el origen de datos.</strong>");
}

?>