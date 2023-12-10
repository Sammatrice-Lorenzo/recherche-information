<?php

include 'bdd.php';

/**
 * Permet d'upload un fichier
 *
 * @return string
 */
function upload(array $files): string|null
{
    if (!$files) {
        return "";
    }
    $nameDirectory = "";

    for ($i = 0; $i < count($files['name']); $i++) {
        $fileName = $files['name'][$i];
        $fileTempName = $files['tmp_name'][$i];

        $extension = pathinfo($fileName, PATHINFO_EXTENSION); // Extension du ficher

        if (!isACorrectFile($extension)) {
            continue;
        }
    
        $title = uniqid() . "." . $extension;
        // Ici on lui donne un id unique et le path vers quelle il doit être stocké
        $nameDirectory = "uploads/" . strtok($files['full_path'][$i], '/');

        // Si le dossier n'existe pas on le crée.
        if (!is_dir($nameDirectory)) {
            mkdir($nameDirectory, 0700);
        }
        $nameFile = $nameDirectory . "/". $title;

        // Vérifier si le fichier est valide
        if (!is_uploaded_file($fileTempName) || !move_uploaded_file($fileTempName, $nameFile)) {
            return null;
        }
    }

    return $nameDirectory;
}

/**
 * Permet de lire les documents en recursive
 *
 * @param string $path
 * @return void
 */
function explorerDir(string $path, $cnx): void
{
	$folder = opendir($path);

    getHeaderTableFilesUploaded();

	while ($entree = readdir($folder)) {
		// On ignore les entrées
		if ($entree != "." && $entree != "..") {
			// On vérifie si il s'agit d'un répertoire
			if (is_dir($path."/".$entree)) {
				$savPath = $path;

				// Construction du path jusqu'au nouveau répertoire
				$path .= "/".$entree;
								
				// On parcours le nouveau répertoire
				// En appellant la fonction avec le nouveau répertoire
				explorerDir($path, $cnx);
				$path = $savPath;
			} else {
                $sourcePath = $path."/".$entree;
                $pathInfo = pathinfo($sourcePath);

                if (isACorrectFile($pathInfo['extension'])) {
                    insertionWordsAndDocument($sourcePath, $pathInfo['basename'], $cnx);
                }
			}
		}
	}
	closedir($folder);
}

/**
 * Récupérer les stops words
 *
 * @return array
 */
function getStopWords(): array
{
    return explode("\n", file_get_contents('stopwords.txt'));
}

/**
 * Permet de savoir si c'est un fichier '.txt .html .xml'
 *
 * @param string $path
 * @return boolean
 */
function isACorrectFile(string $extension): bool
{
    $validExtensions = ['txt', 'html', 'xml', 'pdf', 'docx'];

    return in_array($extension, $validExtensions);
}

function getContentFile(string $path): string
{
    $pathInfo = pathinfo($path);

    return match ($pathInfo['extension']) {
        'html' => getWordsFileHtml($path),
        'txt' => strtolower(file_get_contents($path)),
        'xml' => getWordsFileXML($path),
        'pdf' => getWordsFilePDForDoc($path, 'pdftohtml -i -noframes', 'pdf'),
        'docx' => getWordsFilePDForDoc($path, 'pandoc -s', 'docx'),
        default => '',
    };
}

/**
 * Va permettre la tokenisation en gardent que les mots clé et supprimer les stopWords
 *
 * @param string $path
 * @return array<int, string[]|string>
 */
function tokenisation(string $path): array
{
    $contentFile = getContentFile($path);
    if (!$contentFile) {
        return [];
    }

    $stopWords = getStopWords();

    // Dans le regex on lui set les espaces que ca soit un mots alphabétiques avec accent
    $tokenisation = preg_split('/[^A-Za-zÀ-ÖØ-öø-ÿ]+/u', $contentFile, -1, PREG_SPLIT_NO_EMPTY);
    $tokenisation = array_filter($tokenisation, static fn (string $word) => strlen($word) > 2);

    $words = array_count_values(array_diff($tokenisation, $stopWords));

    $wordsLemmatized = transformWordsWithLemmatization($words);

    return [
        $wordsLemmatized,
        $contentFile
    ];
}

function getWordsFileXML(string $path): string
{
    $content = file_get_contents($path);
    $xml = simplexml_load_string($content);
    $isXML = $xml->asXML();

    $xmlWithoutTags = strip_tags($isXML);
    $textFiltered = array_filter(
        explode(" ", $xmlWithoutTags),
        static fn (string $text) => $text !== "" && trim($text) !== ""
    );

    return implode(" ", $textFiltered);
}

function getWordsFileHtml(string $path): string
{
    $contentFile = strtolower(file_get_contents($path));
    $text = strip_tags($contentFile, "<style>");
    $substring = substr($text, strpos($text, "<style"), strpos($text,"</style>") + 2);

    $text = str_replace($substring, "", $text);
    $textFiltered = array_filter(
        explode(" ", $text),
        static fn (string $text) => $text !== "" && $text !== "\n" && !str_starts_with($text, '6546')
    );

    return implode(" ", $textFiltered);
}

function pagination(int $totElement, string $word, int $page = 1): void
{
    // On define le nombre d'élément par page
    $nbElementInTable = 5;
    //On récurer le nb total element vua la function situé dans Utils.php

    // On calcule le nombre de pages total le résultat va être mis au supérieure.
    $pages = ceil($totElement / $nbElementInTable);

    echo "<li class='page-item'>" .
        "<a href='?page=" . $page - 1  ."&word=$word' class='page-link' aria-label='Previous'>
            <span aria-hidden='true'>&laquo;</span>
        </a>
    </li>";

    // On boucle pour avoir les nombre de pages
    for ($i = 1; $i <= $pages; $i++) {
        echo
            "<li class='page-item'>
                <a href='?page=$i&word=$word' class='page-link'>$i</a>
            </li>"
        ;
    }

    echo "<li class='page-item'>
        <a href='?page=" . $page + 1 . "&word=$word' class='page-link' aria-label='Next'></span>
            <span aria-hidden='true'>&raquo;</span>
        </a>
    </li>";
}

function getTableUploadedFiles(array $wordsFiltered, string $path, string $title)
{
    $countWordFiltered = count($wordsFiltered);
    $countWords = count(explode(" ", getContentFile($path)));
    $stopWords = $countWords - $countWordFiltered;

    echo "<tr>";
        echo "<td>$title</td>";
        echo "<td>$countWords</td>";
        echo "<td>$countWordFiltered</td>";
        echo "<td>$stopWords</td>";
    echo "</tr>";
}

function getHeaderTableFilesUploaded(): void
{
    echo "<table id='doc-upload' class='table table-bordered table-hover text-center mx-auto' style='max-width: 800px;'>
            <thead>
                <tr>
                    <th>Nom du fichier</th>
                    <th>Mots total</th>
                    <th>Mot filtrés</th>
                    <th>Mots vide</th>
                </tr>
            </thead>
    ";
}

function encodingData(array $data): array
{
    $targetEncoding = 'UTF-8';

    return array_map(function(string $text) use ($targetEncoding) {
        $detectedEncoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');

        return mb_convert_encoding($text, $targetEncoding, $detectedEncoding);
    }, $data);
}


function getLemmatization(): array
{
    $csvFile = './Lexique.csv';

    $lemmatisation = [];
    if (($handle = fopen($csvFile, 'r')) !== false) {
        while (($data = fgetcsv($handle, 0, ';', '"', "\n")) !== false) {
            $data = encodingData($data);
            $lemmatisation[$data[0]] = $data[1];
        }
        fclose($handle);
    }

    return $lemmatisation;
}

/**
 * @param string[] $words
 * @return string[]
 */
function transformWordsWithLemmatization(array $words): array
{
    $wordsLemmatized = [];
    $lemmatisation = getLemmatization();

    foreach ($words as $word => $frequence) {
        $key = array_key_exists($word, $lemmatisation) ? $lemmatisation[$word] : $word;
        $wordsLemmatized[$key] = $frequence;
    }

    return $wordsLemmatized;
}

function fixEncoding(string $text): string
{
    $detectedEncoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
    return mb_convert_encoding($text, 'UTF-8', $detectedEncoding);
}

function getWordsFilePDForDoc(string $path, string $command, string $extension): string
{
    error_reporting(0); // n'affiche pas les warning
    $parameterCommand = '';
    $infos = pathinfo($path);
    $htmlOutputPath =  $infos['dirname'] . "/" . $infos['filename'] . ".html";

    if ($extension === 'docx') {
        $parameterCommand = '-o ';
    }

    shell_exec($command . ' ' .  $path . ' ' . $parameterCommand . $htmlOutputPath);

    $htmlToText = getWordsFileHtml($htmlOutputPath);
    $words = preg_split('/[^A-Za-zÀ-ÖØ-öø-ÿ]+/u', $htmlToText, -1, PREG_SPLIT_NO_EMPTY);

    $cleanedWords = encodingData($words);
    $cleanedWords = array_map(static fn (string $word) => str_replace('&#160;', '', $word), $cleanedWords);

    if ($extension !== 'docx') {
        unlink($htmlOutputPath);
    }

    if ($extension === 'docx') {
        unlink($infos['dirname'] . "/" . $infos['basename']);
    }

    return implode(' ', $cleanedWords);
}

function getPreviewText(string $contentFile): string
{
    return str_split($contentFile, 200)[0] . '...';
}
