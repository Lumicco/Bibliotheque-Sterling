<?php
// Ouvrir la session, permet d'accéder à la variable &_SESSION
session_start();

// Vérifier si l'utilisateur est connecté et a le role admin, redirection vers page login dans le cas échéant
if(!isset($_SESSION["loggedin"]) && $_SESSION["role"] !== 'admin')
{
    header("location: login.php");
    exit;
}

// Inclure le fichier config
require_once "../config.php";
 
// Definition et initialisation des variables
$title = $author = $cover = $file = "";
$title_err = $author_err = $cover_err = $file_err = "";
 
// Traitement des données du formulaire après submit
if($_SERVER["REQUEST_METHOD"] == "POST")
{ 
    // Afficher une erreur si le champ (la variable) est vide
    if(empty(trim($_POST["title"])))
    {
        $title_err = "Veuillez renseigner un titre.";
    } 
    else
    {
        // Préparation d'une requête select
        $sql = "SELECT id FROM livres WHERE Libelle = :title";
        
        if($stmt = $pdo->prepare($sql))
        {
            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            
            // Définition des paramètres
            $param_title = trim($_POST["title"]);
            
            // Execution de la requête préparée
            if($stmt->execute())
            {
                $title = trim($_POST["title"]);
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
    if(empty(trim($_POST["author"])))
    {
        $author_err = "Veuillez renseigner un nom d'auteur.";     
    } 
    // Définition de la variable
    else
    {
        $author = trim($_POST["author"]);
    }

    // Afficher une erreur si le champ (la variable) est vide, $_FILES est un array donc on regarde la taille
    if($_FILES['cover']['size'] == 0)
    {
        $cover_err = "Veuillez télécharger une image de couverture.";     
    } 
    else
    {
        // Définition d'un int pour le nom de fichier
        $n = 1;

        // Récupération de l'extension du fichier image
        $img_ext = '.'.pathinfo($target_path.basename($_FILES['cover']['name']), PATHINFO_EXTENSION);

        // Récupération de l'extension du fichier pdf
        $file_ext = '.'.pathinfo($target_path.basename($_FILES['file']['name']), PATHINFO_EXTENSION);

        // Définition du chemin de destination (un dossier qui porte le nom du fichier)
        $ds = DIRECTORY_SEPARATOR;
        $target_path = join($ds, array("C:", "wamp64", "www", "PPE", "Books"));
        $target_path = $target_path.$ds.basename($_FILES['file']['name'], $file_ext).$ds;

        // Crée le dossier s'il n'existe pas
        if(!is_dir($target_path))
        {
            mkdir($target_path);
        }

        // Tant que le nom du fichier existe déjà, incrémener le numéro dans le nom de 1 (évite qu'un fichier avec le même nom soit écrasé)
        while(is_file($target_path.basename($_FILES['cover']['name'], $img_ext).'_'.$n.$img_ext))
        {
            $n += 1;          
        }
        
        // Définition du nom de fichier
        $target_path = $target_path.basename($_FILES['cover']['name'], $img_ext).'_'.$n.$img_ext; 
        
        // Définition de la variable, remplacement du chemin absolu par le chemin rélatif
        $cover = str_replace("C:".$ds."wamp64".$ds."www", "..", $target_path);
        
        // Copie du fichier vers le chemin de destination
        if(move_uploaded_file($_FILES['cover']['tmp_name'], $target_path)) 
        {  
            echo "Succès.";
        } 
        // Afficher une erreur si le fichier n'a pas pu copié
        else
        {  
            echo "Un problème est survenu. Veuillez réessayer ultérieurement.";  
        }  
    }

    // Afficher une erreur si le champ (la variable) est vide, $_FILES est un array donc on regarde la taille
    if($_FILES['file']['size'] == 0)
    {
        $file_err = "Veuillez télécharger un fichier.";
    } 
    else
    {
        // Définition d'un int pour le nom de fichier
        $n = 1;

        // Récupération de l'extension du fichier
        $file_ext = '.'.pathinfo($target_path.basename($_FILES['file']['name']), PATHINFO_EXTENSION);
        
        // Définition du chemin de destination (un dossier qui porte le nom du fichier)
        $ds = DIRECTORY_SEPARATOR;
        $target_path = join($ds, array("C:", "wamp64", "www", "PPE", "Books"));
        $target_path = $target_path.$ds.basename($_FILES['file']['name'], $file_ext).$ds;

        // Crée le dossier s'il n'existe pas
        if(!is_dir($target_path))
        {
            mkdir($target_path);
        }

        // Tant que le nom du fichier existe déjà, incrémener le numéro dans le nom de 1 (évite qu'un fichier avec le même nom soit écrasé)
        while(is_file($target_path.basename($_FILES['file']['name'], $file_ext).'_'.$n.$file_ext))
        {
            $n += 1;          
        }
        
        // Définition du nom de fichier
        $target_path = $target_path.basename($_FILES['file']['name'], $file_ext).'_'.$n.$file_ext; 
        
        // Définition de la variable, remplacement du chemin absolu par le chemin rélatif
        $file = str_replace("C:".$ds."wamp64".$ds."www", "..", $target_path);

        // Copie du fichier vers le chemin de destination
        if(move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) 
        {  
            echo "Succès.";
        } 
        // Afficher une erreur si le fichier n'a pas pu copié
        else
        {  
            echo "Un problème est survenu. Veuillez réessayer ultérieurement.";  
        }  
    }
    
    // Vérification des erreurs
    if(empty($title_err) && empty($author_err) && empty($cover_err) && empty($file_err))
    {    
        // Préparation d'une requête insert
        $sql = "INSERT INTO livres (Libelle, NomAuteur, Couverture, Fichier) VALUES (:title, :author, :cover, :file)";
         
        if($stmt = $pdo->prepare($sql))
        {
            // Liaison des variables à la requête en tant que paramètres
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":author", $param_author, PDO::PARAM_STR);
            $stmt->bindParam(":cover", $param_cover, PDO::PARAM_STR);
            $stmt->bindParam(":file", $param_file, PDO::PARAM_STR);
            
            // Définition des paramètres
            $param_title = $title;
            $param_author = $author;
            $param_cover = $cover;
            $param_file = $file;

            // Afficher une erreur si la requête n'a pas pu être éxecuté
            if(!$stmt->execute())
            {
                echo "Un problème est survenu. Veuillez réessayer ultérieurement.";
            }
            else
            {
                // Redirection vers la page succès
                header("location: success.php");
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
        <div class="container shadow" id="upload">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data"> <!--Appelle le script (self) après submit-->
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" 
                    value="<?php echo $title; ?>" id="title" name="title">
                    <span class="invalid-feedback"><?php echo $title_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="author">Auteur</label>
                    <input type="text" class="form-control <?php echo (!empty($author_err)) ? 'is-invalid' : ''; ?>" 
                    value="<?php echo $author; ?>" id="author" name="author">
                    <span class="invalid-feedback"><?php echo $author_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="cover">Image de couverture</label>
                    <input type="file" accept="image/*" class="form-control <?php echo (!empty($cover_err)) ? 'is-invalid' : ''; ?>" 
                    id="cover" name="cover">
                    <span class="invalid-feedback"><?php echo $cover_err; ?></span>
                </div>
                <div class="form-group">
                    <label for="file">Fichier</label>
                    <input type="file" accept="application/pdf" class="form-control <?php echo (!empty($file_err)) ? 'is-invalid' : ''; ?>" 
                    id="file" name="file">
                    <span class="invalid-feedback"><?php echo $file_err; ?></span>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
                <button type="button" class="btn btn-secondary" onclick="location.href='../index.php';">Annuler</button>
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