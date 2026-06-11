<?php
function register($data) {
    $db = getDB();
    if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Name, email and password are required']);
        return;
    }
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }
    $hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['name'], $data['email'], $data['phone'] ?? '', $hash]);
    $id = $db->lastInsertId();
    $user = $db->query("SELECT id, name, email, phone, address, role FROM users WHERE id = $id")->fetch();
    echo json_encode(['success' => true, 'message' => 'Account created successfully', 'user' => $user, 'token' => base64_encode($id . ':' . $data['email'])]);
}

function login($data) {
    $db = getDB();
    if (empty($data['email']) || empty($data['password'])) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        return;
    }
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($data['password'], $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        return;
    }
    unset($user['password']);
    echo json_encode(['success' => true, 'message' => 'Login successful', 'user' => $user, 'token' => base64_encode($user['id'] . ':' . $user['email'])]);
}
