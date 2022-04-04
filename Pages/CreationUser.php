<?php
// Inclure le fichier config
require_once "../config.php";

// Vérifier si l'utilisateur est connecté et n'a pas le role admin, redirection vers page login le cas échéant
if(!isset($_SESSION["loggedin"]) && $_SESSION["role"] !== 'admin')
{
    header("location: login.php");
    exit;
}
 
// Definition et initialisation des variables
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Traitement des données du formulaire après submit
if($_SERVER["REQUEST_METHOD"] == "POST")
{ 
    // Afficher une erreur si le champ (la variable) est vide
    if(empty(trim($_POST["username"])))
    {
        $username_err = "Veuillez renseigner un nom d'utilisateur.";
    } 
    // Afficher une erreur si le nom d'utilisateur contient autre chose que des lettres et des chiffres
    elseif(!preg_match('/^[a-zA-Z0-9]+$/', trim($_POST["username"])))
    {
        $username_err = "Le nom d'utilisateur ne peut contenir que des lettres et des chiffres.";
    } 
    else
    {
        // Préparation d'une requête select
        $sql = "SELECT id FROM utilisateurs WHERE NomUser = :username";
        
        if($stmt = $pdo->prepare($sql))
        {
            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Définition des paramètres
            $param_username = trim($_POST["username"]);
            
            // Execution de la requête préparée
            if($stmt->execute())
            {
                // Afficher une erreur si le même nom d'utilisateur est déjà dans la BDD
                if($stmt->rowCount() == 1) // Retourne le nombre de ligne affecté par la dernière requête
                {
                    $username_err = "Ce nom d'utilisateur existe déjà.";
                } 
                // Définition de la variable
                else
                {
                    $username = trim($_POST["username"]);
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
    }
    
    // Afficher une erreur si le champ (la variable) est vide
    if(empty(trim($_POST["password"])))
    {
        $password_err = "Veuillez renseigner un mot de passe.";     
    } 
    // Afficher une erreur si le mot de passe contient moins que 8 caractères
    elseif(strlen(trim($_POST["password"])) < 8)
    {
        $password_err = "Le mot de passe doit contenir un minimum de 8 caractères.";
    }
    // Définition de la variable
    else
    {
        $password = trim($_POST["password"]);
    }
    
    // Afficher une erreur si le champ (la variable) est vide
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Veuillez confirmer le mot de passe.";     
    } 
    else
    {
        // Définition de la variable
        $confirm_password = trim($_POST["confirm_password"]);
        // Afficher une erreur si les mots de passe ne correspondent pas
        if(empty($password_err) && ($password != $confirm_password))
        {
            $confirm_password_err = "Les mots de passe ne correspondent pas.";
        }
    }
    
    // Vérification des erreurs
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err))
    {    
        // Préparation d'une requête insert
        $sql = "INSERT INTO utilisateurs (NomUser, MotDePasse) VALUES (:username, :password)";
         
        if($stmt = $pdo->prepare($sql))
        {
            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            
            // Définition des paramètres
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Création d'un hachage du mot de passe
            
            // Execution de la requête préparée
            if($stmt->execute())
            {   
                // Confirmation de création de compte et redirection vers page login
                echo"<script type='text/javascript'>
                alert('Le compte a été créé.')
                document.location.href = 'GestionUser.php';
                </script>";
            } 
            // Afficher une erreur si la requête n'a pas pu être éxecuté
            else
            {
                echo "Un problème est survenu. Veuillez réessayer ultérieurement.";
            }

            // Fermer la requête
            unset($stmt);
        }
    }
    
    // Femer la connexion
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <!--Bootstrap 4-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="../style.css" />
        <title>Bibliothèque Sterling</title>
    </head>

    <body>
        <div class="bg-dark">
            <div class="container">        
                <!--HEADER-->
                <header class="row">
                    <nav class="col navbar navbar-expand-lg navbar-dark">
                        <a href="../index.php" class="navbar-brand"><img src="../Images/BiblioLogo.png" class="logo" alt="Logo de la bibliothèque" />Bibliothèque Sterling</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div id="navbarContent" class="collapse navbar-collapse">
                            <ul class="navbar-nav">
                                <li class="navbar-item">
                                    <a class="nav-link mx-4" href="../index.php">Le catalogue</a>
                                </li>
                                <li class="navbar-item">
                                    <?php
                                    // Afficher l'option 'ajouter un livre' si l'utilisateur connecté a le role admin
                                    if(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'admin')
                                    {
                                        echo '<a class="nav-link mx-4" href="AjoutLivre.php">Ajouter un livre</a>';
                                    }

                                    ?>
                                </li>
                                <li class="navbar-item">
                                    <?php
                                    // Afficher l'option 'gérer les utilisateurs' si l'utilisateur connecté a le role admin
                                    if(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'admin')
                                    {
                                        echo '<a class="nav-link mx-4" href="GestionUser.php">Gérer les utilisateurs</a>';
                                    }

                                    ?>
                                </li>
                            </ul>
                            <ul class="navbar-nav ml-auto">
                                <li class="navbar-item">
                                    <?php
                                    // Afficher bouton connexion si utilisateur déjà connecté, bouton déconnexion le cas échéant
                                    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
                                    {
                                        echo '<a class="nav-link ml-4" href="login.php">
                                        <button type="button" class="btn btn-secondary">Connexion</button>
                                        </a>';          
                                    }
                                    else
                                    {
                                        echo '<a class="nav-link ml-4" href="logout.php">
                                        <button type="button" class="btn btn-danger">Se déconnecter</button>
                                        </a>';
                                    }
                                    ?>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </header>
            </div>
        </div>
        
        <!--Formulaire-->
        <div class="container shadow" id="login">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> <!--Appelle le script (self) après submit-->
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" 
                    value="<?php echo $username; ?>" id="username" name="username">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                    value="<?php echo $password; ?>" id="password" name="password">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmation du mot de passe</label>
                    <input type="password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" 
                    value="<?php echo $confirm_password; ?>" id="confirm_password" name="confirm_password">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Créer</button>
            </form>
        </div>

        <div class="bg-light">
            <div class="container">        
                <!--FOOTER-->
                <footer class="row fixed-bottom">
                    <div class="col">
                        <ul class="list-inline text-center">                            
                            <li class="list-inline-item">
                                <a href="Conditions-Utilisation.php">Conditions d'Utilisation</a>
                            </li>
                            <li class="list-inline-item">
                                <a href="Politique-Confidentialite.php">Politique de Confidentialité</a>
                            </li>
                        </ul>
                    </div>
                </footer>
            </div>
        </div>

        <!--jQuery-->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" 
        crossorigin="anonymous"></script>

        <!--Popper.js-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" 
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" 
        crossorigin="anonymous"></script>

        <!--Bootstrap js-->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" 
        crossorigin="anonymous"></script>
    </body>
</html>