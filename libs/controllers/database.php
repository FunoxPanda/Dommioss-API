<?php

$dbname = 'DommiossGroup';
$dbpass = '';
$dbhost = 'localhost';
$dbuser = '';


try {
    $bdd = new PDO("mysql:host=" + $dbhost + ";dbname=" + $dbname + ";charset=utf8", $dbuser, $dbpass);
} catch (PDOEXCEPTION $error) {
    print($error);
}
