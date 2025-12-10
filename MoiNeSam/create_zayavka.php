<?php
$pageTitle = "Создание заявки";
require_once "db/db.php";

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$error = "";
$success = "";

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = mysqli_real_escape_string($db, $_POST['address']);
    $contact_data = mysqli_real_escape_string($db, $_POST['contact_data']);
    $data = mysqli_real_escape_string($db, $_POST['data']);
    $time = mysqli_real_escape_string($db, $_POST['time']);
    $service_type_id = (int)$_POST['service_type_id'];
    $pay_type_id = (int)$_POST['pay_type_id'];
    
    // Валидация обязательных полей
    if (!empty($address) && !empty($contact_data) && !empty($data) && !empty($time) && $service_type_id > 0 && $pay_type_id > 0) {
        
        // Вставляем новую заявку
        $query = "INSERT INTO `service` (`address`, `user_id`, `service_type_id`, `data`, `time`, `pay_type_id`, `status_id`, `reason_cancel`) 
                  VALUES ('$address', '{$user['id_user']}', '$service_type_id', '$data', '$time', '$pay_type_id', '1', '$contact_data')";
        
        if (mysqli_query($db, $query)) {
            $success = "Заявка успешно создана!";
        } else {
            $error = "Ошибка при создании заявки: " . mysqli_error($db);
        }
    } else {
        $error = "Все поля обязательны для заполнения!";
    }
}

// Получим типы услуг
$service_types = [];
$service_type_query = mysqli_query($db, "SELECT * FROM service_type");
if ($service_type_query) {
    while ($row = mysqli_fetch_assoc($service_type_query)) {
        $service_types[$row['id_service_type']] = $row;
    }
}

// Получим типы оплаты
$pay_types = [];
$pay_type_query = mysqli_query($db, "SELECT * FROM pay_type");
if ($pay_type_query) {
    while ($row = mysqli_fetch_assoc($pay_type_query)) {
        $pay_types[$row['id_pay_type']] = $row;
    }
}

// Формируем контент страницы
ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="address" class="form-label">Адрес:</label>
                        <input type="text" id="address" name="address" class="form-control" 
                               required value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="contact_data" class="form-label">Контактные данные:</label>
                        <input type="text" id="contact_data" name="contact_data" class="form-control" 
                               required placeholder="Телефон или email" 
                               value="<?php echo isset($_POST['contact_data']) ? htmlspecialchars($_POST['contact_data']) : ''; ?>">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="data" class="form-label">Желаемая дата:</label>
                            <input type="date" id="data" name="data" class="form-control" 
                                   required value="<?php echo isset($_POST['data']) ? htmlspecialchars($_POST['data']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="time" class="form-label">Желаемое время:</label>
                            <input type="time" id="time" name="time" class="form-control" 
                                   required value="<?php echo isset($_POST['time']) ? htmlspecialchars($_POST['time']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_type_id" class="form-label">Вид услуги:</label>
                        <select id="service_type_id" name="service_type_id" class="form-select" required>
                            <option value="">-- Выберите услугу --</option>
                            <?php foreach ($service_types as $id => $type): ?>
                                <option value="<?php echo $id; ?>" <?php echo (isset($_POST['service_type_id']) && $_POST['service_type_id'] == $id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name_service']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pay_type_id" class="form-label">Тип оплаты:</label>
                        <select id="pay_type_id" name="pay_type_id" class="form-select" required>
                            <option value="">-- Выберите тип оплаты --</option>
                            <?php foreach ($pay_types as $id => $type): ?>
                                <option value="<?php echo $id; ?>" <?php echo (isset($_POST['pay_type_id']) && $_POST['pay_type_id'] == $id) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['name_pay']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Создать заявку</button>
                        <a href="zayavka.php" class="btn btn-outline-secondary">Вернуться к списку заявок</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>