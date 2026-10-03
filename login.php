<?php
$page_title = "Login";
require_once 'db_connect.php';
require_once 'header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($login_input) || empty($password)) {
        $error = "Please enter both username/email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $login_input, $login_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user['username']) . "!";

                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with that username or email.";
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #2f3542, #57606f);">
                    <h3 class="fw-bold mb-0">Welcome Back</h3>
                    <p class="mb-0 text-white-50 fs-6">Log in to manage orders or access dashboard</p>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger rounded-3 mb-3"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label font-weight-bold">Username or Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Enter username or email" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 py-2 rounded-pill fw-bold shadow-sm">Log In</button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="mb-2 text-muted">Don't have an account? <a href="register.php" class="text-danger fw-bold text-decoration-none">Create Account</a></p>
                        <span class="badge bg-light text-dark border p-2 mt-2">
                            <i class="fa-solid fa-key me-1 text-warning"></i> Admin Credentials: <strong>admin</strong> | <strong>admin123</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
