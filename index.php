<?php
$page_title = "Home - Fresh Handcrafted Flowers";
require_once 'db_connect.php';
require_once 'header.php';

// Fetch top 6 products from database
$sql = "SELECT * FROM products ORDER BY id ASC LIMIT 6";
$result = $conn->query($sql);
?>

<style>
    .hero-banner {
        background: linear-gradient(rgba(0, 0, 0, 0.45), rgba(0, 0, 0, 0.45)), url('./image/9.jpg') center/cover no-repeat;
        color: white;
        padding: 120px 0 100px;
        border-radius: 0 0 50px 50px;
        margin-bottom: 50px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.4);
    }

    .hero-title {
        font-size: 3.8rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        font-weight: 300;
        max-width: 600px;
        margin: 20px auto 30px;
    }

    .btn-hero {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
        padding: 14px 38px;
        border-radius: 40px;
        font-size: 1.1rem;
        border: none;
        box-shadow: 0 6px 20px rgba(255, 107, 129, 0.4);
        transition: all 0.3s ease;
    }

    .btn-hero:hover {
        background-color: var(--primary-hover);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(255, 107, 129, 0.5);
    }

    .feature-card {
        background: white;
        padding: 30px 20px;
        border-radius: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.04);
        transition: transform 0.3s ease;
        text-align: center;
    }

    .feature-card:hover {
        transform: translateY(-8px);
    }

    .feature-icon {
        width: 60px;
        height: 60px;
        background-color: var(--light-bg);
        color: var(--primary-color);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 20px;
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
        height: 260px;
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

<!-- Hero Section -->
<section class="hero-banner text-center">
    <div class="container">
        <h1 class="hero-title">Handcrafted Blooms<br>For Every Moment</h1>
        <p class="hero-subtitle">Express love, celebration, and joy with fresh, premium floral arrangements delivered straight to your door.</p>
        <a href="shop.php" class="btn btn-hero mt-2"><i class="fa-solid fa-store me-2"></i> Explore Shop</a>
    </div>
</section>

<!-- Features Section -->
<div class="container mb-5">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-leaf"></i></div>
                <h4 class="fw-bold">100% Fresh Flowers</h4>
                <p class="text-muted mb-0">Locally sourced blooms, carefully selected daily by our master florists.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <h4 class="fw-bold">Same-Day Express Delivery</h4>
                <p class="text-muted mb-0">Fast and careful delivery to ensure your bouquets arrive blooming fresh.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-card">
                <div class="feature-icon"><i class="fa-solid fa-heart"></i></div>
                <h4 class="fw-bold">Bespoke Designs</h4>
                <p class="text-muted mb-0">Custom arrangements tailored for weddings, corporate events, and gifts.</p>
            </div>
        </div>
    </div>
</div>

<!-- Featured Products Section -->
<div class="container my-5">
    <div class="text-center mb-5">
        <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Handpicked Selection</span>
        <h2 class="display-5 fw-bold mt-1">Featured Flower Bouquets</h2>
        <div class="mx-auto" style="width: 80px; height: 3px; background-color: var(--primary-color);"></div>
    </div>

    <div class="row g-4">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
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
                <?php
            }
        } else {
            echo "<div class='col-12 text-center text-muted py-4'>No products currently available.</div>";
        }
        ?>
    </div>

    <div class="text-center mt-5">
        <a href="shop.php" class="btn btn-outline-danger btn-lg rounded-pill px-5 fw-bold" style="border-width: 2px;">
            View Entire Collection <i class="fa-solid fa-arrow-right ms-2"></i>
        </a>
    </div>
</div>

<!-- Banner Callout -->
<div class="container my-5">
    <div class="bg-dark text-white rounded-5 p-5 position-relative overflow-hidden shadow-lg" style="background: linear-gradient(135deg, #2c3e50, #000000);">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h2 class="display-6 fw-bold mb-3">Planning a Wedding or Corporate Event?</h2>
                <p class="lead text-white-50 mb-4">Our expert floral stylists create customized packages designed to suit your aesthetic vision and budget.</p>
                <a href="services.php" class="btn btn-danger rounded-pill px-4 py-2 fw-bold" style="background-color: var(--primary-color); border: none;">Discover Our Services</a>
            </div>
            <div class="col-lg-4 text-center mt-4 mt-lg-0">
                <i class="fa-solid fa-wand-magic-sparkles text-warning fa-6x" style="opacity: 0.8;"></i>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
