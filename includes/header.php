<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../config/db.php';

$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += isset($item['quantity']) ? (int)$item['quantity'] : 1;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scoop & Swirl | Creamery & Dairy Bar</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            pink: '#FF7597',
                            pinklight: '#FFF0F4',
                            cream: '#FFFDF7',
                            yellow: '#FFF3CD',
                            berry: '#8C1D40',
                            mocha: '#4A2C2A',
                            accent: '#FF477E',
                            mala: '#0284C7'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    }
                }
            }
        }
    </script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,800;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/main.js" defer></script>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-brand-pink selection:text-white">

    <!-- Announcement Bar -->
    <div class="bg-brand-pink text-white text-xs md:text-sm font-medium py-2 px-4 text-center tracking-wide flex items-center justify-center gap-2 shadow-inner">
        <span><strong>Special Offer:</strong> Free delivery on all orders over KSh 2,500! Try our new Mint & Ginger Mala today!</span>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-40 glass border-b border-brand-pink/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Brand Logo -->
                <a href="index.php" class="flex items-center gap-3 cursor-pointer">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-pink to-brand-accent text-white flex items-center justify-center text-2xl shadow-lg shadow-brand-pink/30">
                        <i class="fa-solid font-bold fa-ice-cream"></i>
                    </div>
                    <div>
                        <span class="font-serif-title text-2xl font-extrabold text-brand-mocha tracking-tight block leading-none">Scoop & Swirl</span>
                        <span class="text-[10px] font-bold text-brand-pink uppercase tracking-widest block mt-1">Creamery & Dairy Bar</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-1 lg:space-x-2 font-medium text-gray-700">
                    <a href="index.php" class="px-4 py-2 rounded-full text-sm hover:text-brand-pink hover:bg-brand-pinklight transition-all">Home</a>
                    <a href="menu.php" class="px-4 py-2 rounded-full text-sm hover:text-brand-pink hover:bg-brand-pinklight transition-all">Menu & Products</a>
                    <a href="about.php" class="px-4 py-2 rounded-full text-sm hover:text-brand-pink hover:bg-brand-pinklight transition-all">About Us</a>
                    <a href="contact.php" class="px-4 py-2 rounded-full text-sm hover:text-brand-pink hover:bg-brand-pinklight transition-all">Locations</a>
                </nav>

                <!-- Actions Right Side -->
                <div class="flex items-center space-x-3">
                    <a href="admin/index.php" class="hidden sm:flex items-center gap-2 bg-brand-mocha/5 text-brand-mocha border border-brand-mocha/10 hover:bg-brand-mocha hover:text-white px-3.5 py-2 rounded-xl text-xs font-bold transition-all">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Admin</span>
                    </a>

                    <a href="cart.php" class="relative bg-brand-pink text-white hover:bg-brand-accent px-4 py-2.5 rounded-2xl flex items-center gap-2 shadow-md shadow-brand-pink/20 transition-all active:scale-95">
                        <i class="fa-solid fa-bag-shopping text-base"></i>
                        <span class="font-bold text-xs hidden sm:inline">Cart</span>
                        <span id="cart-badge" class="bg-white text-brand-pink font-extrabold text-xs px-2 py-0.5 rounded-full shadow-sm"><?php echo $cart_count; ?></span>
                    </a>

                    <!-- Mobile Menu Hamburger Button -->
                    <button id="mobileMenuBtn" class="md:hidden text-brand-mocha p-2 rounded-xl hover:bg-brand-pinklight focus:outline-none text-xl">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Dropdown Menu -->
        <div id="mobileMenu" class="hidden md:hidden bg-white border-b border-gray-100 px-6 py-4 space-y-3 shadow-lg">
            <a href="index.php" class="block text-sm font-semibold text-gray-700 hover:text-brand-pink">Home</a>
            <a href="menu.php" class="block text-sm font-semibold text-gray-700 hover:text-brand-pink">Menu & Products</a>
            <a href="about.php" class="block text-sm font-semibold text-gray-700 hover:text-brand-pink">About Us</a>
            <a href="contact.php" class="block text-sm font-semibold text-gray-700 hover:text-brand-pink">Locations</a>
            <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                <a href="admin/index.php" class="inline-flex items-center gap-2 text-xs font-bold text-brand-mocha bg-brand-mocha/5 px-3 py-2 rounded-xl">
                    <i class="fa-solid fa-gauge-high"></i> Admin Portal
                </a>
            </div>
        </div>
    </header>