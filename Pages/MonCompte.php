<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Vérifier si l'utilisateur est connecté et a le role user, redirection vers page login dans ce cas
if(!isset($_SESSION["loggedin"]) || $_SESSION["role"] !== 'user')
{
    header("location: login.php");
    exit;
}

// Inclure le fichier config
require_once "../config.php";
 
// Definition et initialisation des variables
$id = $_SESSION['id'];
$password = $new_password = $password_verif = "";
$password_err = $new_password_err = "";

// Traitement des données du formulaire après submit
if($_SERVER["REQUEST_METHOD"] == "POST")
{
    if(!empty($_POST['new-password']))
    {
        // Défintion des variables
        $password = $_POST['password'];
        $new_password = $_POST['new-password'];

        // Préparation d'une requête select
        $sql = 'SELECT MotDePasse FROM utilisateurs WHERE id = :id';
        $stmt = $pdo->prepare($sql);

        // Liaison des variables à la requête en tant que paramètres
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execution de la requête préparée
        if($stmt->execute()) 
        {
            // Récuperation du mot de passe de l'utilisateur
            $password_verif = $stmt->fetchColumn();
        }
        else
        {
            echo "Un problème est survenu. Veuillez réessayer ultérieurement.";
        }

        // Verification de l'ancien mot de passe
        if(!empty(trim($password)) == $_SESSION['username'])
        {
            if(strlen(trim($new_password)) < 8)
            {
                $new_password_err = "Le mot de passe doit contenir un minimum de 8 caractères.";
            }
            else
            {
            // Préparation d'une requête update
            $sql = 'UPDATE utilisateurs SET MotDePasse = :new_password WHERE id = :id';
            $stmt = $pdo->prepare($sql);

            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(':new_password', $new_password, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $new_password = password_hash($new_password, PASSWORD_DEFAULT); // Création d'un hachage du mot de passe

            if(empty($error) && empty($password_err) && empty($new_password_error))
            {
                // Execution de la requête préparée
                if($stmt->execute()) 
                {
                    // Confirmation de modification de compte et redirection vers page mon compte
                    echo"<script type='text/javascript'>
                    alert('Vos informations ont été modifiées.')
                    document.location.href = 'MonCompte.php';
                    </script>";
                }
                else
                {
                    echo "Un problème est survenu. Veuillez réessayer ultérieurement.";
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
        else
        {
            $password_err = "Mot de passe invalide.";
        }

    }
}

// Femer la connexion
unset($pdo);
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
                                <li class="navbar-item active">
                                    <?php
                                    // Afficher l'option 'mon compte' si l'utilisateur connecté a le role user
                                    if(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'user')
                                    {
                                        echo '<a class="nav-link mx-4" href="MonCompte.php">Mon compte</a>';
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

        <div class="container"> 
            <div class="row jumbotron mt-5">
                <h1 class="col text-center">Mes informations</h1>
            </div>
        </div>

        <!--Formulaire-->
        <div class="container" id="informations">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> <!--Appelle le script (self) après submit-->
                <div class="form-group row">
                    <label for="username" class="col-sm-3 col-form-label"><b>Nom d'utilisateur</b></label>
                    <div class="col-sm-3">
                        <input type="username" class="form-control" id="username" name="username" aria-describedby="username-help"
                        value="<?php echo htmlspecialchars($_SESSION['username']); ?>" readonly>
                    </div>
                    <div class="col-sm-6">
                        <small id="username-help" class="form-text text-muted">Veuillez contacter votre administrateur pour changer 
                        de nom d'utilisateur.</small>
                    </div>
                </div>                
                <div class="form-group row">
                    <label for="role" class="col-sm-3 col-form-label"><b>Rôle</b></label>
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="role" name="role" aria-describedby="role-help" 
                        value="<?php echo htmlspecialchars($_SESSION['role']); ?>" readonly>
                    </div>
                    <div class="col-sm-6">
                        <small id="role-help" class="form-text text-muted">Veuillez contacter votre administrateur pour changer 
                        de rôle.</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="password" class="col-sm-3 col-form-label"><b>Ancien mot de passe</b></label>
                    <div class="col-sm-3">
                        <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" 
                        id="password" name="password">
                        <span class="invalid-feedback"><?php echo $password_err; ?></span>
                    </div>

                    <label for="new-password" class="col-sm-3 col-form-label"><b>Nouveau mot de passe</b></label>
                    <div class="col-sm-3">
                        <input type="new-password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" 
                        id="new-password" name="new-password">
                        <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary ml-auto">Modifier</button>
                </div>
            </form>
        </div>

        <div class="bg-light">
            <div class="container-fluid">        
                <!--FOOTER-->
                <footer class="row" style="margin-top: 50px">
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