<?php
require_once '../../backend/config/session.php';
require_once '../../backend/config/db.php';
$user_id = $_SESSION['user_id'];
$community_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$community_id) { header('Location: communities.php'); exit; }
$comm = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM communautes WHERE id=$community_id"));
if (!$comm) { header('Location: communities.php'); exit; }
$is_member = mysqli_num_rows(mysqli_query($conn, "SELECT 1 FROM membres_communautes WHERE communautes_id=$community_id AND utilisateur_id=$user_id")) > 0;
$is_admin = ($is_member && mysqli_fetch_assoc(mysqli_query($conn, "SELECT role FROM membres_communautes WHERE communautes_id=$community_id AND utilisateur_id=$user_id"))['role'] == 'admin');
if (!$is_member && $comm['type'] == 'fermee') { echo "Accès réservé aux membres."; exit; }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($comm['nom']) ?> - ConnectHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/connectHub_rem/frontend/assets/css/style.css">
    <style>
        .community-header { border-left: 5px solid <?= $comm['theme_couleur'] ?>; padding-left: 15px; margin-bottom: 1.5rem; }
        .post-type-selector { display: flex; gap: 10px; margin-bottom: 15px; flex-wrap: wrap; }
        .post-type-btn { background: #f0f0f0; border: none; padding: 8px 15px; border-radius: 20px; cursor: pointer; }
        .post-type-btn.active { background: <?= $comm['theme_couleur'] ?>; color: white; }
        .post-type-panel { display: none; }
        .post-type-panel.active { display: block; }
        .poll-option { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .remove-option { background: red; color: white; border: none; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; }
        .btn, .post-actions button:hover { background-color: <?= $comm['theme_couleur'] ?>; }
        .btn:hover { filter: brightness(0.9); }
        .edit-community { margin-bottom: 1rem; }
        .edit-form { background: white; padding: 1rem; border-radius: 16px; margin-bottom: 1rem; display: none; }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="feed">
    <div class="community-header">
        <h1><?= htmlspecialchars($comm['nom']) ?></h1>
        <p><?= nl2br(htmlspecialchars($comm['description'])) ?></p>
        <?php if ($is_member): ?>
            <button id="leaveCommunityBtn" class="btn btn-outline" data-id="<?= $community_id ?>">Quitter</button>
        <?php else: ?>
            <button id="joinCommunityBtn" class="btn" data-id="<?= $community_id ?>">Rejoindre</button>
        <?php endif; ?>
        <?php if ($is_admin): ?>
            <button id="editCommunityBtn" class="btn" style="margin-left:10px;">✏️ Modifier la communauté</button>
        <?php endif; ?>
    </div>

    <?php if ($is_admin): ?>
    <div id="editCommunityForm" class="edit-form">
        <h3>Modifier la communauté</h3>
        <form id="updateCommunityForm">
            <input type="hidden" name="community_id" value="<?= $community_id ?>">
            <div class="form-group"><label>Nom</label><input type="text" name="nom" value="<?= htmlspecialchars($comm['nom']) ?>" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars($comm['description']) ?></textarea></div>
            <div class="form-group"><label>Règles</label><textarea name="regles" rows="3"><?= htmlspecialchars($comm['regles']) ?></textarea></div>
            <div class="form-group"><label>Thème de couleur</label><select name="theme_couleur">
                <option value="#C2286B" <?= $comm['theme_couleur']=='#C2286B'?'selected':'' ?>>Rose</option>
                <option value="#FF6B6B" <?= $comm['theme_couleur']=='#FF6B6B'?'selected':'' ?>>Rouge</option>
                <option value="#4ECDC4" <?= $comm['theme_couleur']=='#4ECDC4'?'selected':'' ?>>Turquoise</option>
                <option value="#FFE66D" <?= $comm['theme_couleur']=='#FFE66D'?'selected':'' ?>>Jaune</option>
                <option value="#1A535C" <?= $comm['theme_couleur']=='#1A535C'?'selected':'' ?>>Bleu</option>
                <option value="#F7FFF7" <?= $comm['theme_couleur']=='#F7FFF7'?'selected':'' ?>>Blanc</option>
                <option value="#343434" <?= $comm['theme_couleur']=='#343434'?'selected':'' ?>>Graphite</option>
            </select></div>
            <button type="submit" class="btn">Enregistrer</button>
        </form>
    </div>
    <?php endif; ?>

    <?php if ($is_member): ?>
    <div class="create-post">
        <h3>Publier dans la communauté</h3>
        <div class="post-type-selector">
            <button type="button" class="post-type-btn active" data-type="text">📝 Texte</button>
            <button type="button" class="post-type-btn" data-type="media">🖼️ Image/Vidéo</button>
            <button type="button" class="post-type-btn" data-type="link">🔗 Lien</button>
            <button type="button" class="post-type-btn" data-type="poll">📊 Sondage</button>
        </div>
        <form id="postForm" enctype="multipart/form-data">
            <input type="hidden" name="community_id" value="<?= $community_id ?>">
            <input type="hidden" name="post_type" id="post_type" value="text">
            <div id="text-panel" class="post-type-panel active">
                <div class="form-group"><label for="titre">Titre (optionnel)</label><input type="text" name="titre" id="titre" class="form-control"></div>
                <div class="form-group"><label for="description">Votre texte</label><textarea name="description" id="description" class="form-control" rows="5"></textarea></div>
            </div>
            <div id="media-panel" class="post-type-panel">
                <div class="form-group"><label for="titre_media">Titre (optionnel)</label><input type="text" name="titre_media" id="titre_media" class="form-control"></div>
                <div class="form-group"><label for="desc_media">Description</label><textarea name="desc_media" id="desc_media" class="form-control" rows="3"></textarea></div>
                <div class="form-group"><label for="media">Fichier (image ou vidéo)</label><input type="file" name="media" id="media" accept="image/*,video/*"></div>
            </div>
            <div id="link-panel" class="post-type-panel">
                <div class="form-group"><label for="titre_link">Titre du lien</label><input type="text" name="titre_link" id="titre_link" class="form-control"></div>
                <div class="form-group"><label for="url">URL du lien</label><input type="url" name="url" id="url" placeholder="https://..." class="form-control"></div>
                <div class="form-group"><label for="desc_link">Description (optionnelle)</label><textarea name="desc_link" id="desc_link" class="form-control" rows="3"></textarea></div>
            </div>
            <div id="poll-panel" class="post-type-panel">
                <div class="form-group"><label for="poll_question">Question du sondage</label><input type="text" name="poll_question" id="poll_question" class="form-control"></div>
                <div id="poll-options-container">
                    <div class="poll-option"><input type="text" name="poll_options[]" placeholder="Option 1"><button type="button" class="remove-option">✖</button></div>
                    <div class="poll-option"><input type="text" name="poll_options[]" placeholder="Option 2"><button type="button" class="remove-option">✖</button></div>
                </div>
                <button type="button" id="add-poll-option" class="btn-sm">+ Ajouter une option</button>
                <div class="form-group"><label for="poll_duration">Durée du sondage</label><select name="poll_duration" id="poll_duration"><option value="1">1 jour</option><option value="3">3 jours</option><option value="7">7 jours</option><option value="30">30 jours</option></select></div>
            </div>
            <button type="submit" class="btn">Publier</button>
        </form>
    </div>
    <?php endif; ?>

    <div id="communityFeed"><p>Chargement...</p></div>
</div>
<div class="right-sidebar">
    <div class="tendances"><h3>À propos</h3><p><?= nl2br(htmlspecialchars($comm['regles'] ?? 'Aucune règle définie')) ?></p></div>
</div>
<script>
window.currentUserId = <?= json_encode($user_id) ?>;
window.communityId = <?= json_encode($community_id) ?>;
window.isAdmin = <?= json_encode($is_admin) ?>;
</script>
<script src="/connectHub_rem/frontend/assets/js/community.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>