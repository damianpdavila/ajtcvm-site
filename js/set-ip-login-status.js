(function() {

    if (window.addEventListener) {
        // For standards-compliant web browsers
        window.addEventListener('load', checkForLoginViaIp, false);
    } else {
        window.attachEvent('onload', checkForLoginViaIp);
    }

    function checkForLoginViaIp() {
        // Custom plugin adds class to the login button in header.  If set and the profile editor update button is on page...
        if (document.querySelector('.ajtcvm-logged-in-by-IP') && document.getElementById('edd_profile_editor_submit')) {
            document.getElementById('edd_profile_editor_submit').addEventListener('click', function(evt) {
                // Disable updating the profile.
                evt.stopPropagation();
                window.alert('Sorry, institutional subscribers are not allowed to update their profiles.  Please contact your administrator for assistance.');
                evt.preventDefault();
            }, false);
        }
    }

})();