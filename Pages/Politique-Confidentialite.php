<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();
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
                                <li class="navbar-item">
                                    <?php
                                    // Afficher l'option 'mon compte' si l'utilisateur connecté a le role user
                                    if(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'user')
                                    {
                                        echo '<a class="nav-link mx-4" href="MonCompte.php">Mon compte</a>';
                                    }

                                    ?>
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

        <div class="container"> 
            <div class="row jumbotron mt-5">
                <h1 class="col text-center">Politique de confidentialité</h1>
            </div>
        </div>

        <div class="container" style="text-align: justify; font-size: 1.5em; padding-bottom: 100px">
            <p>Les données personnelles collectées et traitées sont celles transmises lors de votre inscription, notamment
                votre nom d'utilisateur et votre mot de passe.<br></p>
            
            <p>Aucun transfert, transmission ou cession de vos données à des tiers non autorisés n’a lieu. Aucune information 
                personnelle autre que celles demandées explicitement n’est collectée à votre insu.<br></p>
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