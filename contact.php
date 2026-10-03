<?php
$page_title = "Contact Us";
require_once 'db_connect.php';
require_once 'header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $success = "Thank you! Your message has been sent to our team. We will get back to you shortly.";
        } else {
            $error = "Failed to send message. Please try again.";
        }
    }
}
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <span class="text-danger fw-bold text-uppercase" style="letter-spacing: 2px;">Get In Touch</span>
        <h2 class="display-5 fw-bold mt-1">Contact Petal Picks</h2>
        <p class="text-muted mx-auto" style="max-width: 600px;">Have a question about an order, custom arrangement, or event floral package? Reach out to us anytime!</p>
    </div>

    <div class="row g-4">
        <!-- Contact Info Cards -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h4 class="fw-bold mb-4">Contact Information</h4>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-danger text-white rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: var(--primary-color) !important;">
                        <i class="fa-solid fa-location-dot fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Store Address</h6>
                        <p class="text-muted mb-0">123 Flower Road, Cinnamon Gardens,<br>Colombo 07, Sri Lanka</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-danger text-white rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: var(--primary-color) !important;">
                        <i class="fa-solid fa-phone fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Phone Number</h6>
                        <p class="text-muted mb-0">+94 75 868 3533<br>+94 11 234 5678</p>
                    </div>
                </div>

                <div class="d-flex align-items-start mb-4">
                    <div class="bg-danger text-white rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: var(--primary-color) !important;">
                        <i class="fa-solid fa-envelope fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Email Address</h6>
                        <p class="text-muted mb-0">info@petalpicks.com<br>support@petalpicks.com</p>
                    </div>
                </div>

                <div class="d-flex align-items-start">
                    <div class="bg-danger text-white rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background-color: var(--primary-color) !important;">
                        <i class="fa-solid fa-clock fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Opening Hours</h6>
                        <p class="text-muted mb-0">Mon - Sat: 8:00 AM - 8:00 PM<br>Sunday: 9:00 AM - 6:00 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
                <h4 class="fw-bold mb-3">Send Us a Message</h4>
                
                <?php if ($success): ?>
                    <div class="alert alert-success rounded-3 mb-4"><i class="fa-solid fa-circle-check me-2"></i> <?php echo $success; ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="contact.php">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Your Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Your Message <span class="text-danger">*</span></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="How can we help you?" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger py-3 px-5 rounded-pill fw-bold shadow-sm" style="background-color: var(--primary-color); border: none;">
                        <i class="fa-solid fa-paper-plane me-2"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
