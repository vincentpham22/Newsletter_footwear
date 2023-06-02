<?php
session_start();
require_once "connexion.php";
require_once "constants.php";
require_once "user.php";

Singleton::setConfiguration(HOST, PORT, DB, USER1, PASS1);

$nom = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $model = new User(HOST, PORT, DB, USER1, PASS1, TABLE);

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
            // $valid = isset($_SESSION["valid"]) ? $_SESSION["valid"] : '';
            $nom = "";
            $email = "";
            header("Location:index.php"); // Redirige vers la même page pour éviter la réinscription lors de l'actualisation
            exit;
        }
    }
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
    <link rel="stylesheet" href="corps.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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

    <form method="post" action="index.php">
        <?php
        if (isset($_SESSION["error"]) && !empty($_SESSION["error"])) {
            echo '<div class="alert alert-danger" role="alert">' . $_SESSION["error"] . '</div>';
            unset($_SESSION["error"]);
        } elseif (isset($_SESSION["valid"]) && !empty($_SESSION["valid"])) {
            echo '<div class="alert alert-success" role="alert">' . $_SESSION["valid"] . '</div>';
            unset($_SESSION["valid"]);
        }
        ?>

        <h2>Inscrivez-vous à la newsletter</h2>
        <div>
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" placeholder="Entrez votre nom d'utilisateur" pattern="[A-Za-z\s]+" title="Veuillez entrer uniquement du texte" value="<?php echo $nom; ?>">
        </div>
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Entrez votre adresse email" value="<?php echo $email; ?>">
        </div>
        <input type="submit" value="S'inscrire">
    </form>


    <footer>
        <div class="ff">
            <ul>
                <li><a href="http://localhost/wordpress1/contact/">Contact</a></li>
                <li><a href="http://localhost/wordpress1/services/">Services</a></li>
                <li><a href="http://localhost/wordpress1/faq/">FAQ</a></li>
            </ul>

        </div>
        <div class="ff1">

            <ul>
                <li><a href="http://localhost/wordpress1/conditions-generales/">Conditions générales</a></li>
                <li><a href="http://localhost/wordpress1/politique-de-confidentialite/">Politique de confidentialité</a></li>
                <li><a href="http://localhost/wordpress1/mentions-legales/">Mentions légales</a></li>
            </ul>
        </div>
        <div class="fff">
            <p>Copyright © 2023 | <strong>Footwear</strong></p>
        </div>
        <div class="rs">
            <ul>
                <li><a href=""><i class="fa-brands fa-instagram"></i></a></li>
                <li><a href=""><i class="fa-brands fa-square-facebook"></i></a></li>
                <li><a href=""><i class="fa-brands fa-twitter"></i></a></li>
                <li><a href=""><i class="fa-brands fa-youtube"></i></a></li>
            </ul>
        </div>


    </footer>
</body>

</html>