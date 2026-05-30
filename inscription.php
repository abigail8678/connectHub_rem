<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="inscription.css">
</head>
<body>
    <h1>Inscription</h1>
    <p>Vous êtes déjà inscrit? <a href="connexion.html">Se connecter</a></p>

    <div class="inscription-container">
        <?php
        if(isset($_GET['error'])) {
            echo '<p style="color: red; text-align: center;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        ?>

        <form action="traitement_inscription.php" method="post">
            <label for="name">Nom <br></label>
            <input type="text" id="name" name="name" required>
            <br>
            
            <label for="surname">Prénom <br></label>
            <input type="text" id="surname" name="surname" required>
            <br>
           
            <label for="birth_date">Date de naissance <br></label>
            <input type="date" id="birth_date" name="birth_date" required>
            <br>
            
            <label for="genre">Genre <br></label>
            <select id="genre" name="genre" required>
                <option value=""></option>
                <option value="homme">Homme</option>
                <option value="femme">Femme</option>
                <option value="autre">Autre</option>
            </select>
            <br>
           
            <label for="email">Email <br></label>
            <input type="email" id="email" name="email" required>
            <br>
            
            <label for="number">Numéro de téléphone <br></label>
            <input type="text" id="number" name="number" required>
            <br>
            
            <label for="password">Mot de passe <br></label>
            <input type="password" id="password" name="password" required>
            <br>
            
            <label for="confirm_password">Confirmer le mot de passe <br></label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <br>
            
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</body>
</html>