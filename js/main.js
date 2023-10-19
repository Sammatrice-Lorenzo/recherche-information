function showContentModal(idDocument) {
    resetModal()

    $.ajax({
        type: 'GET',
        url: 'cloudWord.php?idDocument=' + idDocument,
        success: function (data) {
            $('.modal-body').append(data)
        },
        error: function () {
            alert('Un problème est survenu lors de la requête')
        }
    })
}

function resetModal() {
    $('.modal-body').empty()
}

$(document).ready(function () {
    $('.btn-cloud').on('click', function (e) {
        e.preventDefault()
        let idDocument = $(this).parent().attr('data-document')
        $('#modalNuage').modal('show')
        showContentModal(idDocument)
    });
});
