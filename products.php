<?php
function getProducts() {
    $db = getDB();
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $featured = $_GET['featured'] ?? '';
    $sql = "SELECT * FROM products WHERE is_active = 1";
    $params = [];
    if ($category) { $sql .= " AND category = ?"; $params[] = $category; }
    if ($search) { $sql .= " AND (name LIKE ? OR name_ar LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
    if ($featured) { $sql .= " AND is_featured = 1"; }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['success' => true, 'products' => $stmt->fetchAll()]);
}

function getProduct($id) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) { echo json_encode(['success' => false, 'message' => 'Product not found']); return; }
    echo json_encode(['success' => true, 'product' => $product]);
}

function createProduct($data) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO products (name, name_ar, description, description_ar, price, original_price, category, image_url, stock, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['name'], $data['name_ar'] ?? '', $data['description'] ?? '', $data['description_ar'] ?? '', $data['price'], $data['original_price'] ?? null, $data['category'] ?? '', $data['image_url'] ?? '', $data['stock'] ?? 0, $data['is_featured'] ?? 0]);
    echo json_encode(['success' => true, 'message' => 'Product created', 'id' => $db->lastInsertId()]);
}

function updateProduct($id, $data) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE products SET name=?, name_ar=?, description=?, price=?, original_price=?, category=?, image_url=?, stock=?, is_featured=?, is_active=? WHERE id=?");
    $stmt->execute([$data['name'], $data['name_ar'] ?? '', $data['description'] ?? '', $data['price'], $data['original_price'] ?? null, $data['category'] ?? '', $data['image_url'] ?? '', $data['stock'] ?? 0, $data['is_featured'] ?? 0, $data['is_active'] ?? 1, $id]);
    echo json_encode(['success' => true, 'message' => 'Product updated']);
}

function deleteProduct($id) {
    $db = getDB();
    $db->prepare("UPDATE products SET is_active = 0 WHERE id = ?")->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'Product deleted']);
}
