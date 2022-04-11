(function() {

    if (window.addEventListener) {
        // For standards-compliant web browsers
        window.addEventListener("load", checkForAllAccessPass, false);
    } else {
        window.attachEvent("onload", checkForAllAccessPass);
    }

    function checkForAllAccessPass() {

        /* get pass status from the EDD purchase button */

        let has_all_access = document.querySelector('.edd_purchase_submit_wrapper a.edd-all-access-btn');

        if (has_all_access) {

            /* set class on All Access Pass upsell box */

            document.getElementById('all-access-pass-upsell').classList.add('has-all-access-pass');
        }

    };

})();