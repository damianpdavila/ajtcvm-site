(function () {
   if (window.addEventListener) {
     // For standards-compliant web browsers
     window.addEventListener("load", setCartQuantity, false);
   } else {
     window.attachEvent("onload", setCartQuantity);
   }
   /**
    * Retrieve current cart item quantity from cookie and update the cart icon in top nav.
    * Necessary to compensate for server-cached pages.
    */
   function setCartQuantity() {
    
    // Get cart quantity from cookie
    const cookieValue = document.cookie
      .split("; ")
      .find((row) => row.startsWith("edd_cart="))
      ?.split("=")[1];

      let cart = JSON.parse( decodeURIComponent(cookieValue) );

    // Set cart item quantity and appropriate classes into the cart icon

    let cartIcon = document.querySelector(".edd-cart-quantity");
    cartIcon.innerText = cart.quantity.toString();

    if (cart.quantity > 0) {
        cartIcon?.classList.add("edd-cart-quantity", "edd-cart-loaded");
    } else {
        cartIcon?.classList.remove("edd-cart-loaded");
    }


  }
})();
