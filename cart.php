<?php
$page_title = "Shopping Cart";
require_once 'db_connect.php';
require_once 'header.php';

// Handle quantity update via POST if submitted from cart page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_qty') {
    $product_id = (int)$_POST['product_id'];
    $new_qty = (int)$_POST['quantity'];
    
    if (isset($_SESSION['cart'][$product_id])) {
        if ($new_qty > 0) {
            $_SESSION['cart'][$product_id]['quantity'] = $new_qty;
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header("Location: cart.php");
    exit();
}

// Clear cart action
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit();
}
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa-solid fa-basket-shopping text-danger me-2"></i> Shopping Cart</h2>
            <p class="text-muted mb-0">Review your selected floral items before checkout</p>
        </div>
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <a href="cart.php?action=clear" class="btn btn-outline-secondary btn-sm rounded-pill px-3" onclick="return confirm('Are you sure you want to clear your cart?');">
                <i class="fa-solid fa-trash me-1"></i> Clear Cart
            </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Product</th>
                                    <th>Price</th>
                                    <th style="width: 140px;">Quantity</th>
                                    <th>Subtotal</th>
                                    <th class="pe-4 text-end">Remove</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total = 0;
                                foreach ($_SESSION['cart'] as $id => $item): 
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $total += $subtotal;
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="rounded-3 me-3" style="width: 70px; height: 70px; object-fit: cover;" onerror="this.src='./image/1.jpg';">
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                <small class="text-muted">Item #<?php echo $id; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold">$<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <form action="cart.php" method="POST" class="d-flex align-items-center m-0">
                                            <input type="hidden" name="action" value="update_qty">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="99" class="form-control form-control-sm text-center fw-bold me-2" onchange="this.form.submit();">
                                        </form>
                                    </td>
                                    <td class="fw-bold text-danger">$<?php echo number_format($subtotal, 2); ?></td>
                                    <td class="pe-4 text-end">
                                        <form action="remove_from_cart.php" method="POST" class="m-0">
                                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle"><i class="fa-solid fa-xmark"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <a href="shop.php" class="btn btn-outline-dark rounded-pill px-4"><i class="fa-solid fa-arrow-left me-2"></i> Continue Shopping</a>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h4 class="fw-bold mb-3">Order Summary</h4>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Items Subtotal</span>
                        <span class="fw-bold">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Standard Delivery</span>
                        <span class="text-success fw-bold">FREE</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fs-5 fw-bold">Total</span>
                        <span class="fs-4 fw-bold text-danger">$<?php echo number_format($total, 2); ?></span>
                    </div>

                    <a href="checkout.php" class="btn btn-danger w-100 py-3 rounded-pill fw-bold shadow-sm" style="background-color: var(--primary-color); border: none;">
                        Proceed to Checkout <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>

                    <div class="mt-4 text-center text-muted" style="font-size: 0.85rem;">
                        <p class="mb-1"><i class="fa-solid fa-shield-halved text-success me-1"></i> Secure Checkout Guaranteed</p>
                        <p class="mb-0"><i class="fa-solid fa-truck text-primary me-1"></i> Fresh Delivery Guarantee</p>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white shadow-sm rounded-4">
            <i class="fa-solid fa-basket-shopping text-muted fa-5x mb-3" style="opacity: 0.3;"></i>
            <h3 class="fw-bold">Your Cart is Empty</h3>
            <p class="text-muted">Looks like you haven't added any beautiful flowers to your cart yet.</p>
            <a href="shop.php" class="btn btn-danger rounded-pill px-4 py-2 mt-2 fw-bold" style="background-color: var(--primary-color); border: none;">
                Browse Flower Collection
            </a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
