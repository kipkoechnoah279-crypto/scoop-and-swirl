<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$dbname = 'scoop_swirl_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart action (AJAX & direct)
if (isset($_GET['action']) && $_GET['action'] === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    if ($product_id > 0) {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product['id'],
                    'title' => $product['title'],
                    'price' => $product['price'],
                    'image' => $product['image'],
                    'quantity' => 1
                ];
            }
        }
    }

    $cart_count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
        exit();
    }

    $redirect_url = isset($_GET['redirect']) ? $_GET['redirect'] : 'menu.php';
    header('Location: ' . $redirect_url);
    exit();
}

// Handle Remove Item
if (isset($_GET['action']) && $_GET['action'] === 'remove') {
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: cart.php');
    exit();
}

// Handle Express Checkout Submission
$checkout_success = false;
$order_error = '';

if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
    $customer_name   = trim($_POST['customer_name']);
    $phone_number    = trim($_POST['phone_number']);
    $address         = trim($_POST['address']);
    $payment_method  = trim($_POST['payment_method'] ?? 'M-Pesa');
    
    $total_amount = 0;
    $items_summary = [];
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
        $items_summary[] = $item['quantity'] . 'x ' . $item['title'];
    }
    $items_string = implode(', ', $items_summary);

    try {
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, phone_number, address, payment_method, items, total_amount, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW())");
        $stmt->execute([$customer_name, $phone_number, $address, $payment_method, $items_string, $total_amount]);
        
        $_SESSION['cart'] = [];
        $checkout_success = true;
    } catch (PDOException $e) {
        $order_error = "Checkout failed: " . $e->getMessage();
    }
}

$cart_count = 0;
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_count += $item['quantity'];
    $subtotal += $item['price'] * $item['quantity'];
}

include 'includes/header.php';
?>

<main class="container-main flex-grow w-full">
    <div class="mb-6">
        <span class="text-brand-pink font-extrabold text-xs uppercase tracking-widest">Review Order</span>
        <h1 class="font-serif-title text-3xl font-bold text-brand-mocha mt-1">Your Shopping Cart</h1>
    </div>

    <?php if (!empty($order_error)): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl text-center mb-6 text-xs font-bold">
            <?php echo htmlspecialchars($order_error); ?>
        </div>
    <?php endif; ?>

    <?php if ($checkout_success): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-8 rounded-3xl text-center mb-8 max-w-2xl mx-auto">
            <i class="fa-solid fa-circle-check text-5xl text-emerald-600 mb-4 block"></i>
            <h2 class="font-serif-title text-2xl font-bold">Order Placed Successfully!</h2>
            <p class="text-xs mt-2">Thank you for ordering with Scoop & Swirl. Your cold delivery is on its way!</p>
            <a href="menu.php" class="inline-block mt-6 bg-brand-pink text-white px-6 py-3 rounded-2xl font-bold text-xs">Continue Shopping</a>
        </div>
    <?php elseif (empty($_SESSION['cart'])): ?>
        <div class="bg-white border border-slate-200 p-16 rounded-3xl text-center shadow-sm max-w-2xl mx-auto">
            <div class="w-16 h-16 bg-brand-pinklight text-brand-pink rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                <i class="fa-solid fa-bag-shopping"></i>
            </div>
            <h2 class="font-serif-title text-xl font-bold text-brand-mocha">Your cart is empty</h2>
            <p class="text-xs text-slate-500 mt-2 mb-6">Explore our delicious dairy catalog and add some treats!</p>
            <a href="menu.php" class="bg-brand-pink text-white px-6 py-3 rounded-2xl font-bold text-xs">Browse Menu</a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5 bg-white border border-slate-200 rounded-3xl p-6 shadow-sm">
                <h2 class="font-serif-title text-xl font-bold text-brand-mocha mb-4">Items Summary (<?php echo $cart_count; ?>)</h2>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-12 h-12 rounded-xl object-cover" alt="">
                                <div>
                                    <h4 class="font-bold text-xs text-brand-mocha"><?php echo htmlspecialchars($item['title']); ?></h4>
                                    <span class="text-[11px] text-slate-400">Qty: <?php echo $item['quantity']; ?></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-extrabold text-xs text-brand-mocha">KSh <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" class="text-red-400 hover:text-red-600 text-xs">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Express Checkout Box -->
            <div class="lg:col-span-7 bg-white border border-slate-200 rounded-3xl p-8 shadow-md">
                <div class="text-center mb-6">
                    <h2 class="font-serif-title text-3xl font-extrabold text-[#3B1F1E]">Express Checkout</h2>
                    <p class="text-xs text-slate-400 mt-1">Provide your details to confirm your cold delivery</p>
                </div>

                <form action="cart.php" method="POST" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="customer_name" required placeholder="John Doe" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-brand-pink focus:ring-1 focus:ring-brand-pink">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone_number" required placeholder="+254 700 000 000" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-brand-pink focus:ring-1 focus:ring-brand-pink">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Delivery Address</label>
                            <input type="text" name="address" required placeholder="House/Apt, Street" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-brand-pink focus:ring-1 focus:ring-brand-pink">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Select Payment Method</label>
                        <div class="grid grid-cols-3 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="M-Pesa" checked class="peer sr-only">
                                <div class="border border-slate-200 peer-checked:border-brand-pink peer-checked:bg-pink-50/30 rounded-2xl p-4 text-center flex flex-col items-center justify-center transition-all h-24">
                                    <i class="fa-solid fa-mobile-screen text-emerald-600 text-2xl mb-1"></i>
                                    <span class="text-xs font-bold text-slate-700">M-Pesa</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="Card" class="peer sr-only">
                                <div class="border border-slate-200 peer-checked:border-brand-pink peer-checked:bg-pink-50/30 rounded-2xl p-4 text-center flex flex-col items-center justify-center transition-all h-24">
                                    <i class="fa-solid fa-credit-card text-blue-600 text-2xl mb-1"></i>
                                    <span class="text-xs font-bold text-slate-700">Card</span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="payment_method" value="Cash" class="peer sr-only">
                                <div class="border border-slate-200 peer-checked:border-brand-pink peer-checked:bg-pink-50/30 rounded-2xl p-4 text-center flex flex-col items-center justify-center transition-all h-24">
                                    <i class="fa-solid fa-hand-holding-dollar text-amber-500 text-2xl mb-1"></i>
                                    <span class="text-xs font-bold text-slate-700">Cash</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <span class="font-bold text-sm text-[#3B1F1E]">Payable Total:</span>
                        <span class="font-extrabold text-2xl text-brand-pink">KSh <?php echo number_format($subtotal, 2); ?></span>
                    </div>

                    <button type="submit" name="checkout" class="w-full bg-brand-pink hover:bg-brand-accent text-white font-bold py-4 rounded-full text-sm shadow-md transition-all">
                        Complete Order
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>