<?php
function getSettings() {
    $db = getDB();
    $rows = $db->query("SELECT key_name, value FROM settings")->fetchAll();
    $settings = [];
    foreach ($rows as $row) $settings[$row['key_name']] = $row['value'];
    echo json_encode(['success' => true, 'settings' => $settings]);
}

function updateSettings($data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO settings (key_name, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value = ?");
    foreach ($data as $key => $value) $stmt->execute([$key, $value, $value]);
    echo json_encode(['success' => true, 'message' => 'Settings updated']);
}
