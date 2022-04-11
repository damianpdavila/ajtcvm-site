(function() {

    if (window.addEventListener) {
        // For standards-compliant web browsers
        window.addEventListener("load", setSearchSpinner, false);
    } else {
        window.attachEvent("onload", setSearchSpinner);
    }


    function setSearchSpinner() {

        // set submit listeners for search form and filter form
        const filter_forms = document.querySelectorAll("form.elementor-search-form, form.searchandfilter");

        filter_forms.forEach(filter_form => {

            if (filter_form.addEventListener) {
                // For standards-compliant web browsers
                filter_form.addEventListener("submit", searchTriggered, false);
            } else {
                filter_form.attachEvent("submit", searchTriggered);
            }

        });

        // set submit listeners for pagination
        const search_forms = document.querySelectorAll("#search-results a.page-numbers");

        search_forms.forEach(search_form => {

            if (search_form.addEventListener) {
                // For standards-compliant web browsers
                search_form.addEventListener("click", searchTriggered, false);
            } else {
                search_form.attachEvent("click", searchTriggered);
            }

        });


        function searchTriggered(event) {

            // Show the "Searching..." popup
            elementorProFrontend.modules.popup.showPopup({ id: 4815 });

        }
    }

})();