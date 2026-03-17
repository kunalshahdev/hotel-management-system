/**
 * StayManager — Customer Portal JS
 * Navbar scroll, mobile menu, user dropdown, booking calculator
 */
document.addEventListener('DOMContentLoaded', function () {

    // ── Navbar scroll effect ──
    var navbar = document.getElementById('cNavbar');
    if (navbar) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ── Mobile menu toggle ──
    var menuToggle = document.getElementById('cMenuToggle');
    var navLinks = document.getElementById('cNavLinks');
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', function () {
            navLinks.classList.toggle('open');
        });
    }

    // ── User dropdown ──
    var userMenu = document.getElementById('cUserMenu');
    var userDropdown = document.getElementById('cUserDropdown');
    if (userMenu && userDropdown) {
        userMenu.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
        document.addEventListener('click', function () {
            userDropdown.classList.remove('show');
        });
    }

    // ── Booking price calculator ──
    var checkInInput = document.getElementById('bookCheckIn');
    var checkOutInput = document.getElementById('bookCheckOut');
    var pricePerNight = document.getElementById('bookPricePerNight');
    var nightsDisplay = document.getElementById('bookNights');
    var totalDisplay = document.getElementById('bookTotal');

    function updateBookingCalc() {
        if (!checkInInput || !checkOutInput || !pricePerNight) return;
        var checkIn = new Date(checkInInput.value);
        var checkOut = new Date(checkOutInput.value);
        var price = parseFloat(pricePerNight.value) || 0;

        if (checkIn && checkOut && checkOut > checkIn) {
            var nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
            var total = nights * price;
            if (nightsDisplay) nightsDisplay.textContent = nights;
            if (totalDisplay) totalDisplay.textContent = '₹' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
        }
    }

    if (checkInInput) checkInInput.addEventListener('change', updateBookingCalc);
    if (checkOutInput) checkOutInput.addEventListener('change', updateBookingCalc);

    // Set minimum check-in date to today
    if (checkInInput) {
        var today = new Date().toISOString().split('T')[0];
        checkInInput.setAttribute('min', today);
        if (!checkInInput.value) checkInInput.value = today;
    }
    if (checkOutInput) {
        var tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        var tmrStr = tomorrow.toISOString().split('T')[0];
        checkOutInput.setAttribute('min', tmrStr);
        if (!checkOutInput.value) checkOutInput.value = tmrStr;
    }

    // Initial calc
    updateBookingCalc();
});
