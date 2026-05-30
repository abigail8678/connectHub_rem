<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="connexion.css">
    <title>Connexion</title>
</head>
<body>
    <div class="connexion-container">
        <h1>Se connecter</h1>
        <p>Pas encore de compte? <a href="http://localhost/ConnectHub/inscription.php">S'inscrire</a></p>

        <?php
        // Afficher les messages d'erreur s'ils existent
        session_start();
        if(isset($_GET['error'])) {
            echo '<p style="color: red; text-align: center; margin-bottom: 15px;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        if(isset($_GET['success'])) {
            echo '<p style="color: green; text-align: center; margin-bottom: 15px;">Inscription réussie ! Connectez-vous.</p>';
        }
        ?>


        <form action="traitement_connexion.php" method="post">
            <label for="username">Pseudo ou Email <br></label>
            <input type="text" id="username" name="username" required>
            <br>
            <br>
            <label for="password">Mot de passe <br></label>
            <input type="password" id="password" name="password" required>
            <br>           
            <a href="http://localhost/ConnectHub/mdp.php">Mot de passe oublié?</a>
            <br>
            <br>
            <button type="submit">Se connecter</button>
        </form>

    </div>
</body>
</html>