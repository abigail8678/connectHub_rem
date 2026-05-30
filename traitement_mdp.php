<?php
// traitement_mdp.php
require_once 'config.php';
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $email = trim($_POST['email'] ?? '');
    
    if(empty($email)) {
        header('Location: mdp.php?error=Veuillez entrer votre email');
        exit();
    }
    
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: mdp.php?error=Email invalide');
        exit();
    }
    
    try {
        // Vérifier si l'email existe
        $stmt = $pdo->prepare("SELECT id, pseudo, email FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateurs = $stmt->fetch();
        
        if($utilisateurs) {
            // Générer un token unique
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Supprimer les anciens tokens pour cet utilisateur
            $deleteStmt = $pdo->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $deleteStmt->execute([$utilisateurs['id']]);
            
            // Insérer le nouveau token
            $insertStmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, token, expires_at) 
                VALUES (?, ?, ?)
            ");
            $insertStmt->execute([$utilisateurs['id'], $token, $expires]);
            
            // Envoyer l'email (à configurer selon votre serveur)
            $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/ConnectHub/reset_mdp.php?token=" . $token;
            
            // Pour tester sans serveur mail, afficher le lien
            // En production, utilisez mail() ou PHPMailer
            $to = $email;
            $subject = "Réinitialisation de votre mot de passe";
            $message = "Bonjour,\n\n";
            $message .= "Vous avez demandé à réinitialiser votre mot de passe.\n\n";
            $message .= "Cliquez sur ce lien pour réinitialiser votre mot de passe :\n";
            $message .= $reset_link . "\n\n";
            $message .= "Ce lien est valable 1 heure.\n\n";
            $message .= "Si vous n'avez pas fait cette demande, ignorez cet email.\n";
            $headers = "From: no-reply@votre-site.com";
            
            // Tenter d'envoyer l'email
            if(mail($to, $subject, $message, $headers)) {
                header('Location: mdp.php?success=1');
            } else {
                // Pour le développement, afficher le lien
                echo "<div style='text-align: center; margin-top: 50px;'>
                        <h2>Lien de réinitialisation (mode développement)</h2>
                        <p>Email non envoyé car pas de serveur mail configuré</p>
                        <a href='$reset_link'>$reset_link</a>
                        <br><br>
                        <a href='mdp.php'>Retour</a>
                      </div>";
                exit();
            }
        } else {
            // Pour des raisons de sécurité, on dit quand même que l'email a été envoyé
            header('Location: mdp.php?success=1');
        }
        
    } catch(PDOException $e) {
        header('Location: mdp.php?error=Erreur technique, veuillez réessayer');
    }
    exit();
}
?>