<?php

$host       = 'localhost';
$user       = 'root';
$pass       = '';
$db         = 'beejee';
$charset    = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_WARNING,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);
$pdo = new PDO($dsn, $user, $pass, $opt);
