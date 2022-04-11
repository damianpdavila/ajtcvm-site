(function() {

    if (window.addEventListener) {
        // For standards-compliant web browsers
        window.addEventListener('load', checkForQuotedSearch, false);
    } else {
        window.attachEvent('onload', checkForQuotedSearch);
    }

    function checkForQuotedSearch() {

        /* If quoted search was submitted, show the warning message for highlights. */

        // Since search boxes are synchronized by Wordpress, only need to check the first one - not all of them.
        let search_text_one = document.querySelector('.elementor-search-form__input') ? document.querySelector('.elementor-search-form__input').value.charAt(0) : '';

        // Must check for 'curly' quotes and angled single quotes too cuz iOS (https://stackoverflow.com/questions/36873236/detecting-curly-ms-smart-quotes-in-javascript)
        if (/[\u201C\u201D\u201E\u2018\u2019\u201A"']/.test(search_text_one)) {

            // Show the informational message
            document.getElementById('search-partial-highlight-msg').style.display = 'block';
        } else {
            document.getElementById('search-partial-highlight-msg').style.display = 'none';
        }

    }

})();