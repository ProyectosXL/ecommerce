<?php


function enviarMail($mailDestinatario, $nroPedido){

    
    require("class.phpmailer.php");
    require("class.smtp.php");
        
    $nombre = 'TIENDA ONLINE - XL EXTRALARGE';
    $email = 'info@xl.com.ar';
    $telefono = '';
    $asunto = 'Nuevo pedido de la tienda ONLINE';
    $mensaje = 'Recuerden que deben facturar el pedido, el cliente llega dentro de las 24hs de recibido este mail o el de la tienda';
    $destinatario = $mailDestinatario;
    
    
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
    $mail->AddAddress($destinatario); // Esta es la direcci�n a donde enviamos los datos del formulario
    
    $mail->Subject = "Nuevo pedido desde la tienda"; // Este es el titulo del email.
    $mensajeHtml = nl2br($mensaje);
    $mail->Body = "
    <html> 
    
    <body> 
    
    <h1>Recibiste un nuevo pedido desde la tienda</h1>
    
    <h4>Recorda que tenes que facturarlo</h4>
    
    <p>nombre: {$nombre}</p>
    
    <p>email: {$email}</p>
    
    <p>pedido Nro: {$nroPedido}</p>
    
    <p>asunto: {$asunto}</p>
    
    <p>mensaje: {$mensaje}</p>
    
    </body> 
    
    </html>
    
    <br />"; // Texto del email en formato HTML
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
        
        
        
        return 1;
        
}


?>

