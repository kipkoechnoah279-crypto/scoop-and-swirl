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
    $stmt = $pdo->query("SELECT * FROM products LIMIT 4");
    $bestsellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $bestsellers = [];
}

include 'includes/header.php';
?>

<main class="space-y-16 pb-16">
    <div class="relative overflow-hidden bg-gradient-to-b from-brand-pinklight/60 via-brand-cream to-white pt-8 pb-16 lg:pt-16 lg:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 bg-brand-pink/10 text-brand-pink font-bold px-4 py-1.5 rounded-full text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-sparkles"></i> 100% Pure Farm Fresh Dairy
                    </span>
                    <h1 class="font-serif-title text-4xl sm:text-5xl lg:text-6xl font-extrabold text-brand-mocha leading-tight">
                        Handcrafted Pure Ice Creams & Traditional <span class="text-brand-pink underline decoration-brand-yellow">Dairy Treats</span>
                    </h1>
                    <p class="text-gray-600 text-base sm:text-lg max-w-2xl mx-auto lg:mx-0">
                        Indulge in our small-batch artisanal ice cream, rich thick milkshakes, authentic probiotic sour milk (Mala), and smooth frozen yogurt.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <a href="menu.php" class="px-8 py-4 bg-brand-pink hover:bg-brand-accent text-white font-bold rounded-2xl shadow-lg transition-all flex items-center gap-3">
                            <span>Explore Full Menu</span> <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 relative">
                    <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white">
                        <img src="https://images.unsplash.com/photo-1563805042-7684c019e1cb?auto=format&fit=crop&w=1000&q=80" alt="Sundae" class="w-full h-[420px] object-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-brand-pink font-extrabold text-xs uppercase tracking-widest">Our Delicious Range</span>
            <h2 class="font-serif-title text-3xl font-bold text-brand-mocha mt-1">Explore Dairy Categories</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <?php foreach(['Ice Cream', 'Milkshakes', 'Mala', 'Yogurt', 'Dairy'] as $cat): ?>
                <a href="menu.php?category=<?php echo urlencode($cat); ?>" class="group bg-white rounded-3xl overflow-hidden shadow-md hover:shadow-xl transition-all p-6 text-center border border-gray-100">
                    <h3 class="font-bold text-lg text-brand-mocha group-hover:text-brand-pink transition-colors"><?php echo $cat; ?></h3>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>