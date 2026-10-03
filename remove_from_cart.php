<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        $removed_name = $_SESSION['cart'][$product_id]['name'];
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['flash_success'] = "Removed <strong>" . htmlspecialchars($removed_name) . "</strong> from your cart.";
    }
}

$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'cart.php';
header("Location: " . $referer);
exit();
