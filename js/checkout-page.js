(function() {

    if (window.addEventListener) {
        // For standards-compliant web browsers
        window.addEventListener("load", catchLoginErrorMessage, false);
    } else {
        window.attachEvent("onload", catchLoginErrorMessage);
    }


    function catchLoginErrorMessage() {

        const eleErrMsg = document.getElementById("edd_error_edd_recurring_login");

        if (eleErrMsg) {
            const replacementErrorMsg =
                "<h5>This email is already assigned to an account</h5>" +
                "Please double-check the spelling in case of a typo.  It happens.</br>" +
                "If it is correct, simply <a href='/profile/'>click here</a> to log in with this email address first then return to the cart to complete the purchase.</br>" +
                "Or enter a different email address and we will create a new account for you. <a href='/help/'>Learn more...</a>";

            eleErrMsg.innerHTML = replacementErrorMsg;
            setTimeout(() => {
                eleErrMsg.scrollIntoView({ behavior: "smooth", block: "center" });
            }, 500);
        }

    }

})();