$('#selectBanco').select2({});
 
$('#selectBanco').on('select2:opening select2:closing', function( event ) {
var $searchfield = $(this).parent().find('.select2-search__field');
// $searchfield.prop('disabled', true);
});



$('#selectRubro').select2();

$('#selectRubro').on('select2:opening select2:closing', function( event ) {
var $searchfield = $(this).parent().find('.select2-search__field');
// $searchfield.prop('disabled', true);
});

const mostrarSpiner = () =>{

    let spinner = document.querySelector("#boxLoading");

    spinner.classList.add("loading");


}

$('#selectCategoria').select2();

$('#selectCategoria').on('select2:opening select2:closing', function( event ) {
var $searchfield = $(this).parent().find('.select2-search__field');
// $searchfield.prop('disabled', true);
});


$('#selectRangoEtario').select2();

$('#selectRangoEtario').on('select2:opening select2:closing', function( event ) {
var $searchfield = $(this).parent().find('.select2-search__field');
// $searchfield.prop('disabled', true);
});


$('#selectProvincia').select2();
$('#selectLocalidad').select2();
$('#selectTienda').select2();

const valoresSeleccionados = (id) =>
    Array.from(document.querySelector(id).selectedOptions).map(o => o.value);

/** Reconstruye un select preservando las selecciones que sigan existiendo. */
const repoblarSelect = (id, opciones) => {

    const previos = new Set(valoresSeleccionados(id));

    const html = opciones
        .map(o => `<option value="${o.value}"${previos.has(o.value) ? ' selected' : ''}>${o.text}</option>`)
        .join('');

    $(id).html(html).trigger('change.select2');
}

const filtrarTiendas = () => {

    $.ajax({

        url: 'Controller/ClienteController.php?accion=traerTiendas',
        type: 'POST',
        dataType: 'json',
        data: {
            provincias:  valoresSeleccionados('#selectProvincia'),
            localidades: valoresSeleccionados('#selectLocalidad')
        },
        success: function (data) {
            repoblarSelect('#selectTienda',
                data.map(s => ({ value: s.NRO_SUCURSAL, text: s.DESC_SUCURSAL })));
        }

    })

}

const filtrarPorLocalidad = () => filtrarTiendas();

const filtrarPorProvincia = () => {

    $.ajax({

        url: 'Controller/ClienteController.php?accion=traerLocalidades',
        type: 'POST',
        dataType: 'json',
        data: { provincias: valoresSeleccionados('#selectProvincia') },
        success: function (data) {
            repoblarSelect('#selectLocalidad',
                data.map(l => ({ value: l.LOCALIDAD, text: l.LOCALIDAD })));
            filtrarTiendas();   // encadena: provincia -> localidad -> tienda
        }

    })

}

const filtrarCategoria = () =>{

    let allOptions = Array.from(document.querySelector("#selectRubro").selectedOptions);

    let arraySelected = [];
    allOptions.forEach((element,x )=> {
        arraySelected[x] = element.value;
    });

    let rubros = "";

    if(arraySelected.length != 0){

        rubros = JSON.stringify(arraySelected).replace("[", "(").replace("]", ")").replace(/"/g, "'");
    
    }

        $.ajax({

            url: 'Controller/ClienteController.php?accion=traerCategorias',
            type: 'POST',
            dataType: 'json',
            data: {rubros: rubros},
            success: function (data) {
                let html = '';
                data.forEach(element => {
                    html += `<option value="${element.RUBRO}-${element.CATEGORIA}">${element.CATEGORIA}</option>`;
                });
                $('#selectCategoria').html("");
                $('#selectCategoria').html(html);
                $('#selectCategoria').select2();
            }

        })

}

const DT_CONFIG_CLIENTES = {
    "bLengthChange": false,
    "bInfo": false,
    "aaSorting": false,
    'columnDefs': [
        {
            "targets": "_all",
            "className": "text-center",
            "sortable": false,
        },
    ],
    "oLanguage": {

        "sSearch": "Busqueda rapida:",
        "sSearchPlaceholder" : "Sobre cualquier campo"

    },
};

/** Inicializa la tabla y todo lo que cuelga de ella. La usan la vista y exportTable(). */
const initTablaClientes = () => {

    $('#tablaClientes').DataTable(DT_CONFIG_CLIENTES);

    $("#tablaClientes_filter").append('<button class="btn btn-success btn_exportar" style="margin-bottom:4px;margin-left:10px;height:40px;margin-right:5px" onclick ="exportTable()"> Exportar<i class="bi bi-file-earmark-excel"></i></button>');
    $('.dataTables_filter input[type="search"]').css(
        {'height':'40px'}
    );

    let newdiv2 = document.createElement( "strong" );
    let newdiv1 =  document.querySelector("#conteo").textContent + " Registros Encontrado" ;
    newdiv2.append(newdiv1)

    $("#tablaClientes_filter").parent().parent().children()[0].appendChild(newdiv2);
    document.querySelector("#boxLoading").classList.remove("loading")
}

const exportTable = () =>{

    // destroy() devuelve TODAS las filas al DOM, asi el excel no queda limitado
    // a la pagina visible de DataTables.
    $('#tablaClientes').DataTable().destroy();

    $(`#tablaClientes`).table2excel({
    // exclude CSS class
    exclude: ".noExl",
    name: "Segmentacion de Clientes",
    filename: "SegmentacionClientes", //do not include extension
    fileext: ".xlsx" // file extension
    });

    initTablaClientes();
}