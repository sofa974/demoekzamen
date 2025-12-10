<?php
$pageTitle = 'Регистрация';
require_once "db/db.php"; // Убедитесь что эта строка есть

$errors = [];
$success = "";
$login = $password = $surname = $name = $otchestvo = $phone = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $otchestvo = trim($_POST['otchestvo'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    $errors = [];
    if (empty($login)) $errors[] = "Логин обязателен для заполнения";
    if (empty($password)) $errors[] = "Пароль обязателен для заполнения";
    if (empty($surname)) $errors[] = "Фамилия обязательна для заполнения";
    if (empty($name)) $errors[] = "Имя обязательно для заполнения";
    if (empty($otchestvo)) $errors[] = "Отчество обязательно для заполнения";
    if (empty($phone)) $errors[] = "Телефон обязателен для заполнения";
    if (empty($email)) $errors[] = "Email обязателен для заполнения";
    
    if (!empty($login)) {
        $check_login = mysqli_query($db, "SELECT id_user FROM user WHERE username = '$login'");
        if (mysqli_num_rows($check_login) > 0) {
            $errors[] = "Пользователь с таким логином уже существует";
        }
    }
        
    // Если ошибок нет - регистрируем пользователя
    if (empty($errors)) {
        // user_type_id = 1 - обычный пользователь
        $sql = "INSERT INTO user (user_type_id, surname, name, otchestvo, phone, email, username, password) 
                VALUES ('1', '$surname', '$name', '$otchestvo', '$phone', '$email', '$login', MD5('$password'))";
        
        if (mysqli_query($db, $sql)) {
            $success = "Регистрация прошла успешно! Теперь вы можете войти в систему.";
            // Очищаем поля формы после успешной регистрации
            $login = $password = $surname = $name = $otchestvo = $phone = $email = '';
        } else {
            $errors[] = "Ошибка при регистрации: " . mysqli_error($db);
        }
    }
}

// Формируем контент страницы
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($success) && !empty($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="surname" class="form-label">Фамилия *</label>
                            <input type="text" name="surname" id="surname" class="form-control" 
                                   value="<?php echo htmlspecialchars($surname); ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="name" class="form-label">Имя *</label>
                            <input type="text" name="name" id="name" class="form-control" 
                                   value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="otchestvo" class="form-label">Отчество *</label>
                            <input type="text" name="otchestvo" id="otchestvo" class="form-control" 
                                   value="<?php echo htmlspecialchars($otchestvo); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="login" class="form-label">Логин *</label>
                        <input type="text" name="login" id="login" class="form-control" 
                               value="<?php echo htmlspecialchars($login); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Пароль *</label>
                        <input type="password" name="password" id="password" class="form-control" 
                               value="<?php echo htmlspecialchars($password); ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Телефон *</label>
                            <input type="tel" name="phone" id="phone" class="form-control" 
                                   value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
                    </div>
                </form>
                
                <div class="mt-3 text-center">
                    <p class="mb-0">Уже есть аккаунт? <a href="index.php">Войдите здесь</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>