<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'includes/header.php';
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12 flex-grow">
    <div class="text-center max-w-2xl mx-auto space-y-3">
        <span class="text-brand-pink font-extrabold text-xs uppercase tracking-widest">Our Heritage</span>
        <h1 class="font-serif-title text-4xl font-extrabold text-brand-mocha">Crafting Happiness Since 2018</h1>
        <p class="text-gray-600 text-sm leading-relaxed">At Scoop & Swirl, we combine traditional slow-fermentation techniques with modern artisanal ice cream crafting.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-white p-8 sm:p-10 rounded-3xl border border-gray-100 shadow-sm">
        <img src="https://images.unsplash.com/photo-1559598467-f8b76c8155d0?auto=format&fit=crop&w=800&q=80" class="rounded-2xl shadow-md h-80 w-full object-cover" alt="Farm dairy">
        <div class="space-y-4">
            <h2 class="font-serif-title text-2xl font-bold text-brand-mocha">From Local Pastures to Your Table</h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                We started with a simple belief: high quality, pure dairy products shouldn't be compromised with artificial preservatives. Our authentic fermented Mala and fresh creams are sourced directly from trusted local farms, aged to perfection, and churned in small daily batches.
            </p>
            <div class="pt-2 flex gap-4">
                <div class="bg-brand-pinklight p-4 rounded-2xl flex-1 text-center">
                    <span class="block font-extrabold text-brand-pink text-xl">100%</span>
                    <span class="text-xs text-gray-600 font-medium">Natural Ingredients</span>
                </div>
                <div class="bg-brand-pinklight p-4 rounded-2xl flex-1 text-center">
                    <span class="block font-extrabold text-brand-pink text-xl">Daily</span>
                    <span class="text-xs text-gray-600 font-medium">Freshly Churned</span>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>