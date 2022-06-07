(function() {

    if (window.addEventListener) {
        // For standards-compliant web browsers
        window.addEventListener("load", setRegisterButton, false);
    } else {
        window.attachEvent("onload", setRegisterButton);
    }


    function setRegisterButton() {

        // Hide the register button in top bar if user logged in
        const userLoggedIn = document.querySelector("span.ajtcvm-logged-in");

        if (userLoggedIn) {
            // add hide class to button
            document.querySelector('#register-button').classList.add('hide');
        } else {
            // remove hide class from button
            document.querySelector('#register-button').classList.remove('hide');
        }

    }

})();