<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Метод не разрешён']);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($name === '' || $phone === '' || $address === '') {
    echo json_encode(['success' => false, 'error' => 'Заполните все поля']);
    exit;
}
$digits = preg_replace('/\D/', '', $phone);
if (strlen($digits) < 11) {
    echo json_encode(['success' => false, 'error' => 'Неверный номер телефона']);
    exit;
}
$file = __DIR__ . '/orders.json';
$orders = file_exists($file) 
    ? (json_decode(file_get_contents($file), true) ?: []) 
    : [];

$order = [
    'id'      => uniqid('ord_'),
    'name'    => $name,
    'phone'   => $phone,
    'address' => $address,
    'product' => 'AuraPods Pro',
    'price'   => 14900,
    'status'  => 'new',
    'date'    => date('Y-m-d H:i:s')
];

$orders[] = $order;
file_put_contents($file, json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['success' => true, 'order_id' => $order['id']]);




$product = [
    'name'  => 'AuraPods Pro',
    'price' => 14900,
    'old'   => 19900,
    'desc'  => 'Беспроводные наушники с активным шумоподавлением и 30 часами работы.',
    'image' => 'https://images.unsplash.com/photo-1600294037681-c80b4cb5b434?w=800&q=80'
];

$features = [
    ['🎧', 'Активное шумоподавление'],
    ['🔋', '30 часов работы'],
    ['💧', 'Защита от воды IPX5'],
    ['⚡', 'Быстрая зарядка USB-C'],
];

$reviews = [
    ['Анна К.', 'Звук просто космос! Ношу целыми днями.'],
    ['Дмитрий П.', 'Шумоподавление реально работает в метро.'],
    ['Игорь С.', 'За эти деньги — лучший вариант.'],
];

$discount = round((1 - $product['price'] / $product['old']) * 100);
?>