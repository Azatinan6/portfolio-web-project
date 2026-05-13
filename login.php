<?php
session_start();

// Eğer kullanıcı zaten giriş yapmışsa direkt admin paneline yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin.php");
    exit;
}

require_once 'db.php'; // Veritabanı bağlantımızı dahil ediyoruz
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Veritabanından admini bul
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    // Şifre doğrulama (Kriptolanmış şifre çözülür)
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // 1. SESSION BAŞLAT (Hocanın Rubric Kuralı)
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];

        // 2. COOKIE OLUŞTUR (Hocanın Rubric Kuralı - 1 günlük çerez)
        setcookie("admin_last_login", date("Y-m-d H:i:s"), time() + 86400, "/");

        header("Location: admin.php");
        exit;
    } else {
        $error = "Hatalı kullanıcı adı veya şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Portfolio</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: var(--bg-color);
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 40px;
            text-align: center;
        }

        .login-card input {
            margin-bottom: 20px;
        }

        .error-msg {
            color: #ff5f56;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="login-card glass-effect hover-glow animate-slide-up">
        <h2 style="margin-bottom: 30px;">Terminal <span class="text-gradient">Access.</span></h2>

        <?php if ($error): ?>
            <div class="error-msg">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mac-form" style="padding: 0;">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="mac-btn primary btn-glow" style="width: 100%;">Login</button>
        </form>
        <p style="margin-top: 20px; font-size: 0.8rem; color: var(--text-secondary);">Authorized personnel only.</p>
    </div>
</body>

</html>