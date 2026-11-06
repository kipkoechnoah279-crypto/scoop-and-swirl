document.addEventListener("DOMContentLoaded", () => {
    // Asynchronous AJAX Add to Cart handler
    document.body.addEventListener("submit", (e) => {
        if (e.target.classList.contains("ajax-add-cart-form")) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                method: "POST",
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    const badge = document.getElementById("cart-badge");
                    if (badge) {
                        badge.textContent = data.cart_count;
                    }
                    const btn = form.querySelector("button[type='submit']");
                    if (btn) {
                        const originalHTML = btn.innerHTML;
                        btn.innerHTML = `<i class="fa-solid fa-check"></i> Added!`;
                        btn.classList.add("bg-green-600");
                        setTimeout(() => {
                            btn.innerHTML = originalHTML;
                            btn.classList.remove("bg-green-600");
                        }, 1500);
                    }
                }
            })
            .catch(err => console.error("Error adding product to cart:", err));
        }
    });

    // Admin Password Visibility Toggle
    const toggleBtn = document.getElementById("togglePasswordBtn");
    const passInput = document.getElementById("passwordInput");

    if (toggleBtn && passInput) {
        toggleBtn.addEventListener("click", () => {
            const isPassword = passInput.getAttribute("type") === "password";
            passInput.setAttribute("type", isPassword ? "text" : "password");
            toggleBtn.innerHTML = isPassword ? `<i class="fa-solid fa-eye-slash"></i>` : `<i class="fa-solid fa-eye"></i>`;
        });
    }
});
document.addEventListener("DOMContentLoaded", () => {
    // Existing code (AJAX Cart & Password Toggle)...

    // Mobile Hamburger Menu Toggle
    const mobileMenuBtn = document.getElementById("mobileMenuBtn");
    const mobileMenu = document.getElementById("mobileMenu");

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener("click", () => {
            mobileMenu.classList.toggle("hidden");
            const icon = mobileMenuBtn.querySelector("i");
            if (icon) {
                if (mobileMenu.classList.contains("hidden")) {
                    icon.className = "fa-solid fa-bars";
                } else {
                    icon.className = "fa-solid fa-xmark";
                }
            }
        });
    }
});