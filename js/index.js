$(document).ready(function() {
    $('#results').DataTable( {
        "ajax": "data.php",
        "columns": [
            { "data": "emision" },
            { "data": "orden",
            fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html("<a target=_blank href='https://www.starken.cl/seguimiento?codigo="+oData.orden+"'>"+oData.orden+"</a>");
            }
        },
            { "data": "destinatario" },
            { "data": "estado" },
            { "data": "id",
            fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html("<a target=_blank href='https://dentonet.cl/admin-1470710090/index.php?controller=AdminOrders&vieworder&id_order="+oData.id+"'>"+oData.id+"</a>");
            } }
        ],
        "paging":false,
        "aaSorting": [0, 'desc'],
    } );
} );