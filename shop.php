<?php
$page_title = "Shop Flowers";
require_once 'db_connect.php';
require_once 'header.php';

// Search and Category filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR description LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

if (!empty($category) && $category !== 'All') {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$query .= " ORDER BY id ASC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Get unique categories for filter
$cat_res = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''");
$categories = [];
while ($row = $cat_res->fetch_assoc()) {
    $categories[] = $row['category'];
}
?>

<style>
    .shop-header {
        background: linear-gradient(rgba(47, 53, 66, 0.8), rgba(47, 53, 66, 0.8)), url('./image/12.jpg') center/cover no-repeat;
        color: white;
        padding: 60px 0;
        border-radius: 0 0 30px 30px;
        margin-bottom: 40px;
    }

    .filter-btn {
        border-radius: 20px;
        padding: 6px 18px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .product-card {
        background: white;
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }

    .product-img-wrapper {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .product-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-img-wrapper img {
        transform: scale(1.08);
    }

    .badge-category {
        position: absolute;
        top: 15px;
        left: 15px;
        background-color: rgba(255, 255, 255, 0.9);
        color: var(--dark-color);
        font-size: 0.8rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 20px;
    }

    .product-body {
        padding: 22px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .product-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 8px;
    }

    .product-desc {
        color: var(--text-muted);
        font-size: 0.9rem;
        flex-grow: 1;
        margin-bottom: 15px;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .btn-add-cart {
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-add-cart:hover {
        background-color: var(--primary-hover);
        color: white;
        transform: scale(1.02);
    }
</style>

<!-- Header Banner -->
<div class="shop-header text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-2">Our Full Flower Collection</h1>
        <p class="lead mb-0 text-white-50">Handcrafted bouquets and floral designs for every emotion and event.</p>
    </div>
</div>

<div class="container mb-5">
    <!-- Search & Filter Controls -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-md-6">
            <form method="GET" action="shop.php" class="d-flex gap-2">
                <?php if (!empty($category)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <?php endif; ?>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search flowers..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-danger px-4" style="background-color: var(--primary-color); border: none;">Search</button>
                </div>
            </form>
        </div>

        <div class="col-md-6 text-md-end">
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <a href="shop.php?search=<?php echo urlencode($search); ?>" class="btn <?php echo (empty($category) || $category === 'All') ? 'btn-danger' : 'btn-outline-secondary'; ?> filter-btn" style="<?php echo (empty($category) || $category === 'All') ? 'background-color: var(--primary-color); border: none;' : ''; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a href="shop.php?category=<?php echo urlencode($cat); ?>&search=<?php echo urlencode($search); ?>" class="btn <?php echo ($category === $cat) ? 'btn-danger' : 'btn-outline-secondary'; ?> filter-btn" style="<?php echo ($category === $cat) ? 'background-color: var(--primary-color); border: none;' : ''; ?>">
                        <?php echo htmlspecialchars($cat); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="row g-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="product-card">
                        <div class="product-img-wrapper">
                            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.src='./image/1.jpg';">
                            <span class="badge-category"><?php echo htmlspecialchars($row['category'] ?? 'Bouquets'); ?></span>
                        </div>
                        <div class="product-body">
                            <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="product-desc"><?php echo htmlspecialchars($row['description']); ?></p>
                            
                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                <span class="product-price">$<?php echo number_format($row['price'], 2); ?></span>
                                <form action="add_to_cart.php" method="POST" class="m-0">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-add-cart">
                                        <i class="fa-solid fa-cart-plus me-1"></i> Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-seedling text-muted fa-4x mb-3" style="opacity: 0.4;"></i>
                <h4>No Flowers Found</h4>
                <p class="text-muted">No products matched your search parameters. Try searching for something else or clear filters.</p>
                <a href="shop.php" class="btn btn-outline-danger rounded-pill px-4">Reset Filters</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
