<?php
require_once '../../backend/config/session.php';
require_once '../../backend/config/db.php';
$user_id = $_SESSION['user_id'];

// Récupérer les publications mises en signet par l'utilisateur
$sql = "SELECT p.*, u.pseudo, c.nom as communaute_nom,
        (SELECT COUNT(*) FROM likes WHERE publication_id=p.id) as likes_count,
        (SELECT COUNT(*) FROM commentaires WHERE publication_id=p.id) as comments_count
        FROM signets s
        JOIN publications p ON s.publication_id = p.id
        JOIN utilisateurs u ON p.auteur_id = u.id
        LEFT JOIN communautes c ON p.communautes_id = c.id
        WHERE s.utilisateur_id = $user_id AND (p.est_supprime = 0 OR p.est_supprime IS NULL)
        ORDER BY s.created_at DESC";
$res = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes enregistrements - ConnectHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="feed">
    <h1>📌 Mes publications enregistrées</h1>
    <?php if (mysqli_num_rows($res) == 0): ?>
        <p>Aucune publication enregistrée. Cliquez sur "🔖 Enregistrer" sous une publication pour la retrouver ici.</p>
    <?php else: ?>
        <?php while($post = mysqli_fetch_assoc($res)): ?>
            <div class="post-card" data-id="<?= $post['id'] ?>">
                <div class="post-header">
                    <strong><?= htmlspecialchars($post['pseudo']) ?></strong>
                    <small><?= new DateTime($post['created_at'])->format('d/m/Y H:i:s') ?></small>
                    <?php if ($post['communautes_id']): ?>
                        <small>dans <a href="community.php?id=<?= $post['communautes_id'] ?>"><?= htmlspecialchars($post['communaute_nom']) ?></a></small>
                    <?php endif; ?>
                </div>
                <?php if ($post['titre']): ?>
                    <h3><?= htmlspecialchars($post['titre']) ?></h3>
                <?php endif; ?>
                <p><?= nl2br(htmlspecialchars($post['description'])) ?></p>
                <?php if ($post['fichier_media']): ?>
                    <div class="post-media">
                        <?php if ($post['type_media'] == 'image'): ?>
                            <img src="/connectHub_rem/uploads/<?= $post['fichier_media'] ?>" style="max-width:100%; border-radius:12px;">
                        <?php elseif ($post['type_media'] == 'video'): ?>
                            <video controls src="/connectHub_rem/uploads/<?= $post['fichier_media'] ?>" style="max-width:100%; border-radius:12px;"></video>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <?php if ($post['url']): ?>
                    <a href="<?= htmlspecialchars($post['url']) ?>" target="_blank"><?= htmlspecialchars($post['url']) ?></a>
                <?php endif; ?>
                <div class="post-actions">
                    <span>❤️ <?= $post['likes_count'] ?></span>
                    <span>💬 <?= $post['comments_count'] ?></span>
                    <button class="remove-bookmark" data-id="<?= $post['id'] ?>">❌ Retirer des enregistrements</button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>
<div class="right-sidebar">
    <div class="tendances">
        <h3>Astuce</h3>
        <p>Les publications que vous enregistrez apparaissent ici.</p>
    </div>
</div>
<script>
document.querySelectorAll('.remove-bookmark').forEach(btn => {
    btn.addEventListener('click', () => {
        let postId = btn.dataset.id;
        fetch('/connectHub_rem/backend/api/router.php?action=bookmark_post', {
            method: 'POST',
            body: `post_id=${postId}`
        }).then(() => location.reload());
    });
});
</script>
<?php include '../includes/footer.php'; ?>
</body>
</html>