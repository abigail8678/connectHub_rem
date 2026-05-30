<?php
// traitement_profil.php
session_start();
require_once 'config.php';

// Vérifier si l'utilisateur est connecté (à adapter selon votre système)
if(!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];

// Vérifier que le formulaire est soumis
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Récupérer et valider les données
    $pseudo = trim($_POST['username'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $centre_interet = trim($_POST['centre'] ?? '');
    
    if(empty($pseudo)) {
        $errors[] = "Le pseudo est requis";
    }
    
    if(empty($description)) {
        $errors[] = "La description est requise";
    }
    
    if(empty($centre_interet)) {
        $errors[] = "Le centre d'intérêt est requis";
    }
    
    // Vérifier si le pseudo existe déjà (pour un autre utilisateur)
    $checkStmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE pseudo = ? AND id != ?");
    $checkStmt->execute([$pseudo, $user_id]);
    if($checkStmt->rowCount() > 0) {
        $errors[] = "Ce pseudo est déjà utilisé";
    }
    
    // 2. Gérer l'upload de l'avatar
    $avatar_url = null;
    if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/avatars/';
        
        // Créer le dossier s'il n'existe pas
        if(!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if(in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $destination = $upload_dir . $new_filename;
            
            if(move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                $avatar_url = $destination;
            } else {
                $errors[] = "Erreur lors de l'upload de l'image";
            }
        } else {
            $errors[] = "Format d'image non autorisé (JPG, PNG, GIF, WEBP)";
        }
    }
    
    // 3. Récupérer l'ID du centre d'intérêt
    $interestStmt = $pdo->prepare("SELECT id FROM interets WHERE name = ?");
    $interestStmt->execute([ucfirst($centre_interet)]);
    $interet = $interestStmt->fetch();
    $interet_id = $interet ? $interet['id'] : null;
    
    // 4. Mettre à jour ou insérer dans la base de données
    if(empty($errors)) {
        try {
            // Démarrer une transaction
            $pdo->beginTransaction();
            
            // Mettre à jour le pseudo dans la table utilisateurs
            $updateUser = $pdo->prepare("UPDATE utilisateurs SET pseudo = ? WHERE id = ?");
            $updateUser->execute([$pseudo, $user_id]);
            
            // Vérifier si le profil existe déjà
            $checkProfile = $pdo->prepare("SELECT user_id FROM profil_utilisateurs WHERE user_id = ?");
            $checkProfile->execute([$user_id]);
            
            if($checkProfile->rowCount() > 0) {
                // Mettre à jour le profil existant
                $updateProfile = $pdo->prepare("
                    UPDATE profil_utilisateurs 
                    SET description = ?, avatar_url = ?, centre_interet_principal = ? 
                    WHERE user_id = ?
                ");
                $updateProfile->execute([$description, $avatar_url, $interet_id, $user_id]);
            } else {
                // Créer un nouveau profil
                $insertProfile = $pdo->prepare("
                    INSERT INTO profil_utilisateurs (user_id, description, avatar_url, centre_interet_principal) 
                    VALUES (?, ?, ?, ?)
                ");
                $insertProfile->execute([$user_id, $description, $avatar_url, $interet_id]);
            }
            
            // Valider la transaction
            $pdo->commit();
            
            // Rediriger avec succès
            header('Location: creationprofil.php?success=1');
            exit();
            
        } catch(PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Erreur base de données : " . $e->getMessage();
        }
    }
    
    // S'il y a des erreurs, rediriger avec les messages
    if(!empty($errors)) {
        $error_string = implode(', ', $errors);
        header('Location: creationprofil.php?error=' . urlencode($error_string));
        exit();
    }
}
?>