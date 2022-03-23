<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();
 
// Désinitialisation des variables session
$_SESSION = array();
 
// Détruire la session
session_destroy();
 
// Redirection vers page login
header("location: login.php");

exit;
?>