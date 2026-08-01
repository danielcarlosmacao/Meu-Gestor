document.addEventListener('DOMContentLoaded', function () {

    const desktopBreakpoint = 1200;

    /*
    |--------------------------------------------------------------------------
    | ABRIR MEGA MENUS NO HOVER SOMENTE EM DESKTOP
    |--------------------------------------------------------------------------
    */

    const dropdownItems =
        document.querySelectorAll('.app-menu-item.dropdown');

    dropdownItems.forEach(function (item) {

        let closeTimer = null;

        item.addEventListener('mouseenter', function () {

            if (window.innerWidth < desktopBreakpoint) {
                return;
            }

            clearTimeout(closeTimer);

            const toggle =
                item.querySelector('[data-bs-toggle="dropdown"]');

            if (!toggle || typeof bootstrap === 'undefined') {
                return;
            }

            bootstrap.Dropdown
                .getOrCreateInstance(toggle)
                .show();
        });

        item.addEventListener('mouseleave', function () {

            if (window.innerWidth < desktopBreakpoint) {
                return;
            }

            closeTimer = setTimeout(function () {

                const toggle =
                    item.querySelector('[data-bs-toggle="dropdown"]');

                if (!toggle || typeof bootstrap === 'undefined') {
                    return;
                }

                bootstrap.Dropdown
                    .getOrCreateInstance(toggle)
                    .hide();

            }, 180);
        });

    });

    /*
    |--------------------------------------------------------------------------
    | FECHAR O MENU MOBILE APÓS CLICAR EM UM LINK
    |--------------------------------------------------------------------------
    */

    const navbarCollapse =
        document.getElementById('mainNavbar');

    if (navbarCollapse) {

        navbarCollapse
            .querySelectorAll('a:not(.dropdown-toggle)')
            .forEach(function (link) {

                link.addEventListener('click', function () {

                    if (window.innerWidth >= desktopBreakpoint) {
                        return;
                    }

                    if (typeof bootstrap === 'undefined') {
                        return;
                    }

                    const collapse =
                        bootstrap.Collapse.getInstance(navbarCollapse);

                    if (collapse) {
                        collapse.hide();
                    }
                });

            });
    }

});