<?php

    $servername = 'db';
    $username = 'root';
    $password = 'password';
    // $dbname = 'recherche_information_document';
    $dbname = 'recherche_information_document2';
    $port = '3306';

    try {
        $cnx = new PDO(
            "mysql:host=$servername;port=$port;dbname=$dbname",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
       echo 'Connection failed: ' . $e->getMessage();
    }
