<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Inclure le fichier config
require_once "../config.php";
 
// Definition et initialisation des variables
$id = $_POST['id'];
$username = $_POST['username'];
$password = $_POST['password'];
$role = $_POST['role'];

$error = $password_err = "";

if(!empty($username))
{
    // Préparation d'une requête update
    $sql = 'UPDATE utilisateurs SET NomUser = :username WHERE id = :id';
    $stmt = $pdo->prepare($sql);

    // Liaison des variables à la requête en tant que paramètres
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execution de la requête préparée
    if(!$stmt->execute())
    {
        $error = "Un problème est survenu.";
    }

    // Fermer la requête
    unset($stmt);
}

if(!empty($password))
{
    if(strlen(trim($password)) < 8)
    {
        $password_err = "Le mot de passe doit contenir un minimum de 8 caractères.";
    }
    else
    {
        // Préparation d'une requête update
        $sql = 'UPDATE utilisateurs SET MotDePasse = :password WHERE id = :id';
        $stmt = $pdo->prepare($sql);

        // Liaison des variables à la requête en tant que paramètres
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $password = password_hash($password, PASSWORD_DEFAULT); // Création d'un hachage du mot de passe

        // Execution de la requête préparée
        if(!$stmt->execute()) 
        {
            $error = "Un problème est survenu.";
        }

        // Fermer la requête
        unset($stmt);
    }
}

if(!empty($role))
{
    // Préparation d'une requête update
    $sql = 'UPDATE utilisateurs SET Role = :role WHERE id = :id';
    $stmt = $pdo->prepare($sql);

    // Liaison des variables à la requête en tant que paramètres
    $stmt->bindParam(':role', $role, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    // Execution de la requête préparée
    if(!$stmt->execute()) 
    {
        $error = "Un problème est survenu.";
    }

    // Fermer la requête
    unset($stmt);
}

if(empty($error) && empty($password_err))
{
    // Redirection vers page gestion des utilisateurs
    header('Location: GestionUser.php');
    exit();
}
else
{
    echo $error;
    echo $password_err;
}

// Femer la connexion
unset($pdo);
?>