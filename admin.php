<?php
session_start();

// GÜVENLİK KONTROLÜ: Eğer giriş yapılmamışsa login sayfasına at (Session Kuralı)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once 'db.php';

// Çerez (Cookie) okuma: Son giriş zamanını göster (Cookie Kuralı)
$lastLogin = isset($_COOKIE['admin_last_login']) ? $_COOKIE['admin_last_login'] : 'İlk girişiniz.';

// YENİ PROJE EKLEME İŞLEMİ (CRUD - Create)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_project'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $tech_stack = trim($_POST['tech_stack']);

    $stmt = $pdo->prepare("INSERT INTO projects (title, description, image_url, tech_stack) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $description, $image_url, $tech_stack]);
    header("Location: admin.php"); // Sayfayı yenile
    exit;
}

// PROJE SİLME İŞLEMİ (CRUD - Delete)
if (isset($_GET['delete_project'])) {
    $id = $_GET['delete_project'];
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// MESAJ SİLME İŞLEMİ
if (isset($_GET['delete_message'])) {
    $id = $_GET['delete_message'];
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// VERİLERİ ÇEKME
$projects = $pdo->query("SELECT * FROM projects ORDER BY id DESC")->fetchAll();
$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Portfolio</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .admin-container { max-width: 1200px; margin: 50px auto; padding: 20px; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 1px solid var(--border-color); }
        .admin-card { padding: 30px; border-radius: 20px; margin-bottom: 30px; overflow-x: auto; }
        
        /* HOCANIN İSTEDİĞİ HTML TABLO STİLLERİ */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 0.95rem; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--border-color); color: var(--text-primary); }
        th { background-color: rgba(255, 255, 255, 0.05); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; color: var(--text-secondary); }
        tr:hover { background-color: rgba(255, 255, 255, 0.02); }
        
        .action-btn { padding: 8px 15px; border-radius: 8px; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: var(--transition); border: 1px solid transparent; }
        .btn-delete { background: rgba(255, 59, 48, 0.1); color: #ff3b30; border-color: rgba(255, 59, 48, 0.3); }
        .btn-delete:hover { background: #ff3b30; color: #fff; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="admin-container">
        <div class="admin-header animate-slide-up">
            <div>
                <h2>Terminal <span class="text-gradient">Dashboard</span></h2>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 5px;">Hoş geldin, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>. Son başarılı giriş: <?php echo htmlspecialchars($lastLogin); ?></p>
            </div>
            <a href="logout.php" class="mac-btn secondary" style="padding: 10px 20px; font-size: 0.9rem;"><i class="fa-solid fa-power-off"></i> Güvenli Çıkış</a>
        </div>

        <div class="admin-card glass-effect hover-glow animate-slide-up" style="animation-delay: 0.1s;">
            <h3><i class="fa-solid fa-plus"></i> Yeni Proje Ekle</h3>
            <form method="POST" class="mac-form" style="padding: 20px 0 0 0;">
                <div class="form-grid">
                    <input type="text" name="title" placeholder="Proje Başlığı" required>
                    <input type="text" name="tech_stack" placeholder="Teknolojiler (Örn: React, Node.js)" required>
                </div>
                <input type="url" name="image_url" placeholder="Görsel URL (Unsplash vb.)" required style="margin-bottom: 20px;">
                <textarea name="description" rows="3" placeholder="Proje Açıklaması" required></textarea>
                <button type="submit" name="add_project" class="mac-btn primary" style="margin-top: 15px;">Veritabanına Kaydet</button>
            </form>
        </div>

        <div class="admin-card glass-effect hover-glow animate-slide-up" style="animation-delay: 0.2s;">
            <h3><i class="fa-solid fa-layer-group"></i> Mevcut Projeler</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Görsel</th>
                        <th>Başlık</th>
                        <th>Teknolojiler</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                    <tr>
                        <td>#<?php echo $project['id']; ?></td>
                        <td><img src="<?php echo htmlspecialchars($project['image_url']); ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;"></td>
                        <td><strong><?php echo htmlspecialchars($project['title']); ?></strong></td>
                        <td><?php echo htmlspecialchars($project['tech_stack']); ?></td>
                        <td><a href="?delete_project=<?php echo $project['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Bu projeyi silmek istediğine emin misin?');"><i class="fa-solid fa-trash"></i> Sil</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-card glass-effect hover-glow animate-slide-up" style="animation-delay: 0.3s;">
            <h3><i class="fa-solid fa-envelope"></i> Gelen Mesajlar</h3>
            <table>
                <thead>
                    <tr>
                        <th>Tarih</th>
                        <th>Gönderen</th>
                        <th>E-posta</th>
                        <th>Mesaj</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($messages) > 0): ?>
                        <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td style="white-space: nowrap;"><?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($msg['name']); ?></strong></td>
                            <td><a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: var(--accent-color); text-decoration: none;"><?php echo htmlspecialchars($msg['email']); ?></a></td>
                            <td style="max-width: 300px; line-height: 1.4;"><?php echo htmlspecialchars($msg['message']); ?></td>
                            <td><a href="?delete_message=<?php echo $msg['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Mesajı silmek istediğine emin misin?');"><i class="fa-solid fa-trash"></i> Sil</a></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-secondary);">Henüz gelen mesaj yok.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</body>
</html>