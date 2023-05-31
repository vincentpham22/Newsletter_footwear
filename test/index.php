<?php
session_start();
require_once "connexion.php";
require_once "constants.php";
require_once "user.php";

Singleton::setConfiguration(HOST, PORT, DB, USER1, PASS1);


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $model = new User(HOST, PORT, DB, USER1, PASS1, 'newsletter');

    $nom = (isset($_POST["nom"]) && !empty($_POST["nom"])) ? htmlspecialchars($_POST["nom"]) : null;
    $email = (isset($_POST["email"]) && !empty($_POST["email"])) ? htmlspecialchars($_POST["email"]) : null;

    // Vérifier si l'e-mail est vide ou non valide
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["error"] = "Veuillez entrer une adresse e-mail valide.";
    } else {
        // Vérifier si l'e-mail existe déjà dans la base de données
        $existEmail = $model->getByEmail($email);
        if ($existEmail) {
            $_SESSION["error"] = "Cet e-mail existe déjà.";
        } else {
            $model->insert(['nom' => $nom, 'email' => $email]);
            $model->sendEmail($email);
            $_SESSION["valid"] = "Vous vous êtes bien inscrit";
            header("Location: index.php"); // Redirection vers la page index.php
            exit(); // Terminer le script pour éviter toute exécution supplémentaire
        }
    }
}

$error = isset($_SESSION["error"]) ? $_SESSION["error"] : '';
$valid = isset($_SESSION["valid"]) ? $_SESSION["valid"] : '';

if (isset($_SESSION["error"])) {
    unset($_SESSION["error"]);
}

if (isset($_SESSION["valid"])) {
    unset($_SESSION["valid"]);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
</head>

<body class="back">
    <header>
        <figure>
            <img src="img/logo.png" alt="">
        </figure>
    </header>

    <nav>
        <ul>
            <li><a href="#">Accueil</a></li>
            <li><a href="#">Boutique</a></li>
            <li><a href="#">Mon compte</a></li>
        </ul>
    </nav>

    <section class="part1">
        <form class="p-5" method="post" action="index.php">
            <?php
            if (!empty($error)) {
                echo '<div class="alert alert-danger" role="alert">' . $error . '</div>';
                unset($error); // Réinitialiser la variable $error après l'affichage
            } elseif (!empty($valid)) {
                echo '<div class="alert alert-success" role="alert">' . $valid . '</div>';
            }
            ?>

            <h2>Inscrivez-vous à la newsletter</h2>
            <div class="champ">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" name="nom" class="form-control" id="nom" placeholder="Entrez votre nom d'utilisateur" pattern="[A-Za-z\s]+" title="Veuillez entrer uniquement du texte">
            </div>
            <div class="champ">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Entrez votre adresse email">
            </div>
            <input type="submit" value="Envoyer">
        </form>
    </section>

    <footer>
        <div class="ff">
            <ul>
                <li>Contact</li>
                <li>Services</li>
                <li>FAQ</li>
            </ul>

            <ul>
                <li>Conditions générales</li>
                <li>Politique de confidentialité</li>
                <li>Mentions légales</li>
            </ul>
        </div>
        <div class="fff">Copyright 2023 | Footwear</div>
        <div class="rs">
            <ul>
                <li><img src="img/icons8-instagram.png" alt=""></li>
                <li><img src="img/icons8-facebook.png" alt=""></li>
                <li><img src="img/icons8-twitter.png" alt=""></li>
                <li><img src="img/icons8-youtube.png" alt=""></li>
            </ul>
        </div>


    </footer>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>

</html>