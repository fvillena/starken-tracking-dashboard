function format ( d ) {
    // `d` is the original data object for the row
    return '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
        '<tr>'+
            '<td>Destino:</td>'+
            '<td>'+d.destino+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Compromiso:</td>'+
            '<td>'+d.compromiso+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Valor:</td>'+
            '<td>'+d.valor_of+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Tipo de Pago:</td>'+
            '<td>'+d.tipo_pago+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Tipo de Entrega:</td>'+
            '<td>'+d.tipo_entrega+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Dirección:</td>'+
            '<td>'+d.direccion+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Fecha de Entrega:</td>'+
            '<td>'+d.fecha_entrega+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>Recibido por::</td>'+
            '<td>'+d.recibe+'</td>'+
        '</tr>'+
        '<tr>'+
            '<td>RUT del Receptor:</td>'+
            '<td>'+d.rut_recibe+'</td>'+
        '</tr>'+
    '</table>';
}
$(document).ready(function() {
    var table = $('#results').DataTable( {
        "ajax": "data.php",
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": '+'
            },
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
            } },
            { "data": "reference" },
        ],
        "paging":false,
        "aaSorting": [1, 'desc'],
        "createdRow": function( row, data, dataIndex){
            if( data["estado"] ==  "ANULADO"){
                $(row).addClass('table-danger');
            }
            if( data["estado"] ==  "ENTREGADO"){
                $(row).addClass('table-success');
            }
            else {
                $(row).addClass('table-warning');
            }
        }
    } );
    $('#results tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
    setInterval( function () {
        table.ajax.reload();
    }, 600000 );
} );