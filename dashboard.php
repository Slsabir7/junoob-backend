<?php
function validatePromo($data) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM promo_codes WHERE code = ? AND is_active = 1");
    $stmt->execute([$data['code']]);
    $promo = $stmt->fetch();
    if (!$promo) { echo json_encode(['success' => false, 'message' => 'Invalid promo code']); return; }
    if ($promo['expires_at'] && strtotime($promo['expires_at']) < time()) {
        echo json_encode(['success' => false, 'message' => 'Promo code has expired']); return;
    }
    if ($data['order_total'] < $promo['min_order']) {
        echo json_encode(['success' => false, 'message' => 'Minimum order amount is ' . $promo['min_order'] . ' QAR']); return;
    }
    $discount = $promo['discount_type'] === 'percentage'
        ? ($data['order_total'] * $promo['discount_value'] / 100)
        : $promo['discount_value'];
    echo json_encode(['success' => true, 'discount' => $discount, 'type' => $promo['discount_type'], 'value' => $promo['discount_value']]);
}

function getDashboard() {
    $db = getDB();
    $totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
    $totalRevenue = $db->query("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
    $totalCustomers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    $totalProducts = $db->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
    $pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    $recentOrders = $db->query("SELECT o.*, u.name as customer_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'total_customers' => $totalCustomers,
            'total_products' => $totalProducts,
            'pending_orders' => $pendingOrders
        ],
        'recent_orders' => $recentOrders
    ]);
}
