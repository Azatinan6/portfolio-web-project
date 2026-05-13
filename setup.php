<?php
require_once 'db.php';

try {
    // Varsa eski tabloyu sil ve tam uyumlu yenisini oluştur
    $pdo->exec("DROP TABLE IF EXISTS admin");
    $pdo->exec("CREATE TABLE admin (
        id INT AUTO_INCREMENT PRIMARY KEY, 
        username VARCHAR(50) UNIQUE, 
        password_hash VARCHAR(255)
    )");

    // admin123 şifresini güvenli (kriptolu) hale getir
    $hash = password_hash('admin123', PASSWORD_DEFAULT);

    // Veritabanına kaydet
    $stmt = $pdo->prepare("INSERT INTO admin (username, password_hash) VALUES ('admin', ?)");
    $stmt->execute([$hash]);

    echo "<h3>Harika! Admin hesabı başarıyla oluşturuldu.</h3>";
    echo "<p>Kullanıcı adı: <b>admin</b></p>";
    echo "<p>Şifre: <b>admin123</b></p>";
    echo '<a href="login.php">Şimdi Giriş Yapmayı Dene</a>';

} catch (Exception $e) {
    echo "Bir hata oluştu: " . $e->getMessage();
}
?>