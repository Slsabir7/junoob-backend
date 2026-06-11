<?php
function getUser($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT id, name, email, phone, address, role FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    echo json_encode($user ? ['success' => true, 'user' => $user] : ['success' => false, 'message' => 'User not found']);
}

function updateUser($id, $data) {
    $db = getDB();
    $db->prepare("UPDATE users SET name=?, phone=?, address=? WHERE id=?")->execute([$data['name'], $data['phone'] ?? '', $data['address'] ?? '', $id]);
    $user = $db->query("SELECT id, name, email, phone, address, role FROM users WHERE id = $id")->fetch();
    echo json_encode(['success' => true, 'message' => 'Profile updated', 'user' => $user]);
}

function changePassword($data) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$data['user_id']]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($data['current_password'], $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        return;
    }
    $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($data['new_password'], PASSWORD_DEFAULT), $data['user_id']]);
    echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
}
