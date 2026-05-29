<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de profil</title>
    <link rel="stylesheet" href="creationprofil.css">

    <script>
    function previewImage(event) {
    const preview = document.getElementById('preview');
    preview.src = URL.createObjectURL(event.target.files[0]);
    preview.style.display = 'block';}
    </script>
</head>
<body>
    <div class="creation-container">
        <?php
        // Afficher les messages d'erreur/succès
        if(isset($_GET['error'])) {
            echo '<p style="color: red; text-align: center;">' . htmlspecialchars($_GET['error']) . '</p>';
        }
        if(isset($_GET['success'])) {
            echo '<p style="color: green; text-align: center;">Profil créé avec succès !</p>';
        }
        ?>
        
        <form action="traitement_profil.php" method="post" enctype="multipart/form-data">
            <h1>Création du profil utilisateur</h1>
           
            <label for="avatar">Photo de profil :</label>
            <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewImage(event)">
            <img id="preview" style="width: 150px; height: 150px; border-radius: 50%; display: none;">
            <br>
            <br>
            <div class="enregistrement">
                <label for="username">PSEUDO <br></label>
                <input type="text" id="username" name="username" required>
                <br>
                <br>
                <label for="description">DESCRIPTION <br></label>
                <textarea id="description" name="description" rows="4" required></textarea>
                <br>
                <br>
                <label for="centre">CENTRE D'INTÉRÊT <br></label>
                <select id="centre" name="centre" required>
                    <option value="">Sélectionnez un centre d'intérêt</option>
                    <option value="theatre">Théâtre</option>
                    <option value="musique">Musique</option>
                    <option value="sculpture">Sculpture</option>
                    <option value="écriture">Écriture</option>
                    <option value="cinema">Cinéma</option>
                    <option value="peinture">Peinture</option>
                    <option value="danse">Danse</option>
                    <option value="photographie">Photographie</option>
                    <option value="dessin">Dessin</option>               
                </select>
                <br>
                <br>
                <button type="submit">Valider</button>
            </div>
        </form>
    </div>
</body>
</html>
