<?php
// reset_mdp.php
require_once 'config.php';
session_start();

$token = $_GET['token'] ?? '';

// Vérifier le token
$stmt = $pdo->prepare("
    SELECT user_id, expires_at 
    FROM password_resets 
    WHERE token = ? AND expires_at > NOW()
");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if(!$reset) {
    die("Lien invalide ou expiré. <a href='mdp.php'>Retour</a>");
}

$user_id = $reset['user_id'];

// Traitement du nouveau mot de passe
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    if(strlen($new_password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    if($new_password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    if(empty($errors)) {
        try {
            // Mettre à jour le mot de passe
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $updateStmt->execute([$password_hash, $user_id]);
            
            // Supprimer le token utilisé
            $deleteStmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $deleteStmt->execute([$token]);
            
            header('Location: connexion.php?success=Votre mot de passe a été réinitialisé avec succès');
            exit();
        } catch(PDOException $e) {
            $error = "Erreur technique, veuillez réessayer";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="mdp.css">
</head>
<body>
    <div class="mdp-container">
        <h1>Réinitialisation</h1>
        <p>Nouveau mot de passe</p>
        
        <?php if(isset($error)): ?>
            <p style="color: red; text-align: center;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if(!empty($errors)): ?>
            <?php foreach($errors as $err): ?>
                <p style="color: red; text-align: center;"><?php echo $err; ?></p>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <form method="post">
            <label for="password">Nouveau mot de passe <br></label>
            <input type="password" id="password" name="password" required>
            <br>
            <br>
            <label for="confirm_password">Confirmer le mot de passe <br></label>
            <input type="password" id="confirm_password" name="confirm_password" required>
            <br>
            <br>
            <button type="submit">Réinitialiser</button>
        </form>
        
        <br>
        <p style="text-align: center;"><a href="connexion.php">Retour à la connexion</a></p>
    </div>
</body>
</html>