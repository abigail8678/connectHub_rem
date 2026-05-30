<?php
// traitement_connexion.php
session_start();
require_once 'config.php';

// Vérifier si le formulaire est soumis
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupérer et nettoyer les données
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $errors = [];
    
    // Validation
    if(empty($username)) {
        $errors[] = "Veuillez entrer votre pseudo ou email";
    }
    
    if(empty($password)) {
        $errors[] = "Veuillez entrer votre mot de passe";
    }
    
    // Si pas d'erreurs, vérifier dans la base de données
    if(empty($errors)) {
        try {
            // Chercher l'utilisateur par pseudo OU email
            $stmt = $pdo->prepare("
                SELECT id, pseudo, email, password_hash 
                FROM utilisateurs 
                WHERE pseudo = ? OR email = ?
            ");
            $stmt->execute([$username, $username]);
            $utilisateurs = $stmt->fetch();
            
            // Vérifier si l'utilisateur existe
            if($utilisateurs) {
                // Vérifier le mot de passe (en supposant qu'il est hashé avec password_hash)
                if(password_verify($password, $utilisateurs['password_hash'])) {
                    // Connexion réussie - créer la session
                    $_SESSION['user_id'] = $utilisateurs['id'];
                    $_SESSION['user_pseudo'] = $utilisateurs['pseudo'];
                    $_SESSION['user_email'] = $utilisateurs['email'];
                    $_SESSION['logged_in'] = true;
                    
                    // Rediriger vers la page de création de profil
                    header('Location: creationprofil.php');
                    exit();
                } else {
                    $errors[] = "Mot de passe incorrect";
                }
            } else {
                $errors[] = "Aucun compte trouvé avec ces identifiants";
            }
        } catch(PDOException $e) {
            $errors[] = "Erreur technique : " . $e->getMessage();
        }
    }
    
    // S'il y a des erreurs, rediriger avec le message
    if(!empty($errors)) {
        $error_string = implode(' ', $errors);
        header('Location: connexion.php?error=' . urlencode($error_string));
        exit();
    }
} else {
    // Si quelqu'un accède directement à ce fichier
    header('Location: connexion.php');
    exit();
}
?>