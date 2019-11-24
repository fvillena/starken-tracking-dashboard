$(document).ready(function() {
    $('#results').DataTable( {
        "ajax": "data.php",
        "columns": [
            { "data": "emision" },
            { "data": "orden" },
            { "data": "destinatario" },
            { "data": "estado" },
            { "data": "id" }
        ],
        "paging":false,
        "aaSorting": [0, 'desc'],
    } );
} );