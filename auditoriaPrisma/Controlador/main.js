$(document).ready(function() { 
$('#tableData').DataTable( { 
select: true,
dom: 'lBfrtip', buttons: [   'copy', 'excel', 'pdf', 'print' ],
"pageLength": 50,
fixedHeader: true
} ); 
} );





function enviar(mail){

    var table = document.getElementById("table");
    var matriz = [];

    for(var i=0;  i<table.rows.length ; i++){
        // if(table.rows[i].cells[0].innerHTML != 'RAZON SOCIAL'){
            matriz[i] = [];

            for(var x=0; x<table.rows[0].cells.length ; x++ ){
                   
                if(x==9 || x==10){
                    if(table.rows[i].cells[x].querySelector('#b:checked') !=null ){
                        var dato =  'si';
                    }else{
                        var dato =  'no';
                    }
                }else{
                    var dato =  table.rows[i].cells[x].innerHTML;   
                }
            
                matriz[i][x] = dato;
            }    
        // }    
    }

    console.log(matriz);

 
    postear(mail, matriz);
        

}






function postear(mail, matriz) {

    console.log(mail, matriz)

    $.ajax({
        url: 'Controlador/cargarAuditoria.php',
        method: 'POST',
        data: {
            mail: mail,
            matriz: matriz
        },

        success: function(data) {
            swal({
                title: "Actualizacion hecha!",
                text: "Auditoria cargada",
                icon: "success",
                button: "Aceptar",
                })
                .then(function() {
                window.location = "index.php";
            })
            ;
            
        } 
    
    });
    
}