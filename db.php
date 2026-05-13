<?php
// Hata gösterimini aktif edelim (Canlıya alırken bunları 0 yapabilirsin)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$dbname = 'portfolio_db';
$user = 'root'; // XAMPP/WAMP varsayılan kullanıcısı
$pass = '';     // XAMPP/WAMP varsayılan şifresi boştur

try {
    // PDO ile güvenli veritabanı bağlantısı
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Veritabanı bağlantı hatası: ' . $e->getMessage()
    ]));
}
?>