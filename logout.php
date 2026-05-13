<?php
session_start();

// Tüm session verilerini temizle ve oturumu yok et
$_SESSION = array();
session_destroy();

// Çerezi (Cookie) geçmiş bir zamana ayarlayarak sil
if (isset($_COOKIE['admin_last_login'])) {
    setcookie("admin_last_login", "", time() - 3600, "/");
}

// Çıkış yaptıktan sonra login sayfasına yönlendir
header("Location: login.php");
exit;
?>