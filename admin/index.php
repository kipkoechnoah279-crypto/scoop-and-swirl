<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: index.php');
    exit();
}

$login_error = '';
if (isset($_POST['login'])) {
    $username_input = trim($_POST['username']);
    $password_input = trim($_POST['password']);

    $admin_user = 'admin';
    $admin_pass = 'admin123';

    if ($username_input === $admin_user && $password_input === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit();
    } else {
        $login_error = 'Invalid username or password.';
    }
}

$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

$host = 'localhost';
$dbname = 'scoop_swirl_db';
$username = 'root';
$password = '';
$pdo = null;
$product_error = '';

if ($is_logged_in) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("<div style='padding:2rem; font-family:sans-serif; color:red;'><h2>Database Connection Error</h2>" . $e->getMessage() . "</div>");
    }

    if (isset($_POST['add_product'])) {
        $title       = trim($_POST['title']);
        $category    = $_POST['category'];
        $price       = (float)$_POST['price'];
        $tag         = trim($_POST['tag']);
        $description = trim($_POST['description']);
        $image_url   = trim($_POST['image_url'] ?? '');
        
        $image_path  = '';

        // Option 1: File Upload from Device
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['product_image']['tmp_name'];
            $file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9._-]/", "", basename($_FILES['product_image']['name']));
            
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $target_file = $upload_dir . $file_name;
            $file_ext    = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed_ext)) {
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $image_path = 'uploads/' . $file_name;
                } else {
                    $product_error = 'Failed to move uploaded file.';
                }
            } else {
                $product_error = 'Invalid file type. Only JPG, JPEG, PNG, and WEBP are allowed.';
            }
        } 
        // Option 2: Pasted Image URL
        elseif (!empty($image_url)) {
            $image_path = $image_url;
        } else {
            $product_error = 'Please either upload an image from your device or provide a valid image URL.';
        }

        if (empty($product_error) && !empty($image_path)) {
            $stmt = $pdo->prepare("INSERT INTO products (title, category, price, tag, description, image) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $category, $price, $tag, $description, $image_path]);
            header('Location: index.php');
            exit();
        }
    }

    if (isset($_GET['delete_product'])) {
        $id = (int)$_GET['delete_product'];
        $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $prod = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Delete local upload file if it exists locally
        if ($prod && !empty($prod['image']) && strpos($prod['image'], 'uploads/') === 0 && file_exists('../' . $prod['image'])) {
            @unlink('../' . $prod['image']);
        }

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php');
        exit();
    }

    if (isset($_GET['update_status'], $_GET['order_id'])) {
        $new_status = $_GET['update_status'];
        $order_id = (int)$_GET['order_id'];
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        header('Location: index.php');
        exit();
    }

    $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

    $total_revenue = 0;
    foreach ($orders as $o) {
        $total_revenue += (float)($o['total_amount'] ?? 0);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Scoop & Swirl</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/main.js" defer></script>
</head>
<body>

<?php if (!$is_logged_in): ?>
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 1rem;">
        <div class="card" style="width: 100%; max-width: 24rem; padding: 2rem;">
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <div class="admin-icon" style="margin: 0 auto 0.75rem auto; width: 48px; height: 48px; font-size: 1.5rem;">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h1 style="font-size: 1.25rem; font-weight: bold; color: #4A2C2A;">Admin Authentication</h1>
                <p style="font-size: 0.75rem; color: #94A3B8; margin-top: 0.25rem;">Sign in to access the management portal</p>
            </div>

            <?php if (!empty($login_error)): ?>
                <div style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; padding: 0.75rem; border-radius: 0.75rem; font-size: 0.75rem; margin-bottom: 1rem; text-align: center; font-weight: bold;">
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" required placeholder="username" class="form-input">
                </div>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="passwordInput" name="password" required placeholder="password" class="form-input">
                        <button type="button" id="togglePasswordBtn" class="password-toggle-btn">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" name="login" class="btn-submit">Sign In</button>
            </form>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="../index.php" style="font-size: 0.75rem; color: #64748B; text-decoration: none; font-weight: bold;">
                    <i class="fa-solid fa-arrow-left"></i> Back to Store
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <header class="admin-header">
        <div class="admin-brand">
            <div class="admin-icon">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <div>
                <h1 style="font-size: 1.15rem; font-weight: bold; line-height: 1.1;">Scoop & Swirl</h1>
                <span style="font-size: 0.6rem; color: #FF7597; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em;">Management Console</span>
            </div>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="../index.php" style="background: rgba(255,255,255,0.1); color: white; padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: bold; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-store"></i> Store
            </a>
            <a href="index.php?logout=1" style="background: #EF4444; color: white; padding: 0.5rem 1rem; border-radius: 0.75rem; font-size: 0.75rem; font-weight: bold; text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </div>
    </header>

    <div class="admin-container">
        <div class="metrics-grid">
            <div class="card" style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 1rem; background: #FFF0F4; color: #FF7597; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div>
                    <span style="font-size: 0.7rem; color: #94A3B8; font-weight: bold; text-transform: uppercase;">Total Products</span>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #4A2C2A; margin: 0;"><?php echo count($products); ?></p>
                </div>
            </div>
            <div class="card" style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 1rem; background: #FFFBEB; color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <span style="font-size: 0.7rem; color: #94A3B8; font-weight: bold; text-transform: uppercase;">Total Orders</span>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #4A2C2A; margin: 0;"><?php echo count($orders); ?></p>
                </div>
            </div>
            <div class="card" style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 1rem; background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <span style="font-size: 0.7rem; color: #94A3B8; font-weight: bold; text-transform: uppercase;">Gross Revenue</span>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #4A2C2A; margin: 0;">KSh <?php echo number_format($total_revenue, 2); ?></p>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card" style="height: fit-content;">
                <div style="margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.25rem; font-weight: bold; color: #4A2C2A;">Add New Menu Item</h2>
                    <p style="font-size: 0.75rem; color: #94A3B8;">Upload from device or paste an image URL.</p>
                </div>

                <?php if (!empty($product_error)): ?>
                    <div style="background: #FEF2F2; border: 1px solid #FCA5A5; color: #DC2626; padding: 0.75rem; border-radius: 0.75rem; font-size: 0.75rem; margin-bottom: 1rem; font-weight: bold;">
                        <?php echo $product_error; ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">Product Title</label>
                        <input type="text" name="title" required placeholder="e.g. Vanilla Bean Sundae" class="form-input">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select">
                                <option value="Ice Cream">Ice Cream</option>
                                <option value="Milkshakes">Milkshakes</option>
                                <option value="Mala">Mala</option>
                                <option value="Yogurt">Yogurt</option>
                                <option value="Dairy">Dairy</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Price (KSh)</label>
                            <input type="number" step="0.01" name="price" required placeholder="450.00" class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Badge Tag</label>
                        <input type="text" name="tag" placeholder="e.g. Bestseller, Probiotic" class="form-input">
                    </div>

                    <!-- Dual Image Input Option -->
                    <div class="form-group">
                        <label class="form-label">Product Image (Choose File OR Paste URL)</label>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <input type="file" name="product_image" accept="image/png, image/jpeg, image/jpg, image/webp" class="form-input" style="padding: 0.45rem;">
                            <span style="font-size: 0.65rem; color: #94A3B8; text-align: center; font-weight: bold;">— OR —</span>
                            <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" required placeholder="Flavor notes, ingredients..." class="form-textarea"></textarea>
                    </div>

                    <button type="submit" name="add_product" class="btn-submit">
                        <i class="fa-solid fa-plus"></i> Add Product
                    </button>
                </form>
            </div>

            <div class="card">
                <div style="margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.25rem; font-weight: bold; color: #4A2C2A;">Product Inventory</h2>
                    <p style="font-size: 0.75rem; color: #94A3B8;">View and manage existing catalog items.</p>
                </div>

                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 2rem; color: #94A3B8;">No products in catalog.</td>
                                </tr>
                            <?php else: foreach ($products as $p): 
                                $img_src = (strpos($p['image'], 'http') === 0) ? $p['image'] : '../' . $p['image'];
                            ?>
                                <tr>
                                    <td style="display: flex; align-items: center; gap: 0.75rem;">
                                        <img src="<?php echo htmlspecialchars($img_src); ?>" style="width: 40px; height: 40px; border-radius: 0.5rem; object-fit: cover;" alt="">
                                        <div>
                                            <span style="font-weight: bold; color: #4A2C2A; display: block;"><?php echo htmlspecialchars($p['title']); ?></span>
                                            <span style="font-size: 0.65rem; color: #94A3B8;"><?php echo htmlspecialchars($p['tag']); ?></span>
                                        </div>
                                    </td>
                                    <td style="font-weight: 600; color: #64748B;"><?php echo htmlspecialchars($p['category']); ?></td>
                                    <td style="font-weight: 800; color: #4A2C2A;">KSh <?php echo number_format($p['price'], 2); ?></td>
                                    <td style="text-align: right;">
                                        <a href="index.php?delete_product=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure you want to remove this item?');" class="action-link action-delete">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="card">
            <div style="margin-bottom: 1.25rem;">
                <h2 style="font-size: 1.25rem; font-weight: bold; color: #4A2C2A;">Customer Orders</h2>
                <p style="font-size: 0.75rem; color: #94A3B8;">Track and fulfill customer orders.</p>
            </div>

            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer & Contact</th>
                            <th>Delivery & Payment</th>
                            <th>Ordered Products</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: #94A3B8;">No customer orders placed yet.</td>
                            </tr>
                        <?php else: foreach ($orders as $o): ?>
                            <tr>
                                <td style="font-weight: bold; color: #4A2C2A;">#<?php echo $o['id']; ?></td>
                                <td>
                                    <span style="font-weight: bold; color: #334155; display: block;"><?php echo htmlspecialchars($o['customer_name'] ?? 'Guest Customer'); ?></span>
                                    <span style="font-size: 0.65rem; color: #64748B; display: block;"><?php echo htmlspecialchars($o['phone_number'] ?? 'N/A'); ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 0.7rem; color: #334155; display: block;"><i class="fa-solid fa-location-dot text-brand-pink"></i> <?php echo htmlspecialchars($o['address'] ?? 'N/A'); ?></span>
                                    <span style="font-size: 0.65rem; color: #0284C7; font-weight: bold;"><i class="fa-solid fa-wallet"></i> <?php echo htmlspecialchars($o['payment_method'] ?? 'M-Pesa'); ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 0.75rem; color: #475569; font-weight: 500;">
                                        <?php echo htmlspecialchars($o['items'] ?? 'Item details unavailable'); ?>
                                    </span>
                                </td>
                                <td style="font-weight: 800; color: #4A2C2A;">KSh <?php echo number_format($o['total_amount'] ?? 0, 2); ?></td>
                                <td>
                                    <span class="badge-status <?php echo ($o['status'] ?? 'Pending') === 'Completed' ? 'status-completed' : 'status-pending'; ?>">
                                        <?php echo htmlspecialchars($o['status'] ?? 'Pending'); ?>
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="index.php?order_id=<?php echo $o['id']; ?>&update_status=Completed" class="action-link action-complete">Complete</a>
                                    <a href="index.php?order_id=<?php echo $o['id']; ?>&update_status=Pending" class="action-link action-pending">Pending</a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

</body>
</html>