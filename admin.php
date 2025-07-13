<?php  
/**  
 * Admin-Seite - Nur mit HTML, PHP und JS  
 */  
  
require_once 'functions.php';  
  
// Session starten falls noch nicht gestartet  
if (session_status() == PHP_SESSION_NONE) {  
    session_start();  
}  
  
// Admin-Passwort (ändere dieses!)  
define('ADMIN_PASSWORD', 'admin123');  
  
$error_message = "";  
$is_admin_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;  
  
// Login-Verarbeitung  
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['admin_login'])) {  
    $entered_password = $_POST['admin_password'];  
      
    if ($entered_password === ADMIN_PASSWORD) {  
        $_SESSION['admin_logged_in'] = true;  
        $is_admin_logged_in = true;  
    } else {  
        $error_message = "Falsches Passwort!";  
    }  
}  
  
// Logout-Verarbeitung  
if (isset($_GET['logout'])) {  
    unset($_SESSION['admin_logged_in']);  
    $is_admin_logged_in = false;  
}  
  
// AJAX-Handler für Benutzer löschen  
if (isset($_POST['delete_user']) && $is_admin_logged_in) {  
    $user_id = intval($_POST['user_id']);  
    $conn = connect();  
    $result = mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");  
    mysqli_close($conn);  
    echo json_encode(['success' => $result]);  
    exit;  
}  
  
// AJAX-Handler für Chips hinzufügen  
if (isset($_POST['add_chips']) && $is_admin_logged_in) {  
    $user_id = intval($_POST['user_id']);  
    $chips = intval($_POST['chips']);  
    $conn = connect();  
    $result = mysqli_query($conn, "UPDATE users SET chips = chips + $chips WHERE id = $user_id");  
    mysqli_close($conn);  
    echo json_encode(['success' => $result]);  
    exit;  
}  
  
// Wenn nicht eingeloggt, zeige Login-Formular  
if (!$is_admin_logged_in) {  
?>  
<!DOCTYPE html>  
<html lang="de">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Admin Login</title>  
    <style>  
        body {  
            font-family: Arial, sans-serif;  
            background: linear-gradient(135deg, #1e3c72, #2a5298);  
            margin: 0;  
            padding: 0;  
            min-height: 100vh;  
            display: flex;  
            justify-content: center;  
            align-items: center;  
        }  
        .login-container {  
            background: white;  
            padding: 40px;  
            border-radius: 10px;  
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);  
            width: 100%;  
            max-width: 400px;  
            text-align: center;  
        }  
        .login-container h2 {  
            color: #333;  
            margin-bottom: 30px;  
        }  
        .form-group {  
            margin-bottom: 20px;  
            text-align: left;  
        }  
        .form-group label {  
            display: block;  
            margin-bottom: 5px;  
            color: #555;  
            font-weight: bold;  
        }  
        .form-group input {  
            width: 100%;  
            padding: 12px;  
            border: 2px solid #ddd;  
            border-radius: 5px;  
            font-size: 16px;  
            box-sizing: border-box;  
        }  
        .form-group input:focus {  
            border-color: #2a5298;  
            outline: none;  
        }  
        .btn {  
            background: #2a5298;  
            color: white;  
            padding: 12px 30px;  
            border: none;  
            border-radius: 5px;  
            cursor: pointer;  
            font-size: 16px;  
            width: 100%;  
        }  
        .btn:hover {  
            background: #1e3c72;  
        }  
        .error {  
            color: red;  
            margin-top: 15px;  
            padding: 10px;  
            background: #ffe6e6;  
            border-radius: 5px;  
        }  
    </style>  
</head>  
<body>  
    <div class="login-container">  
        <h2>🔐 Admin-Bereich</h2>  
        <form method="POST">  
            <div class="form-group">  
                <label for="admin_password">Passwort:</label>  
                <input type="password" id="admin_password" name="admin_password" required>  
            </div>  
            <button type="submit" name="admin_login" class="btn">Anmelden</button>  
        </form>  
        <?php if ($error_message): ?>  
            <div class="error"><?php echo $error_message; ?></div>  
        <?php endif; ?>  
    </div>  
</body>  
</html>  
<?php  
    exit;  
}  
  
// Ab hier: Admin ist eingeloggt - Dashboard anzeigen  
$conn = connect();  
  
// Statistiken sammeln  
$stats = [];  
  
// Benutzer-Statistiken  
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");  
$stats['total_users'] = mysqli_fetch_assoc($result)['total'];  
  
$result = mysqli_query($conn, "SELECT COUNT(*) as active FROM users WHERE last_login > DATE_SUB(NOW(), INTERVAL 24 HOUR)");  
$stats['active_users'] = mysqli_fetch_assoc($result)['active'] ?? 0;  
  
$result = mysqli_query($conn, "SELECT SUM(chips) as total_chips FROM users");  
$stats['total_chips'] = mysqli_fetch_assoc($result)['total_chips'] ?? 0;  
  
// Spiel-Statistiken  
$games = ['blackjack', 'plinko', 'slots', 'mines'];  
$stats['games'] = [];  
foreach ($games as $game) {  
    $table_exists = mysqli_query($conn, "SHOW TABLES LIKE '{$game}_games'");  
    if (mysqli_num_rows($table_exists) > 0) {  
        $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM {$game}_games");  
        $stats['games'][$game] = mysqli_fetch_assoc($result)['count'];  
    } else {  
        $stats['games'][$game] = 0;  
    }  
}  
?>  
<!DOCTYPE html>  
<html lang="de">  
<head>  
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <title>Admin Dashboard</title>  
    <style>  
        * {  
            margin: 0;  
            padding: 0;  
            box-sizing: border-box;  
        }  
        body {  
            font-family: Arial, sans-serif;  
            background: linear-gradient(135deg, #1e3c72, #2a5298);  
            color: #333;  
            min-height: 100vh;  
        }  
        .container {  
            max-width: 1200px;  
            margin: 0 auto;  
            padding: 20px;  
        }  
        .header {  
            background: white;  
            padding: 20px;  
            border-radius: 10px;  
            margin-bottom: 20px;  
            display: flex;  
            justify-content: space-between;  
            align-items: center;  
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);  
        }  
        .header h1 {  
            color: #2a5298;  
        }  
        .logout-btn {  
            background: #dc3545;  
            color: white;  
            padding: 10px 20px;  
            text-decoration: none;  
            border-radius: 5px;  
            transition: background 0.3s;  
        }  
        .logout-btn:hover {  
            background: #c82333;  
        }  
        .stats-grid {  
            display: grid;  
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));  
            gap: 20px;  
            margin-bottom: 30px;  
        }  
        .stat-card {  
            background: white;  
            padding: 20px;  
            border-radius: 10px;  
            text-align: center;  
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);  
        }  
        .stat-card h3 {  
            color: #2a5298;  
            margin-bottom: 10px;  
        }  
        .stat-number {  
            font-size: 2em;  
            font-weight: bold;  
            color: #1e3c72;  
        }  
        .users-section {  
            background: white;  
            padding: 20px;  
            border-radius: 10px;  
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);  
        }  
        .users-section h2 {  
            color: #2a5298;  
            margin-bottom: 20px;  
        }  
        .users-table {  
            width: 100%;  
            border-collapse: collapse;  
            margin-top: 10px;  
        }  
        .users-table th,  
        .users-table td {  
            padding: 12px;  
            text-align: left;  
            border-bottom: 1px solid #ddd;  
        }  
        .users-table th {  
            background: #f8f9fa;  
            font-weight: bold;  
            color: #2a5298;  
        }  
        .users-table tr:hover {  
            background: #f8f9fa;  
        }  
        .btn {  
            padding: 5px 10px;  
            border: none;  
            border-radius: 3px;  
            cursor: pointer;  
            margin: 0 2px;  
            font-size: 12px;  
        }  
        .btn-danger {  
            background: #dc3545;  
            color: white;  
        }  
        .btn-success {  
            background: #28a745;  
            color: white;  
        }  
        .btn:hover {  
            opacity: 0.8;  
        }  
        .modal {  
            display: none;  
            position: fixed;  
            z-index: 1000;  
            left: 0;  
            top: 0;  
            width: 100%;  
            height: 100%;  
            background: rgba(0,0,0,0.5);  
        }  
        .modal-content {  
            background: white;  
            margin: 15% auto;  
            padding: 20px;  
            border-radius: 10px;  
            width: 300px;  
            text-align: center;  
        }  
        .close {  
            color: #aaa;  
            float: right;  
            font-size: 28px;  
            font-weight: bold;  
            cursor: pointer;  
        }  
        .close:hover {  
            color: black;  
        }  
        .form-group {  
            margin: 15px 0;  
            text-align: left;  
        }  
        .form-group label {  
            display: block;  
            margin-bottom: 5px;  
            font-weight: bold;  
        }  
        .form-group input {  
            width: 100%;  
            padding: 8px;  
            border: 1px solid #ddd;  
            border-radius: 3px;  
        }  
    </style>  
</head>  
<body>  
    <div class="container">  
        <div class="header">  
            <h1>🎰 Casino Admin Dashboard</h1>  
            <a href="?logout=1" class="logout-btn">Abmelden</a>  
        </div>  
  
        <div class="stats-grid">  
            <div class="stat-card">  
                <h3>👥 Gesamte Benutzer</h3>  
                <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>  
            </div>  
            <div class="stat-card">  
                <h3>🟢 Aktive Benutzer (24h)</h3>  
                <div class="stat-number"><?php echo number_format($stats['active_users']); ?></div>  
            </div>  
            <div class="stat-card">  
                <h3>🪙 Gesamte Chips</h3>  
                <div class="stat-number"><?php echo number_format($stats['total_chips']); ?></div>  
            </div>  
            <div class="stat-card">  
                <h3>🎮 Gespielte Spiele</h3>  
                <div class="stat-number"><?php echo number_format(array_sum($stats['games'])); ?></div>  
            </div>  
        </div>  
  
        <div class="stats-grid">  
            <?php foreach ($stats['games'] as $game => $count): ?>  
            <div class="stat-card">  
                <h3><?php echo ucfirst($game); ?></h3>  
                <div class="stat-number"><?php echo number_format($count); ?></div>  
            </div>  
            <?php endforeach; ?>  
        </div>  
  
        <div class="users-section">  
            <h2>👤 Benutzer verwalten</h2>  
            <table class="users-table">  
                <thead>  
                    <tr>  
                        <th>ID</th>  
                        <th>Benutzername</th>  
                        <th>E-Mail</th>  
                        <th>Chips</th>  
                        <th>Registriert</th>  
                        <th>Letzter Login</th>  
                        <th>Aktionen</th>  
                    </tr>  
                </thead>  
                <tbody>  
                    <?php  
                    $result = mysqli_query($conn, "SELECT id, username, email, chips, created_at, last_login FROM users ORDER BY created_at DESC");  
                    while ($row = mysqli_fetch_assoc($result)):  
                        $created = date('d.m.Y', strtotime($row['created_at']));  
                        $last_login = $row['last_login'] ? date('d.m.Y H:i', strtotime($row['last_login'])) : 'Nie';  
                    ?>  
                    <tr>  
                        <td><?php echo $row['id']; ?></td>  
                        <td><?php echo htmlspecialchars($row['username']); ?></td>  
                        <td><?php echo htmlspecialchars($row['email']); ?></td>  
                        <td><?php echo number_format($row['chips']); ?></td>  
                        <td><?php echo $created; ?></td>  
                        <td><?php echo $last_login; ?></td>  
                        <td>  
                            <button class="btn btn-success" onclick="addChips(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')">+ Chips</button>  
                            <button class="btn btn-danger" onclick="deleteUser(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['username']); ?>')">Löschen</button>  
                        </td>  
                    </tr>  
                    <?php endwhile; ?>  
                </tbody>  
            </table>  
        </div>  
    </div>  
  
    <!-- Modal für Chips hinzufügen -->  
    <div id="chipsModal" class="modal">  
        <div class="modal-content">  
            <span class="close" onclick="closeModal('chipsModal')">&times;</span>  
            <h3>Chips hinzufügen</h3>  
            <div class="form-group">  
                <label>Benutzer:</label>  
                <input type="text" id="chipsUsername" readonly>  
            </div>  
            <div class="form-group">  
                <label>Chips hinzufügen:</label>  
                <input type="number" id="chipsAmount" min="1" value="1000">  
            </div>  
            <button class="btn btn-success" onclick="confirmAddChips()">Hinzufügen</button>  
            <button class="btn" onclick="closeModal('chipsModal')">Abbrechen</button>  
        </div>  
    </div>  
  
    <!-- Modal für Benutzer löschen -->  
    <div id="deleteModal" class="modal">  
        <div class="modal-content">  
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>  
            <h3>Benutzer löschen</h3>  
            <p>Möchtest du den Benutzer <strong id="deleteUsername"></strong> wirklich löschen?</p>  
            <button class="btn btn-danger" onclick="confirmDelete()">Ja, löschen</button>  
            <button class="btn" onclick="closeModal('deleteModal')">Abbrechen</button>  
        </div>  
    </div>  
  
    <script>  
        let currentUserId = null;  
  
        function addChips(userId, username) {  
            currentUserId = userId;  
            document.getElementById('chipsUsername').value = username;  
            document.getElementById('chipsAmount').value = 1000;  
            document.getElementById('chipsModal').style.display = 'block';  
        }  
  
        function deleteUser(userId, username) {  
            currentUserId = userId;  
            document.getElementById('deleteUsername').textContent = username;  
            document.getElementById('deleteModal').style.display = 'block';  
        }  
  
        function closeModal(modalId) {  
            document.getElementById(modalId).style.display = 'none';  
            currentUserId = null;  
        }  
  
        function confirmAddChips() {  
            const chips = document.getElementById('chipsAmount').value;  
              
            fetch('', {  
                method: 'POST',  
                headers: {  
                    'Content-Type': 'application/x-www-form-urlencoded',  
                },  
                body: `add_chips=1&user_id=${currentUserId}&chips=${chips}`  
            })  
            .then(response => response.json())  
            .then(data => {  
                if (data.success) {  
                    alert('Chips erfolgreich hinzugefügt!');  
                    location.reload();  
                } else {  
                    alert('Fehler beim Hinzufügen der Chips!');  
                }  
            });  
              
            closeModal('chipsModal');  
        }  
  
        function confirmDelete() {  
            fetch('', {  
                method: 'POST',  
                headers: {  
                    'Content-Type': 'application/x-www-form-urlencoded',  
                },  
                body: `delete_user=1&user_id=${currentUserId}`  
            })  
            .then(response => response.json())  
            .then(data => {  
                if (data.success) {  
                    alert('Benutzer erfolgreich gelöscht!');  
                    location.reload();  
                } else {  
                    alert('Fehler beim Löschen des Benutzers!');  
                }  
            });  
              
            closeModal('deleteModal');  
        }  
  
        // Modal schließen wenn außerhalb geklickt wird  
        window.onclick = function(event) {  
            const chipsModal = document.getElementById('chipsModal');  
            const deleteModal = document.getElementById('deleteModal');  
              
            if (event.target == chipsModal) {  
                closeModal('chipsModal');  
            }  
            if (event.target == deleteModal) {  
                closeModal('deleteModal');  
            }  
        }  
    </script>  
</body>  
</html>  
<?php  
mysqli_close($conn);  
?>