<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Inclure le fichier config
require_once "../config.php";
 
// Definition et initialisation des variables
$file = $_POST['file'];
$file_path = str_replace("Bibliotheque-Sterling", "", $file); //Enlever partie du chemin pour redirection
$id_livre = $_POST['id'];
$id_user = $_SESSION['id'];
$date = date("Y-m-d H:i:s");

// Préparation d'une requête delete
$sql = 'INSERT INTO emprunter (NumUser, NumLivre, DateEmprunt) VALUES (:id_user, :id_livre, :date)';
$stmt = $pdo->prepare($sql);

// Liaison des variables à la requête en tant que paramètres
$stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
$stmt->bindParam(':id_livre', $id_livre, PDO::PARAM_INT);
$stmt->bindParam(':date', $date, PDO::PARAM_STR);

// Execution de la requête préparée
if ($stmt->execute()) 
{
    // Redirection vers le fichier du livre
    header('Location: ' . $file_path . '');
    exit();

    // Fermer la requête
    unset($stmt);
}

// Femer la connexion
unset($pdo);
?>