<?php
$pageTitle = 'Список заявок';
require_once "db/db.php";

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user']['id_user'];

// Получаем заявки пользователя
$query = "
    SELECT s.id_service, s.address, s.data, s.time, st.name_service, pt.name_pay, stat.name_status, stat.id_status
    FROM service s
    JOIN service_type st ON s.service_type_id = st.id_service_type
    JOIN pay_type pt ON s.pay_type_id = pt.id_pay_type
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

// Формируем контент страницы
ob_start();
?>

<?php if (empty($zayavki)): ?>
    <div class="alert alert-info">
        <p class="mb-3">У вас пока нет заявок.</p>
        <a href="create_zayavka.php" class="btn btn-primary">Создать первую заявку</a>
    </div>
<?php else: ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        <?php foreach ($zayavki as $z): ?>
            <?php 
            // Определяем цвет статуса
            $status_color = '';
            if ($z['id_status'] == 1) { // Новая заявка
                $status_color = 'bg-secondary';
            } elseif ($z['id_status'] == 2) { // Услуга оказана
                $status_color = 'bg-success';
            } elseif ($z['id_status'] == 3) { // Услуга отменена
                $status_color = 'bg-danger';
            }
            ?>
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>Заявка #<?= htmlspecialchars($z['id_service']) ?></strong>
                            <span class="badge <?= $status_color ?>"><?= htmlspecialchars($z['name_status']) ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Адрес:</small>
                            <p class="mb-0"><?= htmlspecialchars($z['address']) ?></p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Услуга:</small>
                            <p class="mb-0"><?= htmlspecialchars($z['name_service']) ?></p>
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
                        <div class="mb-2">
                            <small class="text-muted">Оплата:</small>
                            <p class="mb-0"><?= htmlspecialchars($z['name_pay']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="text-center">
        <a href="create_zayavka.php" class="btn btn-success">Создать новую заявку</a>
    </div>
<?php endif; ?>

<?php
$pageContent = ob_get_clean();
require_once "struktura.php";
?>