<?php
require_once '../../backend/config/session.php';
require_once '../../backend/config/db.php';
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? '';
if ($role != 'moderateur' && $role != 'admin') {
    header('Location: /connectHub_rem/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modération - ConnectHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .moderation-layout{display:flex;gap:2rem;}.mod-sidebar{width:250px;background:white;border-radius:16px;padding:1rem;}.mod-sidebar nav ul{list-style:none;}.mod-sidebar nav li{margin-bottom:0.5rem;}.mod-sidebar nav a{display:flex;align-items:center;gap:0.5rem;padding:0.5rem;border-radius:12px;text-decoration:none;color:#25104D;}.mod-sidebar nav a:hover,.mod-sidebar nav a.active{background:#F7D9FC;color:#C2286B;}.mod-content{flex:1;}.stats-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-bottom:2rem;}.stat-card{background:white;border-radius:16px;padding:1rem;text-align:center;}.stat-number{font-size:2rem;font-weight:bold;color:#C2286B;}.report-item,.user-item{background:white;border-radius:12px;padding:1rem;margin-bottom:1rem;border-left:4px solid #C2286B;}.report-actions{margin-top:0.5rem;display:flex;gap:0.5rem;flex-wrap:wrap;}.modal{display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;z-index:1000;}.modal-content{background:white;border-radius:16px;padding:2rem;width:90%;max-width:500px;}.search-box{width:100%;padding:0.5rem;margin-bottom:1rem;border-radius:30px;border:1px solid #ccc;}.badge-mod{background:#C2286B;color:white;padding:2px 8px;border-radius:20px;font-size:0.7rem;margin-left:5px;}.badge-suspended{background:orange;color:white;padding:2px 8px;border-radius:20px;}.badge-banned{background:red;color:white;padding:2px 8px;border-radius:20px;}
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="container moderation-layout">
    <aside class="mod-sidebar">
        <nav><ul>
            <li><a href="#" data-tab="dashboard"><i class="fas fa-tachometer-alt"></i> Tableau de bord</a></li>
            <li><a href="#" data-tab="reports"><i class="fas fa-flag"></i> Signalements</a></li>
            <li><a href="#" data-tab="users"><i class="fas fa-users"></i> Utilisateurs</a></li>
            <li><a href="#" data-tab="history"><i class="fas fa-history"></i> Historique</a></li>
        </ul></nav>
    </aside>
    <div class="mod-content">
        <div id="dashboard-tab" class="tab-content active"><h2>Tableau de bord</h2><div class="stats-grid" id="stats-container"></div><h3>Derniers signalements</h3><div id="recent-reports"></div></div>
        <div id="reports-tab" class="tab-content" style="display:none;"><h2>Signalements en attente</h2><div id="reports-list"></div></div>
        <div id="users-tab" class="tab-content" style="display:none;"><h2>Rechercher un utilisateur</h2><input type="text" id="search-user" class="search-box" placeholder="Pseudo ou email..."><div id="user-search-results"></div></div>
        <div id="history-tab" class="tab-content" style="display:none;"><h2>Historique de modération</h2><div id="history-list"></div></div>
    </div>
</div>
<div id="actionModal" class="modal"><div class="modal-content"><h3 id="modalTitle">Action</h3><input type="hidden" id="modalReportId"><input type="hidden" id="modalUserId"><div class="form-group" id="reasonGroup"><label>Raison</label><textarea id="modalRaison" class="form-control" rows="3"></textarea></div><div class="form-group" id="durationGroup" style="display:none;"><label>Durée (jours)</label><input type="number" id="modalDuration" class="form-control" value="7"></div><div class="form-group" id="actionButtons"><button class="btn" id="modalConfirm">Confirmer</button><button class="btn btn-outline" id="modalCancel">Annuler</button></div></div></div>
<script src="../assets/js/moderation.js"></script>
<?php include '../includes/footer.php'; ?>
</body>
</html>