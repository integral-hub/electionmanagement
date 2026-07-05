/**
 * Admin layout behaviour.
 *
 * - Sidebar open/close on mobile (menu button + overlay).
 *   Menu button visibility itself is handled purely by CSS
 *   (see #menuBtn rules in admin-layout.css), so no resize
 *   listener is needed here.
 * - Desktop sidebar collapse (icon rail), persisted in
 *   localStorage so it survives page loads/navigation.
 * - Generic dropdown menu handling for any `[data-dropdown]`
 *   trigger followed by a `.dropdown-menu` element (used here
 *   and in several admin/* views, e.g. elections/index).
 */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuBtn = document.getElementById('menuBtn');
    const collapseBtn = document.getElementById('collapseBtn');

    const toggleSidebar = () => {
        sidebar?.classList.toggle('open');
        overlay?.classList.toggle('open');
    };

    menuBtn?.addEventListener('click', toggleSidebar);
    overlay?.addEventListener('click', toggleSidebar);

    const COLLAPSE_KEY = 'admin-sidebar-collapsed';

    if (sidebar && localStorage.getItem(COLLAPSE_KEY) === '1') {
        sidebar.classList.add('collapsed');
    }

    collapseBtn?.addEventListener('click', () => {
        const collapsed = sidebar?.classList.toggle('collapsed');
        localStorage.setItem(COLLAPSE_KEY, collapsed ? '1' : '0');
    });

    document.addEventListener('click', (event) => {
        document.querySelectorAll('.dropdown-menu.show').forEach((menu) => {
            if (!menu.parentElement.contains(event.target)) {
                menu.classList.remove('show');
            }
        });

        const trigger = event.target.closest('[data-dropdown]');
        if (trigger) {
            const menu = trigger.nextElementSibling;
            menu?.classList.toggle('show');
        }
    });
});
