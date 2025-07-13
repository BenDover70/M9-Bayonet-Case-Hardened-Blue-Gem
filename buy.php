<?php  
require_once 'functions.php';  
session_start();  
  
if (!isset($_SESSION['user_id'])) {  
    // Nicht eingeloggt, zurück zum Shop  
    header("Location: shop.php?message=Bitte einloggen!");  
    exit;  
}  
  
$user_id = $_SESSION['user_id'];  
$amount = isset($_GET['amount']) ? (int)$_GET['amount'] : 0;  
  
// Nur erlaubte Werte zulassen  
$allowed = [5, 10, 20, 50, 100, 500];  
if (!in_array($amount, $allowed)) {  
    header("Location: shop.php?message=Ungültiger Betrag!");  
    exit;  
}  
  
// Chips gutschreiben  
$current = get_user_chips($user_id);  
update_user_chips($user_id, $current + $amount);  
  
// Rickroll!  
header("Location: https://youtu.be/dQw4w9WgXcQ?si=S5pbImNEo74-zN_O");  
exit;  
?>