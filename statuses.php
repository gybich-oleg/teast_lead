<?php
// КОНФІГУРАЦІЯ
$api_url = 'https://crm.belmar.pro/api/v1/getstatuses';
$token = 'ba67df6a-a17c-476f-8e95-bcdb75ed3958';
// ---

// Обробка фільтра дат
$date_from = $_POST['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_POST['date_to'] ?? date('Y-m-d');

$api_date_from = $date_from . ' 00:00:00';
$api_date_to = $date_to . ' 23:59:59';

$statuses_data = [];
$error_message = '';

// Формування тіла запиту для getstatuses
$request_data = [
    'date_from' => $api_date_from,
    'date_to' => $api_date_to,
    'page' => 0,
    'limit' => 500,
];

// Відправка cURL запиту
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'token: ' . $token,
    'Content-Type: application/json',
]);

$response = curl_exec($ch);
curl_close($ch);

// Обробка відповіді
if ($response) {
    $result = json_decode($response, true);

    // Перевіряємо статус успіху та наявність поля 'data'
    if (isset($result['status']) && $result['status'] === true && isset($result['data'])) {

        $data_raw = $result['data'];

        // --- ВИПРАВЛЕННЯ: Додаємо перевірку типу перед другим json_decode() ---
        if (is_string($data_raw)) {
            // Якщо 'data' - це рядок, декодуємо його (як очікується)
            $statuses_data = json_decode($data_raw, true) ?? [];
        } elseif (is_array($data_raw)) {
            // Якщо 'data' - це вже масив (що і викликало помилку), використовуємо його без декодування
            $statuses_data = $data_raw;
        } else {
            // Інші неочікувані типи
            $error_message = 'Некоректний формат даних у полі "data" від API.';
        }
        // --- КІНЕЦЬ ВИПРАВЛЕННЯ ---

    } else {
        // Обробка помилок (наприклад, недійсний токен)
        $error_message = $result['error'] ?? 'Невідома помилка API.';
    }
} else {
    $error_message = 'Помилка з\'єднання з API.';
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Сторінка 2: Статуси Лідів</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Додати Лід</a> |
        <a href="statuses.php">Переглянути Статуси</a>
    </nav>
</header>

<h1>Статуси Лідів (GetStatuses)</h1>

<form method="POST" action="statuses.php" class="filter-form">
    <label>Від: <input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>" required></label>
    <label>До: <input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>" required></label>
    <button type="submit">Фільтрувати</button>
</form>

<?php if ($error_message): ?>
    <p class="message error">Помилка: <?= htmlspecialchars($error_message) ?></p>
<?php elseif (empty($statuses_data)): ?>
    <p>Не знайдено лідів за обраний період.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Status</th>
            <th>FTD</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($statuses_data as $lead): ?>
            <tr>
                <td><?= htmlspecialchars($lead['id'] ?? '') ?></td>
                <td><?= htmlspecialchars($lead['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($lead['status'] ?? '') ?></td>
                <td><?= htmlspecialchars($lead['ftd'] ?? '0') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>