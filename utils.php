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

        // Si le dossier n'existe pas on le cree.
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
 * Récupere les stops words
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
    $validExtensions = ['txt', 'html', 'xml'];

    return in_array($extension, $validExtensions);
}

function getContentFile(string $path): string
{
    $pathInfo = pathinfo($path);

    return match ($pathInfo['extension']) {
        'html' => getWordsFileHtml($path),
        'txt' =>  strtolower(file_get_contents($path)),
        'xml' => getWordsFileXML($path),
        default => '',
    };
}

/**
 * Va permettre la tokenisation en gardent que les mots clé et supprimer les stopowrds
 *
 * @param string $path
 * @return array
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

    return array_count_values(array_diff($tokenisation, $stopWords));
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
    $textWithoutTags = strip_tags($contentFile);
    $textFiltered = array_filter(
        explode(" ", $textWithoutTags),
        static fn (string $text) => $text !== "" && $text !== "\n"
    );

    return implode(" ", $textFiltered);
}

function pagination(int $totElement, string $word, int $page = 1): void
{
    // On define le nombre d'élement par page
    $nbElementInTable = 5;
    //On récuper le nb total élement vua la function sutué dans Utils.php

    // On calcule le nombre de pages total le resultat va étre mis au supériere.
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
    echo "<table class='table table-bordered table-hover text-center mx-auto' style='max-width: 800px;'>
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