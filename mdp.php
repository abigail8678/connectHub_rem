<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="mdp.css">
</head>
<body>
    <div class="mdp-container">
        <h1>Mot de passe oublié</h1>

        <?php
        if(isset($_GET['error'])) {
            echo '<p style="color: red; text-align: center;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        if(isset($_GET['success'])) {
            echo '<p style="color: green; text-align: center;">Un email de réinitialisation vous a été envoyé !</p>';
        }
        ?>

        <p>Nouv. mot de passe</p>
        <form action="traitement_mdp.php" method="post">
            <label for="email">Email <br></label>
            <input type="email" id="email" name="email" required>
            <br>
            <br>
            <button type="submit">Envoyer</button>
        </form>

        <br>
        <p style="text-align: center;"><a href="http://localhost/ConnectHub/connexion.php">Retour à la connexion</a></p>
    </div>
</body>
</html>