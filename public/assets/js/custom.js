$(document).ready(function () {
    var hasModuleSidebar = $(".content-sidebar").length > 0;

    $(".nxl-lavel-mega-menu-toggle").toggleClass("d-none", !hasModuleSidebar);

    function closeMobileNavigation() {
        $(".nxl-navigation").removeClass("mob-navigation-active");
        $(".nxl-navigation .nxl-menu-overlay").remove();
        $("#mobile-collapse .hamburger").removeClass("is-active");
        $("html").removeClass("nxl-lavel-mega-menu-open");
    }

    function closeModuleSidebar() {
        $(".content-sidebar").removeClass("app-sidebar-open");
        $(".content-sidebar-overlay").remove();
    }

    function openModuleSidebar() {
        $(".content-sidebar").addClass("app-sidebar-open");
        $(".content-sidebar-overlay").remove();
        $("body").append('<div class="content-sidebar-overlay"></div>');
        $(".content-sidebar-overlay").on("click", closeModuleSidebar);
    }

    $("#nxl-lavel-mega-menu-open").on("click", function (e) {
        e.preventDefault();

        if (!hasModuleSidebar) {
            return;
        }

        closeMobileNavigation();

        if ($(".content-sidebar").hasClass("app-sidebar-open")) {
            closeModuleSidebar();
        } else {
            openModuleSidebar();
        }
    });

    $("#mobile-collapse").on("click", function () {
        closeModuleSidebar();
    });

    $(".content-sidebar .nav-link").on("click", closeModuleSidebar);

    $(window).on("resize", function () {
        if (window.innerWidth > 1024) {
            closeMobileNavigation();
        }

        if (window.innerWidth > 1199) {
            closeModuleSidebar();
        }
    });

    $(".nxl-hasmenu > .nxl-link").on("click", function (e) {
        

        var parent = $(this).parent();
        var submenu = parent.children(".nxl-submenu");

        if (parent.hasClass("active")) {
            submenu.slideUp(300);
            parent.removeClass("active");
        } else {
            $(".nxl-submenu").slideUp(300);
            $(".nxl-hasmenu").removeClass("active");

            submenu.slideDown(300);
            parent.addClass("active");
        }
    });

});
