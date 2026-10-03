<?php
session_start();
require_once 'db_connect.php';

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] = "Access denied. Admin privileges required.";
    header("Location: login.php");
    exit();
}

// Delete message
if (isset($_GET['action']) && $_GET['action'] === 'delete_msg' && isset($_GET['id'])) {
    $msg_id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $msg_id);
    $stmt->execute();
    $_SESSION['flash_success'] = "Message #$msg_id deleted.";
    header("Location: admin_messages.php");
    exit();
}

// Delete feedback
if (isset($_GET['action']) && $_GET['action'] === 'delete_fb' && isset($_GET['id'])) {
    $fb_id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param("i", $fb_id);
    $stmt->execute();
    $_SESSION['flash_success'] = "Feedback #$fb_id deleted.";
    header("Location: admin_messages.php");
    exit();
}

// Fetch Contact Messages
$messages_res = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");

// Fetch Customer Feedback
$feedback_res = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages & Feedback - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #2c3e50; color: white; padding-top: 20px; }
        .sidebar a { color: #ecf0f1; text-decoration: none; display: block; padding: 12px 20px; border-radius: 8px; margin: 4px 10px; font-weight: 500; }
        .sidebar a:hover, .sidebar a.active { background-color: #34495e; color: #3498db; }
        .content { padding: 30px; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar px-0">
            <h4 class="text-center py-3 mb-3 border-bottom border-secondary text-warning">🌸 Admin Suite</h4>
            <a href="admin.php"><i class="fa-solid fa-chart-line me-2"></i> Dashboard</a>
            <a href="admin_products.php"><i class="fa-solid fa-spa me-2"></i> Manage Products</a>
            <a href="admin_messages.php" class="active"><i class="fa-solid fa-comments me-2"></i> Messages & Feedback</a>
            <hr class="mx-3 border-secondary">
            <a href="index.php" target="_blank"><i class="fa-solid fa-globe me-2"></i> View Website</a>
            <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 content">
            <h2 class="mb-4">Messages & Customer Feedback</h2>

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Contact Messages -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-primary text-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-envelope me-2"></i> Contact Form Messages</h5>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>From</th>
                                        <th>Message</th>
                                        <th>Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($messages_res && $messages_res->num_rows > 0): ?>
                                        <?php while ($m = $messages_res->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($m['name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($m['email']); ?></small>
                                                </td>
                                                <td><?php echo nl2br(htmlspecialchars($m['message'])); ?></td>
                                                <td><small class="text-muted"><?php echo date('M d, Y', strtotime($m['created_at'])); ?></small></td>
                                                <td class="text-end">
                                                    <a href="admin_messages.php?action=delete_msg&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete message?');"><i class="fa-solid fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No messages received yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customer Feedback -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-success text-white py-3">
                            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-star me-2"></i> Customer Feedback</h5>
                        </div>
                        <div class="card-body p-0 table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Customer</th>
                                        <th>Rating</th>
                                        <th>Feedback</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($feedback_res && $feedback_res->num_rows > 0): ?>
                                        <?php while ($f = $feedback_res->fetch_assoc()): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($f['name']); ?></strong><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($f['email']); ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark fw-bold"><?php echo str_repeat('⭐', $f['rating']); ?></span>
                                                </td>
                                                <td><?php echo nl2br(htmlspecialchars($f['message'])); ?></td>
                                                <td class="text-end">
                                                    <a href="admin_messages.php?action=delete_fb&id=<?php echo $f['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete feedback?');"><i class="fa-solid fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="4" class="text-center py-4 text-muted">No feedback submitted yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
