<?php

$extension = pathinfo($_GET['path'])['extension'];

if ($extension === 'pdf') {
    echo "<iframe src=" . $_GET['path'] . " width=\"100%\" style=\"height:100%\"></iframe>";
} else {
    echo "<h1>Le texte</h1>";
    readfile($_GET['path']);
}
