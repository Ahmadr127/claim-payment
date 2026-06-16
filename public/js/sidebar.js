/**
 * Sidebar Component for Alpine.js
 * Single control system: Alpine manages all state, CSS only animates.
 * NO CSS variables, NO html class toggling — clean single source of truth.
 */

// Prevent FOUC: stamp initial appbar/sidebar class on body BEFORE Alpine loads
// so the sidebar starts in the correct position immediately on paint.
(function () {
    const layout = document.currentScript
        ? document.currentScript.dataset.layout
        : null;
    // We can't easily read server-side pref here without embedding it,
    // so we use a data attribute placed on the script tag by Blade.
    // Fallback: read from body data attr if set.
    const bodyLayout = document.body && document.body.dataset.layout;
    if (bodyLayout === 'appbar') {
        // Pre-hide sidebar before Alpine boots so there's zero flash
        document.documentElement.classList.add('pre-appbar');
    }
})();

/**
 * Alpine.js Sidebar Component
 * @param {string} initialLayout - 'sidebar' | 'appbar' (passed from Blade)
 */
function sidebarComponent(initialLayout) {
    return {
        isCollapsed: false,
        mobileOpen: false,
        layoutPref: initialLayout || 'sidebar',

        init() {
            // Sync collapsed state from localStorage (desktop only)
            this.isCollapsed = localStorage.getItem('sidebarCollapsed') === '1';
            // Clean up pre-appbar class — Alpine takes over now
            document.documentElement.classList.remove('pre-appbar');
            const preStyle = document.getElementById('pre-appbar-style');
            if (preStyle) preStyle.remove();
        },

        toggle() {
            if (window.innerWidth >= 1024) {
                this.isCollapsed = !this.isCollapsed;
                localStorage.setItem('sidebarCollapsed', this.isCollapsed ? '1' : '0');
            } else {
                this.mobileOpen = !this.mobileOpen;
            }
        },

        getToggleTitle() {
            if (window.innerWidth >= 1024) {
                return this.isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
            }
            return this.mobileOpen ? 'Tutup menu' : 'Buka menu';
        },

        toggleLayoutPreference(toggleRoute, csrfToken) {
            this.layoutPref = this.layoutPref === 'appbar' ? 'sidebar' : 'appbar';
            fetch(toggleRoute, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            }).catch((err) => console.error(err));
        },

        // Computed helpers for template bindings
        get sidebarClasses() {
            return {
                'appbar-mode': this.layoutPref === 'appbar',
                'sidebar-collapsed-mode': this.isCollapsed && this.layoutPref === 'sidebar',
                'mobile-open': this.mobileOpen,
            };
        },

        get showToggleIcon() {
            return this.layoutPref === 'sidebar';
        },
    };
}
