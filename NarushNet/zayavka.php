<?php
$pageTitle = 'Мои заявления';
require_once "db/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user']['id_user'];

$query = "
    SELECT s.id_service, s.car_number, s.violation_description, s.address, s.data, s.time, stat.name_status, stat.id_status
    FROM service s
    JOIN status stat ON s.status_id = stat.id_status
    WHERE s.user_id = ?
    ORDER BY s.data DESC, s.time DESC
";

$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$zayavki = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

ob_start();
?>

<h1 class="h2 mb-4">Мои заявления о нарушениях</h1>

<?php if (empty($zayavki)): ?>
    <div class="alert alert-info">
        <p class="mb-3">У вас пока нет заявлений о нарушениях.</p>
        <a href="create_zayavka.php" class="btn btn-primary">Сообщить о нарушении</a>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        <?php foreach ($zayavki as $z): ?>
            <?php 
            // Определяем цвет статуса
            $status_color = '';
            if ($z['id_status'] == 1) { // Новая заявка
                $status_color = 'bg-secondary';
            } elseif ($z['id_status'] == 2) { // Услуга оказана (здесь это будет "Рассмотрено")
                $status_color = 'bg-success';
            } elseif ($z['id_status'] == 3) { // Услуга отменена (здесь это будет "Отклонено")
                $status_color = 'bg-danger';
            }
            ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Заявление #<?= htmlspecialchars($z['id_service']) ?></strong>
                            <span class="badge <?= $status_color ?>"><?= htmlspecialchars($z['name_status']) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Номер авто:</small>
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($z['car_number']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Нарушение:</small>
                            <p class="mb-0"><?= htmlspecialchars($z['violation_description']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Место:</small>
                            <p class="mb-0"><?= htmlspecialchars($z['address']) ?></p>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6">
                                <small class="text-muted">Дата:</small>
                                <p class="mb-0"><?= htmlspecialchars($z['data']) ?></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Время:</small>
                                <p class="mb-0"><?= htmlspecialchars($z['time']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center">
        <a href="create_zayavka.php" class="btn btn-success">Сообщить о новом нарушении</a>
    </div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>