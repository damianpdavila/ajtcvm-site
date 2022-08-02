(function() {

    if (window.addEventListener) {
        // For standards-compliant web browsers
        window.addEventListener("load", loadScripts, false);
    } else {
        window.attachEvent("onload", loadScripts);
    }

    function loadScripts() {

        validateDownloadData();

    }

    function validateDownloadData() {

        // Article keywords are sometimes separated by semi-colon.  Force change to separate by comma so WP tag field will parse properly.
        if (document.querySelector("#new-tag-download_tag")) {
            document.querySelector("#new-tag-download_tag").addEventListener('keydown', (event) => {
                // catch the enter key
                if (event.keyCode == 13) {
                    event.target.value = event.target.value.replace(/;/g, ',');
                }
            });
            document.querySelector("#new-tag-download_tag").addEventListener('change', (event) => {
                event.target.value = event.target.value.replace(/;/g, ',');
            });
        }
        const TYPE_CATEGORY = '#download_categorydiv';
        const TYPE_PRICE = '#edd_regular_price_field input.edd-price-field';

        // Validate that appropriate categories are specified (using jQuery for expediency)
        if (jQuery('#taxonomy-download_category').length) {

            // If adding new download, clear price field to ensure a valid numeric value is entered here; 
            // otherwise EDD will default to 0.00, and want to ensure 0.00 was actually intended.
            // if (document.querySelector('form#post input[type="submit"][name="publish"]')) {
            //     document.querySelector("#edd_regular_price_field input.edd-price-field").value = "";
            // }

            // Disable the category checkbox for "Publication Year" parent category as we don't want them to check it off.
            jQuery('#in-download_category-82').prop('disabled', true);

            jQuery('form#post input[type="submit"]').click((event) => {

                let error_message = "";

                // Gotta have a valid price
                let price = document.querySelector("#edd_regular_price_field input.edd-price-field").value;
                if (price == "" || isNaN(Number(price))) {
                    showError('Download Price Error:  Please enter a numeric price.', event, TYPE_PRICE);
                }
                // EDD does not store the price in database if == 0 (!?). But custom DB query for free articles depends on having price in DB (duh).
                // Therefore force "0.00" price if equal to 0 value.
                if (Number(price) == 0) {
                    document.querySelector("#edd_regular_price_field input.edd-price-field").value = "0.00";
                }

                // Validate the categories
                /**
                 * NOTE:  this logic is based on hierarchical categories showing in the post editor.
                 * 
                 * By default, Wordpress will show hierarchy during new post creation only.  When editing existing posts, it annoyingly
                 * moves the previously-selected categories up to the top of the category list in the editor.  This would require lots of extra code to handle.
                 * 
                 * Rather than deal with all that, a plugin called "Categories in Hierarchical Order" was added to force the categories to remain
                 * in hierarchical order.  If this plugin stops working or is removed, then need to modify this logic to handle the new post vs editing post order.
                 */
                // Must choose at least one download category
                if (jQuery('#download_categorychecklist input:checked').length == 0) {
                    showError('Download Category Error:  Please pick at least one category.', event, TYPE_CATEGORY);
                    return;
                }
                // Skip remaining checks if this download is a subscription
                if (jQuery('#in-download_category-15:checked').length == 1) {
                    return;
                }
                // Cannot be both article and journal  
                if (jQuery('#in-download_category-16:checked').length == 1 && jQuery('#in-download_category-10:checked').length == 1) {
                    showError('Download Category Error:  Please pick only one: Articles or Journals', event, TYPE_CATEGORY);
                    return;
                }
                // If this is an article (parent category), it must have one and only one article type (child category)
                if (jQuery('#in-download_category-16:checked').length == 1) {

                    if (jQuery('#download_category-16 ul.children input:checked').length == 1) {;
                    } else {
                        showError('Download Category Error:  If you choose the Articles parent category, you must pick one and only one article type, e.g. Case Studies, Pearls, etc.', event, TYPE_CATEGORY);
                        return;
                    }
                }
                // If article type selected (child category), must select Articles (parent category)
                if (jQuery('#download_category-16 ul.children input:checked').length > 0) {

                    if (jQuery('#in-download_category-16:checked').length == 1) {;
                    } else {
                        showError('Download Category Error:  If you choose an article type, e.g. Case Studies, Pearls, etc., you must also tick the Articles parent category.', event, TYPE_CATEGORY);
                        return;
                    }
                }
                // Must have one and only one publication year selected
                if (jQuery('#download_category-82 ul.children input:checked').length == 1) {;
                } else {
                    showError('Download Category Error:  You must pick one and only one year of publication, e.g., 2006.', event, TYPE_CATEGORY);
                    return;
                }
                // Do not select the Publication Year parent category
                if (jQuery('#in-download_category-82:checked').length == 1) {

                    jQuery('#in-download_category-82').prop("checked", false);
                    showError('Download Category informational reminder:  Please do not select the parent "Publication Year" category, only an individual child year under that.', null, TYPE_CATEGORY);
                    return;
                }

            });
        }

        function showError(msg, event = null, element = null) {
            alert(msg);
            if (event) {
                event.stopImmediatePropagation();
                event.stopPropagation();
                event.preventDefault();
                if (element) {
                    let scrollPos = jQuery(element).offset().top;
                    if (typeof scrollPos === 'number') {
                        scrollPos -= 100;
                        jQuery(window).scrollTop(scrollPos);
                        jQuery(element).css('border', '2px solid red');
                    }

                }
            }
        }
    }

})();