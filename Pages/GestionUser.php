<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Vérifier si l'utilisateur est connecté et a le role admin, redirection vers page login le cas échéant
if(!isset($_SESSION["loggedin"]) && $_SESSION["role"] !== 'admin')
{
    header("location: login.php");
    exit;
}

// Inclure le fichier config
require_once "../config.php";

// Préparation d'une requête select
$sql = "SELECT * FROM utilisateurs";

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
        <!--Bootstrap 4-->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" 
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <!--Font Awesome 4-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                                <li class="navbar-item active">
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
        
        <div class="container" id="gestion-user">  
            <div class="mt-5 text-center">
                <button type="button" class="btn btn-info" onclick="location.href='CreationUser.php';">Créer un compte</button>
            </div>          
            <!--RECHERCHE-->
            <div class="row mb-3 mt-5">
                <div class="col">
                    <input class="form-control" id="searchInput" type="text" placeholder="Rechercher...">
                </div>
            </div>            
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nom d'utilisateur</th>
                        <th scope="col">Mot de passe</th>
                        <th scope="col">Rôle</th>
                        <th scope="col">Statut</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody id="tablelist">
                <!--ON PARCOURT LES LIGNES DE LA TABLE UTILISATEURS DE LA BDD-->
                <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                    <tr>
                        <th scope="row"><?php echo htmlspecialchars($row['id']); ?></th>
                        <td><?php echo htmlspecialchars($row['NomUser']); ?></td>
                        <td><?php echo htmlspecialchars($row['MotDePasse']); ?></td>
                        <td><?php echo htmlspecialchars($row['Role']); ?></td>
                        <td><?php echo htmlspecialchars($row['Statut']); ?></td>
                        <td>
                        <form method="post" action="">
                            <!--SUPPRESSION DE COMPTE-->
                            <button class="button-link" type="submit" formaction="supprimer.php" name="id" value="<?php echo htmlspecialchars($row['id']); ?>" 
                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?')" title="Supprimer"><i class="fa fa-trash"></i></button>
                            <!--SUSPENSION ET REACTIVATION-->
                            <?php if($row['Statut'] != 'suspendu') : ?>
                                <button class="button-link" type="submit" formaction="suspension.php" name="id" value="<?php echo htmlspecialchars($row['id']); ?>" 
                                onclick="return confirm('Êtes-vous sûr de vouloir suspendre ce compte ?')" title="Bannir"><i class="fa fa-ban"></i></button>
                            <?php elseif($row['Statut'] == 'suspendu'): ?>
                                <button class="button-link" type="submit" formaction="suspension.php" name="id" value="<?php echo htmlspecialchars($row['id']); ?>" 
                                onclick="return confirm('Êtes-vous sûr de vouloir réactiver ce compte ?')" title="Réactiver"><i class="fa fa-check"></i></button>
                            <?php endif; ?> 
                            <!--MODIFICATION-->
                            <button class="button-link" type="submit" formaction="modification.php" name="id" value="<?php echo htmlspecialchars($row['id']); ?>" 
                            title="Modifier"><i class="fa fa-gear"></i></button>
                        </form>
                        </td>
                    </tr>   
                <?php endwhile; ?>
                </tbody>
            </table>
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

        <!--BARRE DE RECHERCHE-->
        <script>
            $(document).ready(function(){
                $("#searchInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#tablelist tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
        </script>
    </body>
</html>