/**
 * StayManager HMS — Application JavaScript
 * Sidebar, Modals, Toasts, Dropdowns, Filters
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar Toggle ──
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');

    if (toggle) {
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }

    // ── User Dropdown ──
    const userToggle = document.getElementById('userDropdownToggle');
    const userDropdown = document.getElementById('userDropdown');

    if (userToggle && userDropdown) {
        userToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function () {
            userDropdown.classList.remove('show');
        });
    }

    // ── Auto-dismiss alerts ──
    document.querySelectorAll('[data-auto-dismiss]').forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () { alert.remove(); }, 300);
        }, 4000);
    });

    // ── Confirm Delete ──
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var msg = this.getAttribute('data-confirm') || 'Are you sure you want to delete this?';
            if (!confirm(msg)) {
                e.preventDefault();
            }
        });
    });

    // ── Modal System ──
    window.openModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeModal = function (modalId) {
        var modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    };

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                overlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });
    });

    // ── Toast Notifications ──
    window.showToast = function (message, type) {
        type = type || 'success';
        var container = document.getElementById('toastContainer');
        if (!container) return;

        var toast = document.createElement('div');
        toast.className = 'toast' + (type === 'error' ? ' toast-error' : '');
        toast.textContent = message;
        container.appendChild(toast);

        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(30px)';
            setTimeout(function () { toast.remove(); }, 300);
        }, 3500);
    };

    // ── Table Search/Filter ──
    var searchInputs = document.querySelectorAll('[data-table-search]');
    searchInputs.forEach(function (input) {
        var tableId = input.getAttribute('data-table-search');
        var table = document.getElementById(tableId);
        if (!table) return;

        input.addEventListener('input', function () {
            var query = this.value.toLowerCase();
            var rows = table.querySelectorAll('tbody tr');
            rows.forEach(function (row) {
                var text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });

    // ── Status Filter Tabs ──
    var filterTabs = document.querySelectorAll('[data-filter-status]');
    filterTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            // Remove active from siblings
            this.parentElement.querySelectorAll('[data-filter-status]').forEach(function (t) {
                t.classList.remove('active');
            });
            this.classList.add('active');

            var status = this.getAttribute('data-filter-status');
            var tableId = this.getAttribute('data-filter-table');
            var table = document.getElementById(tableId);
            if (!table) return;

            var rows = table.querySelectorAll('tbody tr');
            rows.forEach(function (row) {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    var rowStatus = row.getAttribute('data-status');
                    row.style.display = (rowStatus === status) ? '' : 'none';
                }
            });
        });
    });

});
