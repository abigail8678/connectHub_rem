<?php require_once '../../backend/config/session.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications - ConnectHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/connectHub_rem/frontend/assets/css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="feed">
    <h1>Notifications</h1>
    <div id="notifications-list">Chargement...</div>
</div>
<div class="right-sidebar">
    <div class="tendances"><h3>Filtres</h3><div class="hashtag filter-notif" data-filter="all">📋 Toutes</div><div class="hashtag filter-notif" data-filter="message">💬 Messages</div><div class="hashtag filter-notif" data-filter="demande_message">📨 Demandes</div><div class="hashtag filter-notif" data-filter="like">❤️ Likes</div><div class="hashtag filter-notif" data-filter="commentaire">💭 Commentaires</div></div>
</div>
<script>
document.querySelectorAll('.filter-notif').forEach(btn => {
    btn.addEventListener('click', () => {
        const filter = btn.dataset.filter;
        document.querySelectorAll('.notif-item').forEach(notif => {
            notif.style.display = (filter === 'all' || notif.dataset.type === filter) ? 'block' : 'none';
        });
    });
});
</script>
<script src="/connectHub_rem/frontend/assets/js/notifications.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>