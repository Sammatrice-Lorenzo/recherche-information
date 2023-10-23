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
    <title>Document</title>
</head>

<div class="text-center">
    <?php
        include 'utils.php';
        $path = upload($_FILES['uploads']);
        explorerDir($path, $cnx);

        updatePathForFileWord($cnx);
        function updatePathForFileWord(PDO $cnx): void
        {
            $filesWord = $cnx->prepare("SELECT * FROM document WHERE titre LIKE '%.docx'");
            $filesWord->execute();

            foreach ($filesWord->fetchAll() as $file) {
                $update = $cnx->prepare('UPDATE document SET titre =?, path=? WHERE id = ?');

                $update->bindValue(1, str_replace('docx', 'html', $file['titre']));
                $update->bindValue(2, str_replace('docx', 'html', $file['path']));
                $update->bindValue(3, $file['id']);
                $update->execute();
            }
        }
    ?>
</div>
