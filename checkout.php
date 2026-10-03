<?php
$page_title = "Checkout";
require_once 'db_connect.php';
require_once 'header.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) == 0) {
    header("Location: shop.php");
    exit();
}

$error = '';
$order_details = null;

// Pre-fill user data if logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$pre_name = '';
$pre_email = '';

if ($user_id) {
    $u_stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $u_stmt->bind_param("i", $user_id);
    $u_stmt->execute();
    $u_res = $u_stmt->get_result();
    if ($u_res->num_rows === 1) {
        $u_data = $u_res->fetch_assoc();
        $pre_name = $u_data['username'];
        $pre_email = $u_data['email'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($email) || empty($address)) {
        $error = "Please fill in all required fields (Name, Email, Delivery Address).";
    } else {
        // Calculate total amount
        $total_amount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }

        // Begin DB Transaction
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, customer_email, customer_phone, customer_address, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("issssd", $user_id, $name, $email, $phone, $address, $total_amount);
            $stmt->execute();
            $order_id = $conn->insert_id;

            $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            $placed_items = [];

            foreach ($_SESSION['cart'] as $p_id => $item) {
                $qty = (int)$item['quantity'];
                $price = (float)$item['price'];
                $item_stmt->bind_param("iiid", $order_id, $p_id, $qty, $price);
                $item_stmt->execute();

                $placed_items[] = [
                    'name' => $item['name'],
                    'quantity' => $qty,
                    'price' => $price,
                    'subtotal' => $qty * $price
                ];
            }

            $conn->commit();

            // Store summary order details for receipt display before clearing cart
            $order_details = [
                'id' => $order_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'total' => $total_amount,
                'items' => $placed_items,
                'date' => date('Y-m-d H:i:s')
            ];

            // Clear session cart
            unset($_SESSION['cart']);

        } catch (Exception $e) {
            $conn->rollback();
            $error = "An error occurred while processing your order. Please try again.";
        }
    }
}
?>

<div class="container my-5">
    <?php if ($order_details): ?>
        <!-- Order Success Receipt Screen -->
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden text-center p-5">
                    <div class="mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-success">Order Successfully Placed!</h2>
                    <p class="text-muted fs-5">Thank you for your order. Your Order ID is <strong class="text-dark">#<?php echo $order_details['id']; ?></strong></p>

                    <div class="card border-0 bg-light rounded-4 p-4 text-start my-4">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">Order Receipt</h5>
                        <p class="mb-1"><strong>Customer Name:</strong> <?php echo htmlspecialchars($order_details['name']); ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order_details['email']); ?></p>
                        <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($order_details['phone'] ?: 'N/A'); ?></p>
                        <p class="mb-3"><strong>Delivery Address:</strong> <?php echo nl2br(htmlspecialchars($order_details['address'])); ?></p>

                        <h6 class="fw-bold border-bottom pb-2">Items Ordered:</h6>
                        <ul class="list-group list-group-flush mb-3">
                            <?php foreach ($order_details['items'] as $item): ?>
                                <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0">
                                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                                    <span class="fw-bold">$<?php echo number_format($item['subtotal'], 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between fs-5 fw-bold text-danger border-top pt-2">
                            <span>Total Paid:</span>
                            <span>$<?php echo number_format($order_details['total'], 2); ?></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="shop.php" class="btn btn-danger rounded-pill px-4" style="background-color: var(--primary-color); border: none;">Shop More Flowers</a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="my_orders.php" class="btn btn-outline-dark rounded-pill px-4">View My Orders</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Checkout Form Screen -->
        <div class="mb-4">
            <h2 class="fw-bold mb-1">Checkout</h2>
            <p class="text-muted mb-0">Enter your shipping details to complete your flower order</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger rounded-3 mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="checkout.php">
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h4 class="fw-bold mb-4"><i class="fa-solid fa-truck-ramp-box text-danger me-2"></i> Shipping & Contact Information</h4>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="John Doe" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : htmlspecialchars($pre_name); ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($pre_email); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label font-weight-bold">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+94 77 123 4567" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Delivery Address <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" rows="4" placeholder="Street address, city, postal code..." required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                        </div>

                        <div class="alert alert-info rounded-3 mt-3 mb-0" style="font-size: 0.9rem;">
                            <i class="fa-solid fa-circle-info me-1"></i> Cash on Delivery / Pay on Delivery is enabled for all local orders.
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h4 class="fw-bold mb-3">Order Summary</h4>
                        <ul class="list-group list-group-flush mb-3">
                            <?php 
                            $total = 0;
                            foreach ($_SESSION['cart'] as $item): 
                                $sub = $item['price'] * $item['quantity'];
                                $total += $sub;
                            ?>
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-2">
                                <div>
                                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <small class="text-muted d-block">Qty: <?php echo $item['quantity']; ?> x $<?php echo number_format($item['price'], 2); ?></small>
                                </div>
                                <span class="fw-bold">$<?php echo number_format($sub, 2); ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>

                        <hr>
                        <div class="d-flex justify-content-between fs-4 fw-bold text-danger mb-4">
                            <span>Total Amount</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>

                        <button type="submit" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm" style="background-color: var(--primary-color); border: none;">
                            <i class="fa-solid fa-lock me-2"></i> Confirm & Place Order
                        </button>

                        <div class="text-center mt-3">
                            <a href="cart.php" class="text-muted text-decoration-none small"><i class="fa-solid fa-arrow-left me-1"></i> Back to Cart</a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
