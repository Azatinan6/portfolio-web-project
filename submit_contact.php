<?php
header('Content-Type: application/json; charset=utf-8');
require 'db.php';

// Sadece POST isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Gelen JSON verisini PHP objesine çevir
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    $name = htmlspecialchars(strip_tags(trim($data['name'] ?? '')));
    $email = filter_var(trim($data['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(strip_tags(trim($data['message'] ?? '')));

    // Backend Validasyonu (JavaScript atlansa bile veritabanını korumak için)
    if (empty($name) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Lütfen tüm alanları doldurun.']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Geçerli bir e-posta adresi girin.']);
        exit;
    }

    try {
        // Güvenli Insert işlemi (SQL Injection'ı önler)
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Mesajınız başarıyla iletildi! En kısa sürede dönüş yapacağım.']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Sistemsel bir hata oluştu. Lütfen daha sonra tekrar deneyin.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Geçersiz istek metodu.']);
}
?>