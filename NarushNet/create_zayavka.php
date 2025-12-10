<?php
$pageTitle = "Сообщить о нарушении";
require_once "db/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user = $_SESSION['user'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $car_number = mysqli_real_escape_string($db, $_POST['car_number']);
    $violation_description = mysqli_real_escape_string($db, $_POST['violation_description']);
    $address = mysqli_real_escape_string($db, $_POST['address']);
    $data = mysqli_real_escape_string($db, $_POST['data']);
    $time = mysqli_real_escape_string($db, $_POST['time']);
    
    if (!empty($car_number) && !empty($violation_description) && !empty($address) && !empty($data) && !empty($time)) {
        $query = "INSERT INTO `service` (`car_number`, `violation_description`, `address`, `user_id`, `data`, `time`, `status_id`) 
                  VALUES ('$car_number', '$violation_description', '$address', '{$user['id_user']}', '$data', '$time', '1')";
        
        if (mysqli_query($db, $query)) {
            $success = "Заявление о нарушении успешно отправлено!";
            $_POST = array();
        } else {
            $error = "Ошибка при отправке заявления: " . mysqli_error($db);
        }
    } else {
        $error = "Все поля обязательны для заполнения!";
    }
}

ob_start();
?>

<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <h2 class="h3 mb-4">Сообщить о нарушении ПДД</h2>
        
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
                        <label for="car_number" class="form-label">Номер автомобиля *</label>
                        <input type="text" id="car_number" name="car_number" class="form-control" required 
                               placeholder="Например: А123БВ777" 
                               value="<?php echo isset($_POST['car_number']) ? htmlspecialchars($_POST['car_number']) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="violation_description" class="form-label">Описание нарушения *</label>
                        <textarea id="violation_description" name="violation_description" class="form-control" required 
                                  placeholder="Подробно опишите нарушение ПДД..." 
                                  rows="4"><?php echo isset($_POST['violation_description']) ? htmlspecialchars($_POST['violation_description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Место нарушения *</label>
                        <input type="text" id="address" name="address" class="form-control" required 
                               placeholder="Улица, дом, район" 
                               value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="data" class="form-label">Дата нарушения *</label>
                            <input type="date" id="data" name="data" class="form-control" required 
                                   value="<?php echo isset($_POST['data']) ? htmlspecialchars($_POST['data']) : ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="time" class="form-label">Время нарушения *</label>
                            <input type="time" id="time" name="time" class="form-control" required 
                                   value="<?php echo isset($_POST['time']) ? htmlspecialchars($_POST['time']) : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Отправить заявление</button>
                        <a href="zayavka.php" class="btn btn-outline-secondary">Вернуться к списку заявлений</a>
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