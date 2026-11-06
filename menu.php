<?php
ini_set('display_errors', 1);
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

    $category = isset($_GET['category']) ? $_GET['category'] : 'All';
    if($category !== 'All') {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->query("SELECT * FROM products");
    }
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

include 'includes/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 flex-grow">
    <div class="bg-gradient-to-r from-brand-mocha to-brand-berry text-white rounded-3xl p-8 sm:p-10 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="space-y-2 text-center md:text-left">
            <span class="text-brand-pink font-bold text-xs uppercase tracking-widest">Our Full Selection</span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold">Scoop & Swirl Store Menu</h1>
            <p class="text-white/80 text-sm max-w-lg">Choose from artisanal ice cream, thick milkshakes, probiotic mala, Greek yogurts, and farm-fresh butter & milk.</p>
        </div>
    </div>

    <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <a href="menu.php" class="px-5 py-2.5 rounded-2xl text-xs font-bold whitespace-nowrap shadow-sm transition-all <?php echo $category=='All' ? 'bg-brand-pink text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-brand-pinklight'; ?>">All Items</a>
        <?php foreach(['Ice Cream', 'Milkshakes', 'Mala', 'Yogurt', 'Dairy'] as $cat): ?>
            <a href="menu.php?category=<?php echo urlencode($cat); ?>" class="px-5 py-2.5 rounded-2xl text-xs font-bold whitespace-nowrap transition-all <?php echo $category==$cat ? 'bg-brand-pink text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-700 hover:bg-brand-pinklight'; ?>"><?php echo $cat; ?></a>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <?php if(empty($products)): ?>
            <div class="col-span-full text-center py-12 text-gray-500 font-bold bg-white rounded-3xl border border-gray-100 shadow-sm">No products found in this category.</div>
        <?php else: foreach($products as $p): ?>
            <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between group">
                <div class="relative h-48 overflow-hidden">
                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-md text-brand-mocha text-[10px] font-extrabold px-3 py-1 rounded-full shadow-sm">
                        <?php echo htmlspecialchars($p['tag'] ?? 'Featured'); ?>
                    </span>
                    <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                <div class="p-5 space-y-3 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between text-xs text-amber-500 font-bold mb-1">
                            <span>★ <?php echo htmlspecialchars($p['rating'] ?? '4.9'); ?></span>
                            <span class="text-gray-400 font-normal"><?php echo htmlspecialchars($p['category']); ?></span>
                        </div>
                        <h3 class="font-bold text-brand-mocha text-base group-hover:text-brand-pink transition-colors"><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mt-1"><?php echo htmlspecialchars($p['description']); ?></p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="font-extrabold text-lg text-brand-mocha">KSh <?php echo number_format($p['price'], 2); ?></span>
                        <form action="cart.php?action=add&redirect=menu.php" method="POST" class="ajax-add-cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                            <button type="submit" class="bg-brand-pink hover:bg-brand-accent text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-md shadow-brand-pink/20 flex items-center gap-1.5">
                                <i class="fa-solid fa-cart-plus"></i> Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>