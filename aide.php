<?php
// Activer les erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// TRAITEMENT DU FORMULAIRE 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if (empty($nom) || empty($email) || empty($message)) {
        http_response_code(400);
        echo "Tous les champs sont requis.";
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo "Adresse email invalide.";
        exit;
    }

    // Enregistrement dans un fichier
    $fichier = 'messages_aide.txt';
    $contenu = date('Y-m-d H:i:s') . " | Nom: $nom | Email: $email | Message: $message" . PHP_EOL;

    if (file_put_contents($fichier, $contenu, FILE_APPEND | LOCK_EX)) {
        echo "success";
    } else {
        http_response_code(500);
        echo "Erreur lors de l'enregistrement du message.";
    }
    exit;
}

// AFFICHAGE DES MESSAGES 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messages d'aide reçus</title>
    <style>
         body { font-family: system-ui; background: #F7D9FC; padding: 32px; }
        .container { max-width: 800px; margin: 0 auto; background: white; border-radius: 20px; padding: 32px; }
        h1 { color: #25104D; font-size: 28px; margin-bottom: 24px; }
        pre { background: #F8F4FF; padding: 16px; border-radius: 16px; overflow-x: auto; font-size: 14px; }
        .message { border-bottom: 1px solid #eee; padding: 8px 0; }
    </style>
</head>
<body>
<div class="container">
    <h1>Messages du centre d'aide</h1>
    <?php
    $fichier = 'messages_aide.txt';
    if (file_exists($fichier) && filesize($fichier) > 0) {
        echo '<pre>';
        echo file_get_contents($fichier);
        echo '</pre>';
    } else {
        echo '<p>Aucun message pour le moment.</p>';
    }
    ?>
</div>
</body>
</html>
