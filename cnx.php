<?php

    $serverName = 'db';
    $userName = 'root';
    $password = 'password';
    // $dbname = 'recherche_information_document';
    $dbname = 'recherche_information_document2';
    $port = '3306';

    try {
        $cnx = new PDO(
            "mysql:host=$serverName;port=$port;dbname=$dbname",
            $userName,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
       echo 'Connection failed: ' . $e->getMessage();
    }
