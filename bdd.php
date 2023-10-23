<?php

include 'cnx.php';

function insertionDocument(PDO $cnx, string $title, string $pathFile): void
{
    $insertWord = $cnx->prepare('INSERT INTO document (titre, path) VALUES (?, ?)');
    $insertWord->bindValue(1, $title);
    $insertWord->bindValue(2, $pathFile);
    $insertWord->execute();
}

function getDocumentById(PDO $cnx, int $id): array
{
    $document = $cnx->prepare('SELECT * FROM document WHERE id = ?');
    $document->bindValue(1, $id);
    $document->execute();

    return $document ? $document->fetch() : [];
}

function getDocumentId(PDO $cnx, string $title): int
{
    $document = $cnx->prepare('SELECT id FROM document WHERE titre = ?');
    $document->bindValue(1, $title);
    $document->execute();

    return $document->fetch(PDO::FETCH_ASSOC)['id'];
}

function getWordId(PDO $cnx, string $word): int
{
    $wordInBdd = $cnx->prepare('SELECT id, mot FROM mot WHERE mot = ?');
    $wordInBdd->bindValue(1, $word);
    $wordInBdd->execute();
    
    $wordInBdd = $wordInBdd->fetch(PDO::FETCH_ASSOC);

    if (!$wordInBdd) {
        $insertWord = $cnx->prepare('INSERT INTO mot (mot) VALUES (?)');
        $insertWord->bindValue(1, $word);
        $insertWord->execute();

        $wordInBdd = $cnx->prepare('SELECT id, mot FROM mot WHERE mot = ?');
        $wordInBdd->bindValue(1, $word);
        $wordInBdd->execute();
        $wordInBdd = $wordInBdd->fetch(PDO::FETCH_ASSOC);
    }

    return $wordInBdd['id'];
}

/**
 * On insère le document et les mots et la liaison entre les 2
 *
 * @param string $path
 * @param string $title
 * @param PDO $cnx
 * @return void
 */
function insertionWordsAndDocument(string $path, string $title, PDO $cnx): void
{
    $words = tokenisation($path);

    getTableUploadedFiles($words, $path, $title);

    insertionDocument($cnx, $title, $path);
    $idDoc = getDocumentId($cnx, $title);

    foreach ($words as $word => $frequence) {
        $idWord = getWordId($cnx, $word);

        $insertWord = $cnx->prepare('INSERT INTO document_mot (frequence, idMot, idDocument) VALUES (?, ?, ?)');
        $insertWord->bindValue(1, $frequence);
        $insertWord->bindValue(2, $idWord);
        $insertWord->bindValue(3, $idDoc);
        $insertWord->execute();
    }
}

/**
 * Permet de trouver le mot recherche s'il existe
 *
 * @param PDO $cnx
 * @param string $word
 * @return array
 */
function findWord(PDO $cnx, string $word): array
{
    if (!$word || strlen($word) < 2) {
        return [];
    }

    $wordInBdd = $cnx->prepare("SELECT * FROM `mot` WHERE mot LIKE '%' ? '%'");
    $wordInBdd->bindValue(1, $word);
    $wordInBdd->execute();

    $result = $wordInBdd->fetchAll();
    $documentsFind = [];

    foreach ($result as $wordBdd) {
        $wordAndDocuments = $cnx->prepare(
            'SELECT frequence, idDocument FROM document_mot WHERE idMot = ? ORDER BY frequence DESC'
        );
        $wordAndDocuments->bindValue(1, $wordBdd['id'], PDO::PARAM_INT);
        $wordAndDocuments->execute();

        foreach ($wordAndDocuments->fetchAll() as $wordAndDocument) {
            $doc = getDocumentById($cnx, $wordAndDocument['idDocument']);
            $documentsFind[] = [
                $doc['titre'],
                $wordAndDocument['frequence'],
                $wordBdd['mot'],
                $doc['path'],
                $wordAndDocument['idDocument']
            ];
        }
    }

    $keyValues = array_column($documentsFind, 1);
    array_multisort($keyValues, SORT_DESC, $documentsFind);

    return $documentsFind;
}

/**
 * Cette fonction permet de récupérer les mots d'un document
 *
 * @param PDO $cnx
 * @param integer $idDocument
 * @return array
 */
function getWordsByDocument(PDO $cnx, int $idDocument): array
{
    $wordByDocument = $cnx->prepare(
        "SELECT idDocument, idMot, m.mot, frequence FROM `document_mot` INNER JOIN mot m on m.id = idMot
        WHERE idDocument = ? and frequence >= 2 ORDER BY frequence DESC"
    );
    $wordByDocument->bindValue(1, $idDocument);
    $wordByDocument->execute();

    return $wordByDocument->fetchAll();
}
