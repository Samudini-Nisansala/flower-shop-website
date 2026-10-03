<?php
session_start();
require_once 'db_connect.php';

// Check admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] = "Access denied. Admin privileges required.";
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';

// Handle Product Addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']);

    // Check if an image file was uploaded
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            $uploadFileDir = './image/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = $dest_path;
            }
        }
    }

    if (empty($name) || $price <= 0 || empty($image_url)) {
        $error = "Please provide valid product name, price, and image URL/file.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $name, $description, $price, $category, $image_url);
        if ($stmt->execute()) {
            $_SESSION['flash_success'] = "Product <strong>" . htmlspecialchars($name) . "</strong> added successfully!";
            header("Location: admin_products.php");
            exit();
        } else {
            $error = "Failed to add product. Please try again.";
        }
    }
}

// Handle Product Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_product') {
    $id = (int)$_POST['product_id'];
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $category = trim($_POST['category']);
    $image_url = trim($_POST['image_url']);

    // Check if new image file uploaded
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'avif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            $uploadFileDir = './image/';
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $image_url = $dest_path;
            }
        }
    }

    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssdssi", $name, $description, $price, $category, $image_url, $id);
    if ($stmt->execute()) {
        $_SESSION['flash_success'] = "Product #$id updated successfully!";
        header("Location: admin_products.php");
        exit();
    } else {
        $error = "Failed to update product.";
    }
}

// Handle Product Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $del_id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    if ($stmt->execute()) {
        $_SESSION['flash_success'] = "Product #$del_id deleted.";
    } else {
        $_SESSION['flash_error'] = "Could not delete product #$del_id.";
    }
    header("Location: admin_products.php");
    exit();
}

// Fetch all products
$products_res = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
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
            <a href="admin_products.php" class="active"><i class="fa-solid fa-spa me-2"></i> Manage Products</a>
            <a href="admin_messages.php"><i class="fa-solid fa-comments me-2"></i> Messages & Feedback</a>
            <hr class="mx-3 border-secondary">
            <a href="index.php" target="_blank"><i class="fa-solid fa-globe me-2"></i> View Website</a>
            <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Product Catalog Management</h2>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fa-solid fa-plus me-1"></i> Add New Flower Product
                </button>
            </div>

            <?php if (isset($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4"><?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger rounded-3 mb-4"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Products Table -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Description</th>
                                <th class="text-end pe-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products_res && $products_res->num_rows > 0): ?>
                                <?php while ($p = $products_res->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-3 fw-bold">#<?php echo $p['id']; ?></td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='./image/1.jpg';">
                                        </td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($p['name']); ?></td>
                                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($p['category'] ?? 'Bouquets'); ?></span></td>
                                        <td class="fw-bold text-success">$<?php echo number_format($p['price'], 2); ?></td>
                                        <td class="text-muted" style="max-width: 280px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                            <?php echo htmlspecialchars($p['description']); ?>
                                        </td>
                                        <td class="text-end pe-3">
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $p['id']; ?>">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <a href="admin_products.php?action=delete&id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal for Product -->
                                    <div class="modal fade" id="editModal<?php echo $p['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="POST" action="admin_products.php" enctype="multipart/form-data">
                                                    <input type="hidden" name="action" value="edit_product">
                                                    <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Edit Product #<?php echo $p['id']; ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Product Name</label>
                                                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($p['name']); ?>" required>
                                                        </div>
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <label class="form-label font-weight-bold">Price ($)</label>
                                                                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $p['price']; ?>" required>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="form-label font-weight-bold">Category</label>
                                                                <input type="text" name="category" class="form-control" value="<?php echo htmlspecialchars($p['category'] ?? 'Bouquets'); ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Description</label>
                                                            <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($p['description']); ?></textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Image URL</label>
                                                            <input type="text" name="image_url" class="form-control" value="<?php echo htmlspecialchars($p['image_url']); ?>" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label font-weight-bold">Or Upload New Image File</label>
                                                            <input type="file" name="image_file" class="form-control" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center py-4">No products found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="admin_products.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_product">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Flower Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Velvet Red Roses" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label font-weight-bold">Price ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="price" class="form-control" placeholder="29.99" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label font-weight-bold">Category</label>
                            <input type="text" name="category" class="form-control" placeholder="Roses, Bouquets, etc." value="Bouquets" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of bouquet..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Image Path/URL <span class="text-danger">*</span></label>
                        <input type="text" name="image_url" class="form-control" placeholder="./image/1.jpg" value="./image/1.jpg">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Or Upload Image File</label>
                        <input type="file" name="image_file" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
