(function () {

    const THEME_KEY = "interviewPrepTheme";

    function getTheme() {
        return localStorage.getItem(THEME_KEY) || "system";
    }


    function isDarkTheme(theme) {

        if (theme === "dark") {
            return true;
        }

        if (theme === "light") {
            return false;
        }

        return window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches;
    }


    function applyTheme(theme) {

        const dark = isDarkTheme(theme);

        document.documentElement.classList.toggle(
            "dark-mode",
            dark
        );

        if (document.body) {

            document.body.classList.toggle(
                "dark-mode",
                dark
            );

        }

        /* Save current theme */
        localStorage.setItem(
            THEME_KEY,
            theme
        );

        /* Update theme controls if available */

        document.querySelectorAll(
            'input[name="theme"]'
        ).forEach(function (input) {

            input.checked =
                input.value === theme;

        });


        document.querySelectorAll(
            '[data-theme]'
        ).forEach(function (button) {

            button.classList.toggle(
                "active",
                button.getAttribute("data-theme") === theme
            );

        });

    }


    /* =====================================
       APPLY THEME IMMEDIATELY
       ===================================== */

    applyTheme(getTheme());


    /* =====================================
       PAGE LOAD
       ===================================== */

    document.addEventListener(
        "DOMContentLoaded",
        function () {

            applyTheme(getTheme());


            /* =================================
               RADIO BUTTONS
               ================================= */

            document.querySelectorAll(
                'input[name="theme"]'
            ).forEach(function (input) {

                input.addEventListener(
                    "change",
                    function () {

                        const selected =
                            this.value;

                        localStorage.setItem(
                            THEME_KEY,
                            selected
                        );

                        applyTheme(selected);

                    }
                );

            });


            /* =================================
               DATA-THEME BUTTONS
               ================================= */

            document.querySelectorAll(
                '[data-theme]'
            ).forEach(function (button) {

                button.addEventListener(
                    "click",
                    function () {

                        const selected =
                            this.getAttribute(
                                "data-theme"
                            );

                        if (
                            selected === "dark" ||
                            selected === "light" ||
                            selected === "system"
                        ) {

                            localStorage.setItem(
                                THEME_KEY,
                                selected
                            );

                            applyTheme(selected);

                        }

                    }
                );

            });

        }
    );


    /* =====================================
       SYSTEM THEME CHANGE
       ===================================== */

    window.matchMedia(
        "(prefers-color-scheme: dark)"
    ).addEventListener(
        "change",
        function () {

            if (getTheme() === "system") {

                applyTheme("system");

            }

        }
    );


})();