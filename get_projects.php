<?php
header('Content-Type: application/json; charset=utf-8');
require 'db.php';

try {
    // Projeleri en yeniden eskiye doğru çekiyoruz
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();

    // JavaScript'in okuyabilmesi için JSON formatında döndürüyoruz
    echo json_encode([
        'status' => 'success',
        'data' => $projects
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Projeler yüklenirken bir hata oluştu.'
    ]);
}
?>