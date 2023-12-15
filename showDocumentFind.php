<!DOCTYPE>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous"
    >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe"
        crossorigin="anonymous">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/main.js"></script>
    <link rel="stylesheet" href="./css/cloud.css">

    <title>Document</title>
</head>
<body>
    <div class="container mt-2">
        <h1 class="text-center">MyEngine</h1>
        <br><br>
        <form action="showDocumentFind.php" method="get">
            <div class="d-flex justify-content-center">
                <div class="mb-3" style="max-width: 400px;">
                    <div class="input-group">
                        <input
                            required="required" type="search"
                            class="form-control rounded-start" placeholder="Rechercher"
                            aria-label="Rechercher" name="word"
                            value=<?php echo $_GET['word'] ?? '' ?>
                        >
                        <button class="btn btn-primary rounded-end" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="bi bi-search" width="1em" height="1em" style="vertical-align: middle;">
                                <path fill-rule="evenodd" d="M6.5 11a4.5 4.5 0 117 0 4.5 4.5 0 01-7 0zM15 9.5A5.5 5.5 0 104.5 9a5.5 5.5 0 0010.089 2.966l3.56 3.56a.5.5 0 00.707-.708l-3.56-3.56A5.472 5.472 0 0015 9.5z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="container text-center">
        <?php
            include 'utils.php';
            include 'modal.html';

            $documentsFind = findWord($cnx, $_GET['word']);
            echo "<div>";
            echo "<span class='mx-5' style='display:flex; position:relative; left: 15%'>
                Nombre de documents trouvés pour: '<b>" . $_GET['word'] . "':  </b>    "
                . '  ' . str_pad(count($documentsFind), 15, '  ', STR_PAD_LEFT);
            echo "</span>";
            echo "</div>";
            echo "<br>";

            $similarWords = getSimilarWords($cnx, $_GET['word']);
            if (!$documentsFind && $similarWords) {

                echo "<div>";
                    echo "<span>Vous chercher peut être : </span>";
                    echo "<br>";
                    foreach ($similarWords as $similarWord) {
                        echo "<a href='showDocumentFind.php?word=" . $similarWord['mot'] ."'>"
                            . $similarWord['mot'] .
                        "</a>";
                        echo "<br>";
                    }
                echo "</div>";
                echo "<br>";
            }
        ?>
        <table class="table table-bordered table-hover text-center mx-auto" style="max-width: 800px;">
            <thead>
                <tr>
                    <th>Nom du fichier</th>
                    <th>Mot</th>
                    <th>Fréquence</th>
                    <th>Aperçu du texte</th>
                    <th>Nuage de mots</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $page = $_GET['page'] ?? 1;
                    $nbElementInPage = 5;
                    
                    // Assurez-vous que la page est un entier positif
                    $page = max(1, intval($page));
                    
                    // Calcul du nombre total de pages
                    $totalPages = ceil(count($documentsFind) / $nbElementInPage);

                    // On vérifie que la page sélectionnée ou sur l'url ne dépasse pas le nb de pages total
                    if ($page > $totalPages) {
                        $page = $totalPages;
                    }
                    
                    // Le début d'élément que l'on va afficher, cela sert pour la limite
                    $start = ($page - 1) * $nbElementInPage;
                    
                    // pour récupérer les éléments du tableau à partir de l'indice de la page
                    $documents = array_slice($documentsFind, $start, $nbElementInPage);

                    foreach ($documents as $key => $document) {
                        echo "<tr>";
                            echo "<td>
                                <a href='./showContentFile.php?path=$document[3]' style='color:blue;'>
                                    $document[0]
                                </a>
                            </td>";
                            echo "<td>$document[2]</td>";
                            echo "<td>$document[1]</td>";
                            echo "<td>";
                                echo $document[5];
                            "</td>";
                            echo "<td data-document=" . $document[4] . " style='width:150px;'>
                                <a id='btn-cloud' class='btn btn-primary btn-cloud' role='button'>
                                    Nuage de mots
                                </a>
                            </td>";
                        echo "</tr>";
                    }

                    if (!$documents) {
                        echo "<tr>";
                            echo "<td colspan='5'>Aucun document trouvé pour ce mot.</td>";
                        echo "</tr>";
                    }
                
                ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php
                    if (count($documentsFind) > 5) {
                        pagination(count($documentsFind), $_GET['word'], $page);
                    }
                ?>
            </ul>
        </nav>
    </div>
</body>
</html>
