<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Inclure le fichier config
require_once "config.php";

// Préparation d'une requête select
$sql = "SELECT * FROM livres";

try
{
    $stmt = $pdo->query($sql);

    if($stmt == false)
    {
        die("Erreur");
    }
}
catch(PDOException $e)
{
    echo $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8" />
        <!-- Bootstrap 4 -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css" />
        <title>Bibliothèque Sterling</title>
    </head>

    <body>
        <div class="bg-dark">
            <div class="container">        
                <!--HEADER-->
                <header class="row">
                    <nav class="col navbar navbar-expand-lg navbar-dark">
                        <a href="index.php" class="navbar-brand"><img src="Images/BiblioLogo.png" class="logo" alt="Logo de la bibliothèque" />Bibliothèque Sterling</a>
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div id="navbarContent" class="collapse navbar-collapse">
                            <ul class="navbar-nav">
                                <li class="navbar-item active">
                                    <a class="nav-link mx-4" href="index.php">Le catalogue</a>
                                </li>
                                <li class="navbar-item">
                                    <?php
                                    // Afficher l'option 'mon compte' si l'utilisateur connecté a le role user
                                    if(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'user')
                                    {
                                        echo '<a class="nav-link mx-4" href="Pages/MonCompte.php">Mon compte</a>';
                                    }

                                    ?>
                                </li>
                                <li class="navbar-item">
                                    <?php
                                    // Afficher l'option 'ajouter un livre' si l'utilisateur connecté a le role admin
                                    if(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'admin')
                                    {
                                        echo '<a class="nav-link mx-4" href="Pages/AjoutLivre.php">Ajouter un livre</a>';
                                    }

                                    ?>
                                </li>
                                <li class="navbar-item">
                                    <?php
                                    // Afficher l'option 'gérer les utilisateurs' si l'utilisateur connecté a le role admin
                                    if(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'admin')
                                    {
                                        echo '<a class="nav-link mx-4" href="Pages/GestionUser.php">Gérer les utilisateurs</a>';
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
                                        echo '<a class="nav-link ml-4" href="Pages/login.php">
                                        <button type="button" class="btn btn-secondary">Connexion</button>
                                        </a>';          
                                    }
                                    else
                                    {
                                        echo '<a class="nav-link ml-4" href="Pages/logout.php">
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

        <!--CAROUSEL-->
        <div class="container"> 
            <div id="carouselControls" class="carousel slide" data-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="Images/Carousel1.png" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                        <img src="Images/Carousel2.png" class="d-block w-100" alt="...">
                    </div>
                </div>
                <a class="carousel-control-prev" href="#carouselControls" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#carouselControls" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>

            <!--JUMBOTRON-->
            <div class="row jumbotron">
                <h1 class="col text-center">Bienvenue !</h1>
            </div>

            <!--RECHERCHE-->
            <div class="row mb-3">
                <div class="col">
                    <input class="form-control" id="searchInput" type="text" placeholder="Rechercher...">
                </div>
            </div>

            <!--CATALOGUE-->
            <div class="row" id="bookList">
                <!--ON PARCOURT LES LIGNES DE LA TABLE LIVRES DE LA BDD-->
                <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                <div class="col-auto card shadow">
                    <img src="<?php echo htmlspecialchars($row['Couverture']); ?>" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <h1><?php echo htmlspecialchars($row['Libelle']); ?></h1>
                        <p><i><?php echo htmlspecialchars($row['NomAuteur']); ?></i></p>
                        <!--OUVRE LE FICHIER SI UTILISATEUR CONNECTE, RENVOIE SUR PAGE LOGIN LE CAS ECHEANT-->
                        <?php
                        if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true)
                        {
                            echo                  
                            '<a href="Pages/login.php">
                            <button type="button" class="btn btn-secondary">Emprunter</button>
                            </a>';
                        }
                        // Si l'utilisateur a le rôle admin, bouton emprunter et remplacé par bouton supprimer
                        elseif(isset($_SESSION["loggedin"]) && $_SESSION["role"] == 'admin')
                        { ?>               
                            <form method="post" action="Pages/SuppressionLivre.php">
                            <button type="submit" class="btn btn-danger" name="id" value="<?php echo htmlspecialchars($row['id']); ?>"
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">Supprimer</button>
                            </form>
                        <?php }
                        else
                        { ?>                            
 
                            <form id="historique" method="post" action="Pages/emprunt.php">                            
                                <input type="hidden" name="file" value="<?php echo htmlspecialchars($row['Fichier']); ?>"/>             
                                <button type="submit" class="btn btn-info" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                Emprunter</button>
                            </form>
                        <?php }
                        ?>
                    </div>
                </div>
                <?php endwhile; ?>

                <!--A VENIR-->
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
            
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
            
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
                <div class="col-auto card shadow">
                    <img src="Images/Cover.png" class="card-img-top" alt="Cover" />
                    <div class="card-body">
                        <p>Disponible prochainement</p>
                    </div>
                </div>
            </div>  
        </div>

        <div class="bg-light">
            <div class="container-fluid">        
                <!--FOOTER-->
                <footer class="row" style="margin-top: 50px">
                    <div class="col">
                        <ul class="list-inline text-center">                            
                            <li class="list-inline-item">
                                <a href="Pages/Conditions-Utilisation.php">Conditions d'Utilisation</a>
                            </li>
                            <li class="list-inline-item">
                                <a href="Pages/Politique-Confidentialite.php">Politique de Confidentialité</a>
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

        <!--BARRE DE RECHERCHE-->
        <script>
            $(document).ready(function(){
                $("#searchInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#bookList .col-auto").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
        </script>
    </body>
</html>