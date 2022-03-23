<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Inclure le fichier config
require_once "../config.php";
 
// Definition et initialisation des variables
$id = $_POST['id'];

// Préparation d'une requête select
$sql = 'SELECT id FROM utilisateurs WHERE statut = "actif" AND id = :id';

if($stmt = $pdo->prepare($sql))
{
    // Liaison des variables à la requête en tant que paramètres
    $stmt->bindParam(":id", $id, PDO::PARAM_STR);
    
    // Execution de la requête préparée
    if($stmt->execute())
    {
        // Mettre à jour le statut en fonction du statut actuel
        if($stmt->rowCount() == 1) // Retourne le nombre de ligne affecté par la dernière requête
        {
            // Préparation d'une requête update
            $sql = 'UPDATE utilisateurs SET statut = "suspendu" WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execution de la requête préparée
            if ($stmt->execute()) 
            {
                // Redirection vers page gestion des utilisateurs
                header('Location: GestionUser.php');
                exit();
            }

            // Fermer la requête
            unset($stmt);
        } 
        else
        {
            // Préparation d'une requête update
            $sql = 'UPDATE utilisateurs SET statut = "actif" WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execution de la requête préparée
            if ($stmt->execute()) 
            {
                // Redirection vers page gestion des utilisateurs
                header('Location: GestionUser.php');
                exit();
            }

            // Fermer la requête
            unset($stmt);
        }
    } 
    // Afficher une erreur si la requête n'a pas pu être éxecuté
    else
    {
        echo "Un problème est survenu. Veuillez réessayer ultérieurement.";
    }

    // Fermer la requête
    unset($stmt);
}

// Femer la connexion
unset($pdo);
?>