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
<body>
    <div class="container mt-5">
        <div class="text-end">
            <a type='btn' href="./form.html" class="btn btn-primary">Upload</a>
        </div>
        <h1 class="text-center">MyEngine</h1>
        <br>
        <div>
            <form action="showDocumentFind.php" method="get">
                <div class="d-flex justify-content-center">
                    <div class="mb-3" style="max-width: 400px;">
                        <div class="input-group">
                            <input required="required" type="search" class="form-control rounded-start" placeholder="Rechercher" aria-label="Rechercher" name="word">
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
    </div>
</div>
</body>
</html>