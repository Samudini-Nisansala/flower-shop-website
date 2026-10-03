<?php
$page_title = "About Us";
require_once 'db_connect.php';
require_once 'header.php';

$fb_success = '';
$fb_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_feedback') {
    $fb_name = trim($_POST['name']);
    $fb_email = trim($_POST['email']);
    $fb_rating = (int)$_POST['rating'];
    $fb_message = trim($_POST['message']);

    if (empty($fb_name) || empty($fb_email) || empty($fb_message)) {
        $fb_error = "Please fill in all feedback fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (name, email, rating, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $fb_name, $fb_email, $fb_rating, $fb_message);
        if ($stmt->execute()) {
            $fb_success = "Thank you for your valuable feedback! We appreciate your support.";
        } else {
            $fb_error = "Failed to submit feedback. Please try again.";
        }
    }
}
?>

<style>
    .about-hero {
        background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('./image/12.jpg') center/cover no-repeat;
        color: white;
        padding: 80px 0;
        border-radius: 0 0 30px 30px;
        margin-bottom: 50px;
        text-align: center;
    }
    .team-img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid white;
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }
</style>

<!-- Hero Section -->
<div class="about-hero">
    <div class="container">
        <h1 class="display-4 fw-bold mb-2">About Petal Picks Boutique</h1>
        <p class="lead text-white-50">Crafting emotion into fresh floral art since 2020.</p>
    </div>
</div>

<div class="container mb-5">
    <!-- Story Section -->
    <div class="row align-items-center mb-5 g-4">
        <div class="col-lg-6">
            <img src="./image/pexels-goran-vrakela-64248-230292.jpg" alt="About Our Store" class="img-fluid rounded-4 shadow-sm" onerror="this.src='./image/1.jpg';">
        </div>
        <div class="col-lg-6">
            <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Our Passion & Vision</span>
            <h2 class="display-6 fw-bold mt-1 mb-3">Freshness, Beauty & Love Delivered Daily</h2>
            <p class="text-muted leading-relaxed">
                At Petal Picks, we believe every flower tells a story. From expressing gratitude and heartfelt love to celebrating grand achievements and unforgettable weddings, flowers bring color and warmth to life's most meaningful milestones.
            </p>
            <p class="text-muted leading-relaxed">
                We partner with local floral growers and sustainable flower farms across Sri Lanka to ensure each bloom is harvested at peak fresh quality. Our master florists then handcraft each arrangement with love, care, and attention to detail.
            </p>
        </div>
    </div>

    <!-- Team Section -->
    <div class="text-center my-5">
        <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Meet The Artisans</span>
        <h2 class="display-6 fw-bold mt-1 mb-4">Our Creative Team</h2>
        
        <div class="row g-4 justify-content-center">
            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white">
                    <div>
                        <img src="./image/777.jpg" alt="Anurada" class="team-img" onerror="this.src='./image/1.jpg';">
                    </div>
                    <h5 class="fw-bold mb-1">Ms. Anurada</h5>
                    <p class="text-danger fw-semibold mb-2">Lead Floral Designer</p>
                    <p class="text-muted small">Specializes in bridal bouquets, pastel aesthetics, and luxury event floral arrangements.</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white">
                    <div>
                        <img src="./image/888.jpg" alt="Safana" class="team-img" onerror="this.src='./image/1.jpg';">
                    </div>
                    <h5 class="fw-bold mb-1">Ms. Safana</h5>
                    <p class="text-danger fw-semibold mb-2">Customer Relations Specialist</p>
                    <p class="text-muted small">Dedicated to ensuring seamless order fulfillment, customized gifts, and client happiness.</p>
                </div>
            </div>

            <div class="col-md-4 col-sm-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white">
                    <div>
                        <img src="./image/444.jpg" alt="Perera" class="team-img" onerror="this.src='./image/1.jpg';">
                    </div>
                    <h5 class="fw-bold mb-1">Mr. Perera</h5>
                    <p class="text-danger fw-semibold mb-2">Operations & Logistics Manager</p>
                    <p class="text-muted small">Oversees fresh supply chain logistics and guarantees temperature-controlled express deliveries.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Feedback Section -->
    <div class="row justify-content-center mt-5">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">We Value Your Feedback</h3>
                    <p class="text-muted">Let us know how your experience with Petal Picks was!</p>
                </div>

                <?php if ($fb_success): ?>
                    <div class="alert alert-success rounded-3 mb-4"><i class="fa-solid fa-circle-check me-2"></i> <?php echo $fb_success; ?></div>
                <?php endif; ?>

                <?php if ($fb_error): ?>
                    <div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $fb_error; ?></div>
                <?php endif; ?>

                <form method="POST" action="about.php">
                    <input type="hidden" name="action" value="submit_feedback">
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Your Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Rating</label>
                        <select name="rating" class="form-select">
                            <option value="5" selected>⭐⭐⭐⭐⭐ (5/5) Excellent</option>
                            <option value="4">⭐⭐⭐⭐ (4/5) Very Good</option>
                            <option value="3">⭐⭐⭐ (3/5) Good</option>
                            <option value="2">⭐⭐ (2/5) Fair</option>
                            <option value="1">⭐ (1/5) Poor</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Your Feedback <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="4" placeholder="Share your experience with us..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm" style="background-color: var(--primary-color); border: none;">
                        Submit Feedback
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>