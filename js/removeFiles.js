function removeFiles() {

    confirm('Cela va effacer tout les informations qui se trouvent dans la base de données')

    $.ajax({
        type: 'GET',
        url: 'truncateBdd.php',
        success: function (data) {
            if (data === 'success') {
                alert('Le fichiers ont été correctement supprimé')
            } else {
                alert('Un problème est survenu lors de la requête')
            }
        },
        error: function (error) {
            console.log(error)
            alert('Un problème est survenu lors de la requête')
        }
    })
}

$(document).ready(function () {
    $('.btn-remove').on('click', function () {
        removeFiles()
    })
})