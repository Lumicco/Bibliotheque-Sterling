<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Inclure le fichier config
require_once "../config.php";
 
// Definition et initialisation des variables
$id = $_POST['id'];

// Préparation d'une requête delete
$sql = 'DELETE FROM livres WHERE id = :id';
$stmt = $pdo->prepare($sql);

// Liaison des variables à la requête en tant que paramètres
$stmt->bindParam(':id', $id, PDO::PARAM_INT);

// Execution de la requête préparée
if ($stmt->execute()) 
{
    // Redirection vers page gestion des utilisateurs
    header('Location: ../index.php');
    exit();

    // Fermer la requête
    unset($stmt);
}

// Femer la connexion
unset($pdo);
?>