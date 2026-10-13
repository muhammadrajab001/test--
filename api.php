<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$file = __DIR__ . '/data.json';

if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
}
function getTasks($file) {
    $content = file_get_contents($file);
    return json_decode($content, true) ?: [];
}
function saveTasks($file, $tasks) {
    file_put_contents($file, json_encode($tasks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// --- Обработка GET (получить список) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (($_GET['action'] ?? '') === 'list') {
        echo json_encode(getTasks($file), JSON_UNESCAPED_UNICODE);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    $tasks = getTasks($file);

    if ($action === 'add') {
        $text = trim($input['text'] ?? '');
        if ($text !== '') {
            $tasks[] = [
                'id'   => uniqid(),
                'text' => $text,
                'done' => false
            ];
            saveTasks($file, $tasks);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Пустая задача']);
        }
    } 
    elseif ($action === 'toggle') {
        $id = $input['id'] ?? '';
        foreach ($tasks as &$t) {
            if ($t['id'] === $id) {
                $t['done'] = !$t['done'];
                break;
            }
        }
        saveTasks($file, $tasks);
        echo json_encode(['success' => true]);
    } 
    elseif ($action === 'delete') {
        $id = $input['id'] ?? '';
        $tasks = array_values(array_filter($tasks, fn($t) => $t['id'] !== $id));
        saveTasks($file, $tasks);
        echo json_encode(['success' => true]);
    } 
    else {
        echo json_encode(['success' => false, 'error' => 'Неизвестное действие']);
    }
    exit;
}