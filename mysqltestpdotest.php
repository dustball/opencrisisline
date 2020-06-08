<?php

$host = 'brcaiddev.cspzofkgh5ed.us-west-1.rds.amazonaws.com';
$db   = 'brcaiddevtest';
$user = 'brcaiddevadmin';
$pass = 'brcaiddevadmin69!';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
    }


