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
        <span class="text-brand-pink font-extrabold text-xs uppercase tracking-widest">Find Us</span>
        <h1 class="font-serif-title text-4xl font-extrabold text-brand-mocha">Scoop & Swirl Creamery Hubs</h1>
        <p class="text-gray-600 text-sm">Visit our stores or drop a message to book catering for your special events!</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-1 gap-2">
        
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-lg space-y-4">
            <div class="w-10 h-10 rounded-xl bg-brand-pinklight text-brand-pink flex items-center justify-center font-bold">
                <i class="fa-solid fa-store"></i>
            </div>
            <h3 class="font-serif-title text-xl font-bold text-brand-mocha">KIRINYAGA,KUTUS</h3>
            <p class="text-xs text-gray-500"><i class="fa-solid fa-location-dot text-brand-pink mr-1"></i> Next to Rubis Petrol Station</p>
            <p class="text-xs text-gray-500"><i class="fa-solid fa-clock text-brand-pink mr-1"></i> Mon-Sun: 10:00 AM – 9:00 PM</p>
        </div>
       
    </div>
</main>

<?php include 'includes/footer.php'; ?>