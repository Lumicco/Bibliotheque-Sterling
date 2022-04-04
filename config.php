<?php

/*Déclaration des variables*/
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'AT2jgTMEx-cHeIT9');
define('DB_NAME', 'bibliotheque');
 
/*Connexion BDD*/
try
{
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    /*Définition du error mode (exception)*/
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e)
{
    die("Erreur de connexion. " . $e->getMessage());
}
?>