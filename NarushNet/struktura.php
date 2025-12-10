<?php
require_once "db/db.php"; 
$navLinks = [];
$showAuthLinks = true;

// Check if user is logged in via session
if (isset($_SESSION['user'])) {
    $showAuthLinks = false; // Hide auth links if user is logged in
    $user = $_SESSION['user']; // User data is already in the session

    // Check user type from the session data
    $userTypeId = $user['user_type_id'] ?? null;
    
    if ($userTypeId == 2) { // Administrator
        // Admin specific links
        $navLinks = [
            ['href' => 'admin.php', 'text' => 'Панель администратора'],
        ];
    } else { // Regular User
        $navLinks = [
            ['href' => 'zayavka.php', 'text' => 'Мои заявления'],
            ['href' => 'create_zayavka.php', 'text' => 'Сообщить о нарушении'],
        ];
    }
    // Add logout button for all logged-in users
    $navLinks[] = ['href' => 'logout.php', 'text' => 'Выход'];
} else {
    // Links visible before authentication
    $navLinks = [
        ['href' => 'index.php', 'text' => 'Авторизация'],
        ['href' => 'registration.php', 'text' => 'Регистрация'],
    ];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>НарушениямНет - <?php echo $pageTitle; ?></title>
    <link rel='icon' href='images/logo.jpeg'>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ваши стили -->
    <link rel='stylesheet' href='css/style.css'>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-white shadow-sm">
        <div class="container">
            <div class="d-flex align-items-center justify-content-center py-3">
                <img src='images/logo.jpg' alt='логотип' class="me-3" style="width: 80px; height: auto;">
                <h1 class="h3 mb-0">НарушениямНет</h1>
            </div>
        </div>
    </header>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <?php foreach ($navLinks as $link): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo htmlspecialchars($link['href']); ?>">
                                <?php echo htmlspecialchars($link['text']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1 py-4">
        <div class="container">
            <div class="content">
                <?php 
                if (isset($pageContent) && !empty($pageContent)) {
                    echo $pageContent;
                }
                ?>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white py-3 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h3 class="h6 mb-0">2025 &copy; НарушениямНет</h3>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Ваши скрипты -->
    <script src="js/script.js"></script>
</body>
</html>