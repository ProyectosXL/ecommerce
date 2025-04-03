<?php


function enviarMail($string){

    
    require("class.phpmailer.php");
    require("class.smtp.php");
        
    $nombre = 'ARTICULOS PENDIENTES - ECOMMERCE';
    $email = 'info@xl.com.ar';
    $telefono = '';
    $asunto = 'ECOMMERCE - Listado de articulos pendientes';
    $mensaje = 'Total de articulos pendientes de los ultimos 60 dias';
    // $destinatario = 'gaston.marcilio@xl.com.ar';
    $destinatario = array(
        "agustina.taboada@xl.com.ar",
        "info@xl.com.ar",
        "lucas.navarro@xl.com.ar",
        "romina.ramirez@xl.com.ar",
        "claudia.otero@xl.com.ar",
        //"ramiro.orozco@xl.com.ar",
        // Dani, Romi, Clau y a Juli.
    );
    

    foreach($destinatario as $destino){
    
        


    // Datos de la cuenta de correo utilizada para enviar v�a SMTP
    $smtpHost = "smtp.xl.com.ar";  // Dominio alternativo brindado en el email de alta 
    $smtpUsuario = "sistemas@xl.com.ar";  // Mi cuenta de correo
    $smtpClave = "Kill2018";  // Mi contrase�a
    
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = false;
    $mail->Port = 26; 
    $mail->IsHTML(true); 
    $mail->CharSet = "utf-8";
    
    // VALORES A MODIFICAR //
    $mail->Host = $smtpHost; 
    $mail->Username = $smtpUsuario; 
    $mail->Password = $smtpClave;
    
    


    
    $mail->From = $email; // Email desde donde env�o el correo.
    $mail->FromName = $nombre;
    $mail->AddAddress($destino); // Esta es la direcci�n a donde enviamos los datos del formulario
    
    $mail->Subject = "Listado diario de articulos pendientes de preparacion"; // Este es el titulo del email.
    $mensajeHtml = nl2br($mensaje);
    $mail->Body = $string; // Texto del email en formato HTML
    $mail->AltBody = "{$mensaje} \n\n "; // Texto sin formato HTML
    // FIN - VALORES A MODIFICAR //
    
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
            )
        );
        
        $estadoEnvio = $mail->Send(); 
        if($estadoEnvio){
            echo "El correo fue enviado correctamente.";
        } else {
            echo "Ocurrio un error inesperado.";
        }
        

    }
        
}


?>

