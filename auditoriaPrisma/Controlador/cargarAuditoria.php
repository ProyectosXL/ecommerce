<?php

include 'controlador.php';

$mail = $_POST['mail'];
$matriz = $_POST['matriz'];

foreach ($matriz as $key => $value) {
    if($value == !null){

        echo $mail.' '.$value[1].' '.$value[2].' '.$value[10].'<br>';
        actualizarAuditoria($mail, $value[2], $value[3], $value[9], $value[10]);
    }
}

