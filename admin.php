
<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
require 'config.php';
// Tabelle wählen
$table = $_GET['table'] ?? null;
?>
<h1>Admin Panel</h1>
<a href="logout.php">Logout</>
<hr>

<h2>Tabellen:</h2>
<ul>
    <?php$tables = $pdo->query("SHOW TABLES")->fetchALL(PDO::FETCH_COLUMN);
    foreach($tables as $t) {
            echo"<li><a href='?table=$t'>$t</a></li>";
    }
    ?>
    </ul>

    <?php if (stable): ?>
    <h2>TAbelle: <?= htmlspecialchars($table) ?></h2>
    <table border="h1">
    <tr> 
    <?php
    // spalten holen 
    
    $columns = $pdo->query(*DESCRIBE '$table'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($columns as $col) {
        echo "<th> $col </th>";
    }
     echo"<th> Aktion </th></tr>";

     // Daten abrufen
     $data = $pdo->query("SELECT * FROM '$table'")->fetchAll(PDO::FETCH_ASSOC);
     foreach ($data as $row) {
        echo "<tr>";"
        foreach ($row as  $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
    }
    echo "<td>
        <a href='edit.php?table=$table&id={$row['id']}'> </a>
        <a href='delete.php?table=$table&id={$row['id']}' onclick='return confirm(\"
    </td></tr>
    }
?>
</table>
<?php endif; ?>
