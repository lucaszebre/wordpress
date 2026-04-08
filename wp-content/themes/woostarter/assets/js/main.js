// main.js - Script principal du thème themeESGI
// Gère le menu mobile et le scroll smooth

(function () {
    // --- Menu mobile (hamburger) ---
    var toggle = document.getElementById('menu-toggle');
    var nav = document.getElementById('main-navigation');

    if (toggle && nav) {
        // Quand on clique sur le hamburger, on ouvre/ferme le menu
        toggle.addEventListener('click', function () {
            var expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', String(!expanded));
            toggle.classList.toggle('active');
            nav.classList.toggle('active');
        });

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target) && !toggle.contains(e.target)) {
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.remove('active');
                nav.classList.remove('active');
            }
        });
    }

    // --- Scroll smooth pour les liens ancres (#...) ---
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
})();
