<?php
$pageTitle = "Панель администратора";
require_once "db/db.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['user_type_id'] != 2) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    $service_id = (int)$_POST['service_id'];
    $new_status = (int)$_POST['status_id'];
    
    $update_query = "UPDATE service SET status_id = '$new_status' WHERE id_service = '$service_id'";
    if (mysqli_query($db, $update_query)) {
        $message = "Статус заявления успешно изменен!";
    } else {
        $message = "Ошибка при изменении статуса: " . mysqli_error($db);
    }
}

$services_query = "SELECT s.*, u.surname, u.name, u.otchestvo, ss.name_status, ss.id_status
                   FROM service s 
                   LEFT JOIN user u ON s.user_id = u.id_user 
                   LEFT JOIN status ss ON s.status_id = ss.id_status 
                   ORDER BY s.data DESC, s.time DESC";
$services_result = mysqli_query($db, $services_query);

$statuses_query = mysqli_query($db, "SELECT * FROM status");
$statuses = [];
while ($row = mysqli_fetch_assoc($statuses_query)) {
    $statuses[$row['id_status']] = $row;
}

ob_start();
?>

<?php if ($message): ?>
    <div class="alert <?php echo strpos($message, 'успешно') !== false ? 'alert-success' : 'alert-danger'; ?>">
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<h2 class="h3 mb-4">Все заявления о нарушениях</h2>

<!-- Легенда статусов -->
<div class="status-legend mb-4">
    <div class="status-item">
        <div class="status-badge status-new"></div>
        <span>Новая заявка</span>
    </div>
    <div class="status-item">
        <div class="status-badge status-completed"></div>
        <span>Рассмотрено</span>
    </div>
    <div class="status-item">
        <div class="status-badge status-cancelled"></div>
        <span>Отклонено</span>
    </div>
</div>

<?php if ($services_result && mysqli_num_rows($services_result) > 0): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php while ($service = mysqli_fetch_assoc($services_result)): ?>
            <?php 
            // Определяем цвет статуса
            $status_color = '';
            $status_id = $service['id_status'];
            if ($status_id == 1) { // Новая заявка
                $status_color = 'bg-secondary';
            } elseif ($status_id == 2) { // Рассмотрено
                $status_color = 'bg-success';
            } elseif ($status_id == 3) { // Отклонено
                $status_color = 'bg-danger';
            }
            ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Заявление #<?= $service['id_service'] ?></strong>
                            <span class="badge <?= $status_color ?>"><?= htmlspecialchars($service['name_status']) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Автомобиль:</small>
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($service['car_number']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Нарушение:</small>
                            <p class="mb-0"><?= htmlspecialchars($service['violation_description']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Место:</small>
                            <p class="mb-0"><?= htmlspecialchars($service['address']) ?></p>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Дата:</small>
                                <p class="mb-0"><?= htmlspecialchars($service['data']) ?></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Время:</small>
                                <p class="mb-0"><?= htmlspecialchars($service['time']) ?></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Заявитель:</small>
                            <p class="mb-0"><?= htmlspecialchars($service['surname'] . ' ' . $service['name'] . ' ' . $service['otchestvo']) ?></p>
                        </div>
                        
                        <form method="POST" class="border-top pt-3">
                            <input type="hidden" name="service_id" value="<?= $service['id_service'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Изменить статус:</label>
                                <select name="status_id" class="form-select" required>
                                    <option value="">Выберите статус</option>
                                    <?php foreach ($statuses as $id => $status): ?>
                                        <option value="<?= $id ?>" <?= $id == $service['status_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($status['name_status']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="change_status" class="btn btn-primary w-100">
                                Изменить статус
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        Заявлений о нарушениях нет.
    </div>
<?php endif; ?>

<div class="text-center mt-4">
    <a href="zayavka.php" class="btn btn-outline-primary">Вернуться к моим заявлениям</a>
</div>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>