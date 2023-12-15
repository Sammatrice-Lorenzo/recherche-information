<?php

include 'cnx.php';

function removeFiles(string $path): void
{
    $files = scandir($path);

    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $currentPath = $path . '/' . $file;

            if (is_dir($currentPath)) {
                removeFiles($currentPath);
                rmdir($currentPath);
            } else {
                unlink($currentPath);
            }
        }
    }
}

function truncateTables(PDO $pdo): string
{
    try {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

        $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $pdo->exec("TRUNCATE TABLE $table");
        }

        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        $path = './uploads/';

        removeFiles($path);

       return 'success';
       
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
}

$response = truncateTables($cnx);

echo $response;
