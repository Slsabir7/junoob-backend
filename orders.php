<?php
function createOrder($data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO orders (user_id, total, shipping_fee, discount, address, payment_method, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['user_id'], $data['total'], $data['shipping_fee'] ?? 0, $data['discount'] ?? 0, $data['address'], $data['payment_method'] ?? 'cod', $data['notes'] ?? '']);
    $orderId = $db->lastInsertId();
    if (!empty($data['items'])) {
        $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($data['items'] as $item) {
            $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
        }
    }
    echo json_encode(['success' => true, 'message' => 'Order placed successfully', 'order_id' => $orderId]);
}

function getOrders() {
    $db = getDB();
    $status = $_GET['status'] ?? '';
    $sql = "SELECT o.*, u.name as customer_name, u.phone as customer_phone FROM orders o LEFT JOIN users u ON o.user_id = u.id";
    $params = [];
    if ($status) { $sql .= " WHERE o.status = ?"; $params[] = $status; }
    $sql .= " ORDER BY o.created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'orders' => $stmt->fetchAll()]);
}

function getUserOrders($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();
    foreach ($orders as &$order) {
        $items = $db->prepare("SELECT oi.*, p.name, p.image_url FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $items->execute([$order['id']]);
        $order['items'] = $items->fetchAll();
    }
    echo json_encode(['success' => true, 'orders' => $orders]);
}

function updateOrderStatus($id, $data) {
    $db = getDB();
    $db->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$data['status'], $id]);
    echo json_encode(['success' => true, 'message' => 'Order status updated']);
}
