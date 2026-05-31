<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: /connectHub_rem/login.php'); exit; }
$user_id = $_SESSION['user_id'];
require_once '../../backend/config/db.php';

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT pseudo, photo_profil FROM utilisateurs WHERE id=$user_id"));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Communautés - ConnectHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="feed">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h1>Communautés</h1>
        <button id="createCommunityBtn" class="btn">+ Créer</button>
    </div>

    <h2>Mes communautés</h2>
    <div class="communities-grid" id="mesCommunautes"></div>

    <h2 style="margin-top:2rem;">Découvrir</h2>
    <div class="communities-grid" id="autresCommunautes"></div>
</div>

<div class="right-sidebar">
    <div class="tendances"><h3>Tendances</h3><div class="hashtag">#Art</div><div class="hashtag">#Peinture</div></div>
</div>

<!-- Modal création -->
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
                    <?php
                    $disc = mysqli_query($conn, "SELECT * FROM disciplines");
                    while($d = mysqli_fetch_assoc($disc)) echo "<option value='{$d['id']}'>{$d['nom']}</option>";
                    ?>
                </select>
            </div>
            <div class="form-group"><label>Type</label><select name="type"><option value="ouverte">Ouverte</option><option value="fermee">Fermée</option></select></div>
            <div class="form-group"><label>Thème de couleur</label><select name="theme_couleur">
                <option value="#C2286B">Rose</option><option value="#FF6B6B">Rouge</option><option value="#4ECDC4">Turquoise</option>
                <option value="#FFE66D">Jaune</option><option value="#1A535C">Bleu</option><option value="#F7FFF7">Blanc</option><option value="#343434">Graphite</option>
            </select></div>
            <button type="submit" class="btn">Créer</button>
        </form>
    </div>
</div>

<script>
const currentUserId = <?= json_encode($user_id) ?>;

function chargerCommunautes() {
    fetch('/connectHub_rem/backend/api/router.php?action=get_all_communities')
        .then(r => r.json())
        .then(data => {
            let mesHtml = '', autresHtml = '';
            data.mesCommunautes.forEach(c => {
                mesHtml += `
                    <div class="community-card">
                        <a href="community.php?id=${c.id}" style="text-decoration:none; color:inherit;">
                            <h3>${escapeHtml(c.nom)}</h3>
                            <p>${escapeHtml(c.description)}</p>
                        </a>
                        <div class="meta"><span class="role-badge">${c.role}</span></div>
                        <div class="card-actions">
                            ${c.role === 'admin' ? 
                                `<button class="btn-sm btn-delete" data-id="${c.id}">🗑️ Supprimer</button>` :
                                `<button class="btn-sm btn-leave" data-id="${c.id}">Quitter</button>`
                            }
                        </div>
                    </div>
                `;
            });
            data.autresCommunautes.forEach(c => {
                autresHtml += `
                    <div class="community-card">
                        <a href="community.php?id=${c.id}" style="text-decoration:none; color:inherit;">
                            <h3>${escapeHtml(c.nom)}</h3>
                            <p>${escapeHtml(c.description)}</p>
                        </a>
                        <div class="meta">${c.nb_membres} membre(s)</div>
                        <div class="card-actions">
                            <button class="btn-sm btn-join" data-id="${c.id}">Rejoindre</button>
                        </div>
                    </div>
                `;
            });
            document.getElementById('mesCommunautes').innerHTML = mesHtml || '<p>Aucune communauté.</p>';
            document.getElementById('autresCommunautes').innerHTML = autresHtml || '<p>Aucune autre communauté.</p>';
            attacherEvents();
        });
}

function attacherEvents() {
    document.querySelectorAll('.btn-delete').forEach(btn => btn.onclick = () => {
        if(confirm('Supprimer définitivement cette communauté ?')) {
            fetch('/connectHub_rem/backend/api/router.php?action=delete_community', {method:'POST', body:`community_id=${btn.dataset.id}`})
                .then(r=>r.json()).then(data=>{if(data.success) location.reload(); else alert(data.error);});
        }
    });
    document.querySelectorAll('.btn-leave').forEach(btn => btn.onclick = () => {
        if(confirm('Quitter cette communauté ?')) {
            fetch('/connectHub_rem/backend/api/router.php?action=leave_community', {method:'POST', body:`community_id=${btn.dataset.id}`})
                .then(r=>r.json()).then(data=>{if(data.success) location.reload(); else alert(data.error);});
        }
    });
    document.querySelectorAll('.btn-join').forEach(btn => btn.onclick = () => {
        fetch('/connectHub_rem/backend/api/router.php?action=join_community', {method:'POST', body:`community_id=${btn.dataset.id}`})
            .then(r=>r.json()).then(data=>{if(data.success) location.reload(); else alert(data.error);});
    });
}

// Gestion modal création
const modal = document.getElementById('createModal');
document.getElementById('createCommunityBtn').onclick = () => modal.style.display = 'flex';
document.querySelector('#createModal .close').onclick = () => modal.style.display = 'none';
window.onclick = e => { if(e.target === modal) modal.style.display = 'none'; };
document.getElementById('createCommunityForm').onsubmit = async e => {
    e.preventDefault();
    let fd = new FormData(e.target);
    let res = await fetch('/connectHub_rem/backend/api/router.php?action=create_community', {method:'POST', body:fd});
    let data = await res.json();
    if(data.success) location.reload();
    else alert(data.error);
};

function escapeHtml(s) { return s ? s.replace(/[&<>]/g, m=>m==='&'?'&amp;':m==='<'?'&lt;':'&gt;') : ''; }

chargerCommunautes();
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>