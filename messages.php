<?php require_once '../../backend/config/session.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie - ConnectHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/connectHub_rem/frontend/assets/css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="feed">
    <h1>Messagerie</h1>
    <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
        <button id="newMsgBtn" class="btn">✉️ Nouveau message</button>
        <button id="newGroupBtn" class="btn">👥 Nouveau groupe</button>
    </div>
    <div class="messages-layout">
        <div class="conversations-list"><h3>Conversations</h3><div id="conversations">Chargement...</div></div>
        <div class="messages-area">
            <div id="current-conversation-header"></div>
            <div id="messages-list"></div>
            <div id="send-message-area" style="display:none;">
                <div class="search-wrapper"><input type="text" id="search-messages" placeholder="🔍 Rechercher dans les messages..." class="search-box"></div>
                <textarea id="message-text" placeholder="Votre message..."></textarea>
                <button id="sendMsgBtn" class="btn">Envoyer</button>
            </div>
        </div>
    </div>
</div>
<div class="right-sidebar">
    <div class="recommandations"><h3>Suggestions</h3><div class="compte-reco"><div class="reco-avatar">A</div><div class="reco-info"><div class="reco-nom">Alice</div><div class="reco-pseudo">@alice</div></div><button class="btn-sm btn-outline">Envoyer</button></div></div>
</div>
<div id="newMsgModal" class="modal">
    <div class="modal-content"><span class="close">&times;</span><h2 id="modalTitle">Nouveau message</h2><input type="text" id="searchUser" placeholder="Chercher un utilisateur..." class="form-control"><div id="searchResults"></div><div id="groupMembers" style="display:none;"><h3>Membres du groupe</h3><div id="selectedMembers"></div><input type="text" id="groupName" placeholder="Nom du groupe" class="form-control"><button id="createGroupConfirm" class="btn">Créer le groupe</button></div></div>
</div>
<script>const user_id = <?= json_encode($_SESSION['user_id']) ?>;</script>
<script src="/connectHub_rem/frontend/assets/js/messages.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>