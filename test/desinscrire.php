<?php
require_once "connexion.php";
require_once "constants.php";
require_once "user.php";

Singleton::setConfiguration(HOST, PORT, DB, USER1, PASS1);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $model = new User(HOST, PORT, DB, USER1, PASS1, 'newsletter');  
    $nom = $_POST['nom'];
    $model->delete($nom);
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
</head>
<body>
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

    <form class="p-5" method="post" action="">
        <h2>Désinscrivez-vous à la newsletter</h2>
        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" name="nom" class="form-control" id="nom" placeholder="Entrez votre nom">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Adresse mail</label>
            <input type="email" name="email" class="form-control" id="email" placeholder="Entrez votre adresse email">
        </div>
        <input type="submit" value="Envoyer">
    </form>

    <footer>
        <p>Tous droits réservés &copy; 2023</p>
    </footer>
</body>
</html>