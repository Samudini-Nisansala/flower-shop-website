<?php
session_start();
require_once 'db_connect.php';

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] = "Access denied. Admin privileges required.";
    header("Location: login.php");
    exit();
}

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = (int)$_POST['order_id'];
    $new_status = $_POST['status'];

    $allowed_statuses = ['pending', 'processing', 'completed', 'cancelled'];
    if (in_array($new_status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        if ($stmt->execute()) {
            $_SESSION['flash_success'] = "Order #$order_id status updated to <strong>" . strtoupper($new_status) . "</strong>.";
        }
    }
    header("Location: admin.php");
    exit();
}

// Dashboard Statistics Queries
$total_revenue = $conn->query("SELECT SUM(total_amount) AS revenue FROM orders WHERE status != 'cancelled'")->fetch_assoc()['revenue'] ?? 0;
$total_orders = $conn->query("SELECT COUNT(*) AS count FROM orders")->fetch_assoc()['count'] ?? 0;
$total_products = $conn->query("SELECT COUNT(*) AS count FROM products")->fetch_assoc()['count'] ?? 0;
$total_messages = $conn->query("SELECT COUNT(*) AS count FROM messages")->fetch_assoc()['count'] ?? 0;
$total_feedback = $conn->query("SELECT COUNT(*) AS count FROM feedback")->fetch_assoc()['count'] ?? 0;

// Fetch Recent Orders with Order Items
$orders_query = "
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') AS item_list
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    GROUP BY o.id
    ORDER BY o.order_date DESC
    LIMIT 30
";
$orders_result = $conn->query($orders_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Petal Picks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #2c3e50; color: white; padding-top: 20px; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 12px 20px; border-radius: 8px; margin: 4px 10px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #34495e; color: #3498db; }
        .content { padding: 30px; }
        .stat-card { border: none; border-radius: 16px; color: white; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); }
        .bg-grad-1 { background: linear-gradient(135deg, #2ed573, #1e90ff); }
        .bg-grad-2 { background: linear-gradient(135deg, #ff4757, #ff6b81); }
        .bg-grad-3 { background: linear-gradient(135deg, #ffa502, #ff7f50); }
        .bg-grad-4 { background: linear-gradient(135deg, #70a1ff, #1e90ff); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar px-0">
            <h4 class="text-center py-3 mb-3 border-bottom border-secondary text-warning">🌸 Admin Suite</h4>
            <a href="admin.php" class="active"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
            <a href="admin_products.php"><i class="fa-solid fa-spa me-2"></i> Manage Products</a>
            <a href="admin_messages.php"><i class="fa-solid fa-comments me-2"></i> Messages & Feedback</a>
            <hr class="mx-3 border-secondary">
            <a href="index.php" target="_blank"><i class="fa-solid fa-globe me-2"></i> View Website</a>
            <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Admin Dashboard Overview</h2>
                <span class="badge bg-dark px-3 py-2 fs-6">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            </div>

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
            <?php endif; ?>

            <!-- Stat Cards Row -->
            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="stat-card bg-grad-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-white-50 text-uppercase fw-bold">Total Revenue</small>
                                <h3 class="fw-bold mb-0 mt-1">$<?php echo number_format($total_revenue, 2); ?></h3>
                            </div>
                            <i class="fa-solid fa-dollar-sign fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card bg-grad-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-white-50 text-uppercase fw-bold">Total Orders</small>
                                <h3 class="fw-bold mb-0 mt-1"><?php echo $total_orders; ?></h3>
                            </div>
                            <i class="fa-solid fa-box-open fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card bg-grad-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-white-50 text-uppercase fw-bold">Flower Products</small>
                                <h3 class="fw-bold mb-0 mt-1"><?php echo $total_products; ?></h3>
                            </div>
                            <i class="fa-solid fa-seedling fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="stat-card bg-grad-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-white-50 text-uppercase fw-bold">Messages / Reviews</small>
                                <h3 class="fw-bold mb-0 mt-1"><?php echo $total_messages + $total_feedback; ?></h3>
                            </div>
                            <i class="fa-solid fa-envelope-open-text fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table Section -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="fa-solid fa-receipt text-primary me-2"></i> Recent Customer Orders</h5>
                    <small class="text-muted">Showing latest 30 orders</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Order ID</th>
                                <th>Customer Info</th>
                                <th>Items Ordered</th>
                                <th>Total Amount</th>
                                <th>Order Date</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Update Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                                <?php while ($order = $orders_result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold text-danger">#<?php echo $order['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong><br>
                                            <small class="text-muted"><i class="fa-solid fa-envelope me-1"></i><?php echo htmlspecialchars($order['customer_email']); ?></small><br>
                                            <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i><?php echo htmlspecialchars($order['customer_address']); ?></small>
                                        </td>
                                        <td>
                                            <small class="fw-semibold text-dark">
                                                <?php echo htmlspecialchars($order['item_list'] ?: 'Standard Flower Package'); ?>
                                            </small>
                                        </td>
                                        <td class="fw-bold text-dark">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td><small class="text-muted"><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></small></td>
                                        <td>
                                            <?php 
                                            $st = strtolower($order['status']);
                                            $badge = 'bg-warning text-dark';
                                            if ($st === 'completed') $badge = 'bg-success';
                                            elseif ($st === 'processing') $badge = 'bg-info text-dark';
                                            elseif ($st === 'cancelled') $badge = 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.75rem;">
                                                <?php echo $order['status']; ?>
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <form method="POST" action="admin.php" class="d-inline-flex gap-1 m-0">
                                                <input type="hidden" name="action" value="update_status">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit();">
                                                    <option value="pending" <?php echo ($st === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="processing" <?php echo ($st === 'processing') ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="completed" <?php echo ($st === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                                    <option value="cancelled" <?php echo ($st === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">No orders found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
