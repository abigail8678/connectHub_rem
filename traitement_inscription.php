<?php
// traitement_inscription.php
require_once 'config.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Récupérer les données
    $nom = trim($_POST['name'] ?? '');
    $prenom = trim($_POST['surname'] ?? '');
    $date_naissance = $_POST['birth_date'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['number'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    $errors = [];
    
    // Validation
    if(empty($nom)) $errors[] = "Le nom est requis";
    if(empty($prenom)) $errors[] = "Le prénom est requis";
    if(empty($date_naissance)) $errors[] = "La date de naissance est requise";
    if(empty($genre)) $errors[] = "Le genre est requis";
    if(empty($email)) $errors[] = "L'email est requis";
    if(empty($telephone)) $errors[] = "Le numéro de téléphone est requis";
    if(empty($password)) $errors[] = "Le mot de passe est requis";
    if($password !== $confirm_password) $errors[] = "Les mots de passe ne correspondent pas";
    
    // Valider l'email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide";
    }
    
    // Valider le mot de passe (au moins 6 caractères)
    if(strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    // Vérifier si l'email existe déjà
    $checkStmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $checkStmt->execute([$email]);
    if($checkStmt->rowCount() > 0) {
        $errors[] = "Cet email est déjà utilisé";
    }
    
    // Si pas d'erreurs, insérer dans la base
    if(empty($errors)) {
        try {
            // Générer un pseudo par défaut
            $pseudo = $prenom . '_' . uniqid();
            
            // Hasher le mot de passe
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insérer l'utilisateur
            $stmt = $pdo->prepare("
                INSERT INTO utilisateurs (pseudo, email, password_hash, nom, prenom, date_naissance, genre, telephone) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $pseudo, $email, $password_hash, $nom, $prenom, $date_naissance, $genre, $telephone
            ]);
            
            // Rediriger vers la connexion
            header('Location: connexion.php?success=1');
            exit();
            
        } catch(PDOException $e) {
            $errors[] = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
    
    // S'il y a des erreurs
    if(!empty($errors)) {
        $error_string = implode(', ', $errors);
        header('Location: inscription.php?error=' . urlencode($error_string));
        exit();
    }
}
?>