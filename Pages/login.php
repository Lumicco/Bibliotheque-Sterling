<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();
 
// Vérifier si l'utilisateur est déjà connecté, dans ce cas redirection vers page bienvenue
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true)
{
    header("location: bienvenue.php");
    exit;
}
 
// Inclure le fichier config
require_once "../config.php";
 
// Definition et initialisation des variables
$username = $password = "";
$username_err = $password_err = $login_err = $banned = "";
 
// Traitement des données du formulaire après submit
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Afficher une erreur si le champ (la variable) est vide
    if(empty(trim($_POST["username"])))
    {
        $username_err = "Veuillez renseigner un nom d'utilisateur.";
    } 
    // Définition de la variable
    else
    {
        $username = trim($_POST["username"]);
    }
    
    // Afficher une erreur si le champ (la variable) est vide
    if(empty(trim($_POST["password"])))
    {
        $password_err = "Veuillez renseigner un mot de passe.";
    } 
    // Définition de la variable
    else
    {
        $password = trim($_POST["password"]);
    }
    
    // Vérification des erreurs
    if(empty($username_err) && empty($password_err))
    {
        // Préparation d'une requête select
        $sql = "SELECT id, NomUser, MotDePasse, Role, Statut FROM utilisateurs WHERE NomUser = :username";
        
        if($stmt = $pdo->prepare($sql))
        {
            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            
            // Définition des paramètres
            $param_username = trim($_POST["username"]);
            
            // Execution de la requête préparée
            if($stmt->execute())
            {
                // Vérifier si le nom d'utilisateur existe
                if($stmt->rowCount() == 1) // Retourne le nombre de ligne affecté par la dernière requête
                {
                    // Vérifier si le mot de passe existe
                    if($row = $stmt->fetch())
                    {
                        // Définition des variables avec les valeurs situées sur la ligne où a été trouvé le nom d'utilisateur
                        $id = $row["id"];
                        $username = $row["NomUser"];
                        $hashed_password = $row["MotDePasse"];
                        $role = $row["Role"];
                        $statut = $row["Statut"];

                        // Vérifier si le mot de passe correspond au hachage
                        if(password_verify($password, $hashed_password))
                        {
                            // Vérifier si le compte est suspendu
                            if($statut == "suspendu")
                            {
                                // Afficher une erreur si le compte est suspendu
                                $banned = "Votre compte a été suspendu.";
                            }
                            else
                            {
                                // Si le mot de passe est correct, ouvrir une nouvelle session si pas encore ouvert
                                if(!isset($_SESSION)) 
                                { 
                                    session_start(); 
                                }
                                
                                // Mettre les données dans des variables session
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;  
                                $_SESSION["role"] = $role;        
                                $_SESSION["statut"] = $statut;                  
                                
                                // Redirection vers la page bienvenue
                                header("location: bienvenue.php");
                            }
                        } 
                        else
                        {
                            // Afficher une erreur si mot de passe invalide
                            $login_err = "Mot de passe invalide.";
                        }
                    }
                } 
                else
                {
                    // Afficher une erreur si identifiant invalide
                    $login_err = "Identifiant invalide.";
                }
            } 
            else
            {
                echo "Un problème est survenu. Veuillez réessayer ultérieurement.";
            }

            // Fermer la requête
            unset($stmt);
        }
    }
    
    // Fermer la connexion
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <!-- Bootstrap 4 -->
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
                            </ul>
                        </div>
                    </nav>
                </header>
            </div>
        </div>
        
        <!--Formulaire-->
        <div class="container shadow" id="login">
            <!--Afficher une erreur si la variable erreur n'est pas vide-->
            <?php 
            if(!empty($login_err))
            {
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }    
            
            // Afficher une erreur si la variable banned n'est pas vide
            if(!empty($banned))
            {
                echo '<div class="alert alert-danger">' . $banned . '</div>';
            }
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> <!--Appelle le script (self) après submit-->
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="username" id="username" name="username" class="form-control 
                    <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control 
                    <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Connexion</button>
                <p><br>Vous n'avez pas encore de compte ? <a href="register.php">Créer un compte</a>.</p>
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

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" 
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" 
        crossorigin="anonymous"></script>

        <!-- Popper.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" 
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" 
        crossorigin="anonymous"></script>

        <!-- Bootstrap js -->
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" 
        crossorigin="anonymous"></script>
    </body>
</html>