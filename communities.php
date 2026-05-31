<?php
require_once '../../backend/config/session.php';
require_once '../../backend/config/db.php';
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Communautés - ConnectHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/connectHub_rem/frontend/assets/css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="feed">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h1>Communautés</h1>
        <button id="createCommunityBtn" class="btn">+ Créer</button>
    </div>
    <h2>Mes communautés</h2>
    <div class="communities-grid">
        <?php 
        $mes = mysqli_query($conn, "SELECT c.*, mc.role FROM communautes c JOIN membres_communautes mc ON c.id=mc.communautes_id WHERE mc.utilisateur_id=$user_id");
        while($c = mysqli_fetch_assoc($mes)): ?>
        <div class="card community-card">
            <a href="community.php?id=<?= $c['id'] ?>" style="text-decoration:none; color:inherit;">
                <h3><?= htmlspecialchars($c['nom']) ?></h3>
                <p><?= htmlspecialchars($c['description']) ?></p>
            </a>
            <span class="role-badge"><?= $c['role'] ?></span>
            <?php if ($c['role'] == 'admin'): ?>
                <button class="btn-delete-community btn-sm" data-id="<?= $c['id'] ?>" style="margin-left:10px; background:#dc3545;">🗑️ Supprimer</button>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($mes) == 0): ?>
            <p>Aucune communauté rejointes.</p>
        <?php endif; ?>
    </div>
    <h2 style="margin-top:2rem;">Découvrir</h2>
    <div class="communities-grid">
        <?php 
        $all = mysqli_query($conn, "SELECT c.*, (SELECT COUNT(*) FROM membres_communautes WHERE communautes_id=c.id) as nb FROM communautes c ORDER BY c.created_at DESC");
        while($c = mysqli_fetch_assoc($all)):
            $estMembre = mysqli_num_rows(mysqli_query($conn, "SELECT 1 FROM membres_communautes WHERE communautes_id={$c['id']} AND utilisateur_id=$user_id")) > 0;
        ?>
        <div class="card community-card">
            <a href="community.php?id=<?= $c['id'] ?>" style="text-decoration:none; color:inherit;">
                <h3><?= htmlspecialchars($c['nom']) ?></h3>
                <p><?= htmlspecialchars($c['description']) ?></p>
            </a>
            <span><?= $c['nb'] ?> membres</span>
            <?php if(!$estMembre): ?>
                <button class="btn-join btn-sm" data-id="<?=$c['id']?>">Rejoindre</button>
            <?php else: ?>
                <span class="member-badge">✓ Membre</span>
                <button class="btn-leave btn-sm" data-id="<?=$c['id']?>">Quitter</button>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
</div>
<div class="right-sidebar">
    <div class="tendances"><h3>Tendances</h3><div class="hashtag">#Art</div><div class="hashtag">#Peinture</div></div>
</div>
<div id="createModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Créer une communauté</h2>
        <form id="createCommunityForm">
            <div class="form-group"><label>Nom</label><input type="text" name="nom" required></div>
            <div class="form-group"><label>Description</label><textarea name="description"></textarea></div>
            <div class="form-group">
                <label>Discipline</label>
                <select name="discipline_id">
                    <?php $disc = mysqli_query($conn, "SELECT * FROM disciplines"); while($d = mysqli_fetch_assoc($disc)): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nom']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group"><label>Type</label><select name="type"><option value="ouverte">Ouverte</option><option value="fermee">Fermée (invitation)</option></select></div>
            <div class="form-group"><label>Thème de couleur</label><select name="theme_couleur"><option value="#C2286B">Rose</option><option value="#FF6B6B">Rouge</option><option value="#4ECDC4">Turquoise</option><option value="#FFE66D">Jaune</option><option value="#1A535C">Bleu</option><option value="#F7FFF7">Blanc</option><option value="#343434">Graphite</option></select></div>
            <button type="submit" class="btn">Créer</button>
        </form>
    </div>
</div>
<script src="/connectHub_rem/frontend/assets/js/communities.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>