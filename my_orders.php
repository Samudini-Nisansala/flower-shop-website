<?php
$page_title = "My Orders";
require_once 'db_connect.php';
require_once 'header.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_error'] = "Please log in to view your orders.";
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch customer's orders
$stmt = $conn->prepare("
    SELECT o.*, COUNT(oi.id) AS total_items 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.id 
    ORDER BY o.order_date DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">My Order History</h2>
            <p class="text-muted mb-0">Track and view your past flower orders</p>
        </div>
        <a href="shop.php" class="btn btn-outline-danger rounded-pill px-4"><i class="fa-solid fa-cart-plus me-1"></i> Shop More Flowers</a>
    </div>

    <?php if ($orders_result->num_rows > 0): ?>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Order ID</th>
                            <th>Date</th>
                            <th>Recipient</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 fw-bold text-danger">#<?php echo $order['id']; ?></td>
                                <td><?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['customer_email']); ?></small>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo $order['total_items']; ?> item(s)</span></td>
                                <td class="fw-bold text-dark">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td>
                                    <?php 
                                    $status = strtolower($order['status']);
                                    $badgeClass = 'bg-warning text-dark';
                                    if ($status === 'completed') $badgeClass = 'bg-success';
                                    elseif ($status === 'processing') $badgeClass = 'bg-info text-dark';
                                    elseif ($status === 'cancelled') $badgeClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                        <?php echo $order['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white shadow-sm rounded-4">
            <i class="fa-solid fa-basket-shopping text-muted fa-4x mb-3" style="opacity: 0.3;"></i>
            <h4>No Orders Found</h4>
            <p class="text-muted">You haven't placed any flower orders yet.</p>
            <a href="shop.php" class="btn btn-danger rounded-pill px-4 mt-2" style="background-color: var(--primary-color); border: none;">Explore Flowers</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
