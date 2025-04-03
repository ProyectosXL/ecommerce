<?php

include 'metodos.php';

$locales = $_POST['locales'];

comprobarLocales($locales);

header('Location: index.php');