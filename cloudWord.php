<?php

include 'bdd.php';

function cloudWords(PDO $cnx, int $idDocument): void
{
    $words = getWordsByDocument($cnx, $idDocument);

    echo "<ul class='cloud' role='navigation'>";
    foreach ($words as $word) {
        echo "<li>
            <a data-weight='" . $word['frequence'] . "'href='showDocumentFind.php?word=" . $word['mot'] ."'>"
                . $word['mot'] .
            "</a>
        </li>";
    }
    echo "</ul>";
}

cloudWords($cnx, $_GET['idDocument']);
