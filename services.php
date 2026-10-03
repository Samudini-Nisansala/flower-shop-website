<?php
$page_title = "Wedding & Special Floral Services";
require_once 'db_connect.php';
require_once 'header.php';

$w_success = '';
$w_error = '';

// Handle Wedding Consultation Quote Inquiry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'wedding_inquiry') {
    $c_name = trim($_POST['name']);
    $c_email = trim($_POST['email']);
    $c_phone = trim($_POST['phone']);
    $style = trim($_POST['bouquet_style']);
    $flowers = isset($_POST['flowers']) ? implode(', ', $_POST['flowers']) : 'Not specified';
    $wedding_date = trim($_POST['wedding_date']);
    $notes = trim($_POST['notes']);

    if (empty($c_name) || empty($c_email) || empty($c_phone)) {
        $w_error = "Please fill in your contact information (Name, Email, Phone).";
    } else {
        $full_message = "WEDDING BOUQUET CONSULTATION INQUIRY:\n"
                      . "Style: " . $style . "\n"
                      . "Preferred Flowers: " . $flowers . "\n"
                      . "Wedding Date: " . $wedding_date . "\n"
                      . "Phone: " . $c_phone . "\n"
                      . "Notes: " . $notes;

        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $c_name, $c_email, $full_message);
        if ($stmt->execute()) {
            $w_success = "Thank you! Your Wedding Bouquet Inquiry has been received. Our senior floral stylist will contact you via phone/email shortly.";
        } else {
            $w_error = "Failed to submit inquiry. Please try again.";
        }
    }
}

// Fetch Sample Wedding Bouquets from Products Catalog
$wedding_products_res = $conn->query("SELECT * FROM products WHERE category = 'Wedding Bouquets' ORDER BY id ASC");
?>

<style>
    .services-hero {
        background: linear-gradient(rgba(47, 53, 66, 0.75), rgba(47, 53, 66, 0.75)), url('./image/DEEEEEE.avif') center/cover no-repeat;
        color: white;
        padding: 90px 0;
        border-radius: 0 0 40px 40px;
        margin-bottom: 50px;
        text-align: center;
    }

    .flower-material-card {
        background: white;
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        transition: transform 0.3s ease;
        height: 100%;
        text-align: center;
    }

    .flower-material-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .flower-material-icon {
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background-color: var(--light-bg);
        color: var(--primary-color);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 15px;
    }

    .wedding-sample-card {
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

    .wedding-sample-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }

    .wedding-sample-img {
        width: 100%;
        height: 260px;
        object-fit: cover;
    }

    .badge-style {
        position: absolute;
        top: 15px;
        right: 15px;
        background-color: rgba(47, 53, 66, 0.9);
        color: #f1c40f;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 5px 14px;
        border-radius: 20px;
    }
</style>

<!-- Hero Section -->
<div class="services-hero">
    <div class="container">
        <span class="badge bg-danger rounded-pill px-3 py-2 mb-3 text-uppercase fw-bold" style="letter-spacing: 2px;">Bridal Floral Studio</span>
        <h1 class="display-4 fw-bold mb-2">Wedding Bouquets & Event Styling</h1>
        <p class="lead text-white-50 mx-auto" style="max-width: 700px;">Explore handcrafted Traditional Sri Lankan & Western bridal bouquets, premium floral materials, and bespoke wedding design packages.</p>
    </div>
</div>

<div class="container mb-5">
    
    <!-- Section 1: Flowers Used to Make Wedding Bouquets -->
    <div class="text-center mb-5">
        <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Wedding Flora Guide</span>
        <h2 class="display-5 fw-bold mt-1">Flowers Used for Wedding Bouquets</h2>
        <p class="text-muted mx-auto" style="max-width: 650px;">We use only the finest fresh blooms and scented fillers tailored to traditional Kandyan / Poruwa ceremonies and modern Western weddings.</p>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-rose"></i></div>
                <h5 class="fw-bold">White & Red Roses</h5>
                <p class="text-muted small mb-0">The quintessential romantic flower used in both Western cascading bouquets and Traditional Poruwa bouquets.</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-spa"></i></div>
                <h5 class="fw-bold">Lotus & Water Lily (Nelum / Nil Manel)</h5>
                <p class="text-muted small mb-0">Sacred pink lotuses and blue water lilies essential for Traditional Kandyan & Poruwa Sri Lankan bridal bouquets.</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-seedling"></i></div>
                <h5 class="fw-bold">Jasmine & Pitchcha</h5>
                <p class="text-muted small mb-0">Fragrant white Jasmine blossoms used for bridal garlands, hair accessories, and delicate bouquet accents.</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-fan"></i></div>
                <h5 class="fw-bold">Baby's Breath & Eucalyptus</h5>
                <p class="text-muted small mb-0">Gypsophila baby's breath and silver eucalyptus leaves for popular Western Boho & rustic bridal bouquets.</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-gem"></i></div>
                <h5 class="fw-bold">Calla Lilies & Orchids</h5>
                <p class="text-muted small mb-0">High-end luxury tropical blooms ideal for sleek Western posy and dramatic cascading wedding arrangements.</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-heart"></i></div>
                <h5 class="fw-bold">Red & Pink Anthuriums</h5>
                <p class="text-muted small mb-0">Vibrant heart-shaped tropical anthuriums perfect for bold contemporary island wedding themes.</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-sun"></i></div>
                <h5 class="fw-bold">Tulips & Carnations</h5>
                <p class="text-muted small mb-0">Soft pastel tulips and ruffled carnations creating lush volume and elegance in Western bridal posies.</p>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="flower-material-card">
                <div class="flower-material-icon"><i class="fa-solid fa-ribbon"></i></div>
                <h5 class="fw-bold">Gold Ribbons & Silk Wraps</h5>
                <p class="text-muted small mb-0">Traditional gold lace trims for Sri Lankan weddings & pure white ivory silk ribbons for Western styles.</p>
            </div>
        </div>
    </div>

    <hr class="my-5">

    <!-- Section 2: Sample Wedding Bouquets Showcase -->
    <div class="text-center mb-5">
        <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Bridal Portfolio</span>
        <h2 class="display-5 fw-bold mt-1">Sample Wedding Bouquets</h2>
        <p class="text-muted mx-auto" style="max-width: 650px;">Choose from our signature Traditional Sri Lankan & Western bridal bouquet designs or request a custom order.</p>
    </div>

    <div class="row g-4 mb-5">
        <?php if ($wedding_products_res && $wedding_products_res->num_rows > 0): ?>
            <?php while ($w_product = $wedding_products_res->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="wedding-sample-card position-relative">
                        <span class="badge-style"><i class="fa-solid fa-crown me-1"></i> Wedding Edition</span>
                        <img src="<?php echo htmlspecialchars($w_product['image_url']); ?>" alt="<?php echo htmlspecialchars($w_product['name']); ?>" class="wedding-sample-img" onerror="this.src='./image/1.jpg';">
                        
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h4 class="fw-bold mb-2 text-dark"><?php echo htmlspecialchars($w_product['name']); ?></h4>
                            <p class="text-muted fs-6 flex-grow-1"><?php echo htmlspecialchars($w_product['description']); ?></p>
                            
                            <div class="d-flex align-items-center justify-content-between mt-3 pt-3 border-top">
                                <span class="fs-4 fw-bold text-danger">$<?php echo number_format($w_product['price'], 2); ?></span>
                                <form action="add_to_cart.php" method="POST" class="m-0">
                                    <input type="hidden" name="product_id" value="<?php echo $w_product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold" style="background-color: var(--primary-color); border: none;">
                                        <i class="fa-solid fa-cart-plus me-1"></i> Order Package
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <!-- Section 3: Custom Wedding Bouquet Inquiry Form -->
    <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
            <div class="card border-0 shadow-lg rounded-5 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Custom Bridal Order</span>
                    <h3 class="display-6 fw-bold mt-1">Book Your Wedding Bouquet Consultation</h3>
                    <p class="text-muted">Tell us your wedding theme, preferred flowers, and date. We will design the bouquet of your dreams!</p>
                </div>

                <?php if ($w_success): ?>
                    <div class="alert alert-success rounded-3 mb-4"><i class="fa-solid fa-circle-check me-2"></i> <?php echo $w_success; ?></div>
                <?php endif; ?>

                <?php if ($w_error): ?>
                    <div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $w_error; ?></div>
                <?php endif; ?>

                <form method="POST" action="services.php">
                    <input type="hidden" name="action" value="wedding_inquiry">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Bride / Groom Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Kasun & Dinithi" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="bride@example.com" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" placeholder="+94 77 123 4567" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Preferred Bouquet Style</label>
                            <select name="bouquet_style" class="form-select">
                                <option value="Traditional Kandyan / Poruwa (Lotus, Jasmine & Gold)">Traditional Kandyan / Poruwa Style (Nelum, Jasmine & Gold)</option>
                                <option value="Western Royal Cascading (White Roses, Calla Lilies & Ribbon)">Western Royal Cascading Style (White Roses, Calla Lilies)</option>
                                <option value="Modern Boho Western (Blush Roses, Eucalyptus & Pampas)">Modern Boho Western Style (Blush Roses, Pampas)</option>
                                <option value="Minimalist Western White Posy (Tulips & Satin Ribbon)">Minimalist Western White Posy Style (White Tulips)</option>
                                <option value="Bridesmaid Matching Set">Bridesmaid Matching Set</option>
                                <option value="Full Wedding Floral Package (Bouquets + Venue Decor)">Full Wedding Package (Bouquets + Venue Styling)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Target Wedding Date</label>
                            <input type="date" name="wedding_date" class="form-control">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Select Preferred Flowers (Check all that apply):</label>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="flowers[]" value="White & Red Roses" id="f1" checked>
                                <label class="form-check-label" for="f1">Roses (White/Red/Blush)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="flowers[]" value="Lotus (Nelum)" id="f2" checked>
                                <label class="form-check-label" for="f2">Lotus (Nelum)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="flowers[]" value="Water Lily (Nil Manel/Olu)" id="f3">
                                <label class="form-check-label" for="f3">Nil Manel / Olu</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="flowers[]" value="Jasmine / Pitchcha" id="f4" checked>
                                <label class="form-check-label" for="f4">Jasmine (Pitchcha)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="flowers[]" value="Baby's Breath & Eucalyptus" id="f5">
                                <label class="form-check-label" for="f5">Baby's Breath & Eucalyptus</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="flowers[]" value="Orchids & Calla Lilies" id="f6">
                                <label class="form-check-label" for="f6">Orchids / Calla Lilies</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Special Requests & Theme Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Mention color palette, venue location, or specific arrangement ideas..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm" style="background-color: var(--primary-color); border: none;">
                        <i class="fa-solid fa-heart me-2"></i> Submit Wedding Consultation Request
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

<?php require_once 'footer.php'; ?>