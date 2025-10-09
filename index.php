<?php
// КОНФІГУРАЦІЯ
$api_url = 'https://crm.belmar.pro/api/v1/addlead';
$token = 'ba67df6a-a17c-476f-8e95-bcdb75ed3958';
$static_data = [
    'box_id' => 28, 'offer_id' => 5, 'countryCode' => 'GB',
    'language' => 'en', 'password' => 'qwerty12',
];
// ---

$message = ''; $message_type = '';

function get_client_ip() {
    // Отримання IP-адреси, безпечніше з боку сервера
    if (isset($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Збір обов'язкових полів
    $required_fields = ['firstName', 'lastName', 'phone', 'email'];
    $form_data = array_intersect_key($_POST, array_flip($required_fields));

    $dynamic_data = [
        'ip' => get_client_ip(),
        'landingUrl' => $_SERVER['HTTP_HOST'] ?? 'localhost',
    ];

    $lead_data = array_merge($form_data, $static_data, $dynamic_data);

    // Відправка cURL запиту
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($lead_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'token: ' . $token,
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Обробка відповіді
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['status']) && $result['status'] === true) {
            $message_type = 'success';
            $message = 'Лід успішно додано! ID: ' . $result['id'];
        } else {
            $message_type = 'error';
            $error_text = $result['error'] ?? 'Невідома помилка API.';
            $message = "Помилка додавання ліда (HTTP $http_code): $error_text";
        }
    } else {
        $message_type = 'error';
        $message = 'Помилка з\'єднання з API.';
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Сторінка 1: Додавання Ліда</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Додати Лід</a> |
        <a href="src/statuses.php">Переглянути Статуси</a>
    </nav>
</header>

<h1>Додати Лід (AddLead)</h1>

<?php if ($message): ?>
    <p class="message <?= $message_type ?>"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST" action="index.php">
    <label>Ім'я (firstName): <input type="text" name="firstName" required></label>
    <label>Прізвище (lastName): <input type="text" name="lastName" required></label>
    <label>Телефон (phone): <input type="text" name="phone" required></label>
    <label>Email (email): <input type="email" name="email" required></label>
    <button type="submit">Надіслати Лід</button>
</form>
</body>
</html>