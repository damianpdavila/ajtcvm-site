(function() {

    window.addEventListener('load', function(event) {

        document.querySelectorAll('#journal-toc p strong').forEach(item => { item.parentElement.classList.add('toc-category-heading'); });
        document.querySelectorAll('#journal-toc p:not(.toc-category-heading)').forEach(item => { item.classList.add('toc-item'); });
        document.querySelectorAll('#journal-toc p > a ~ em').forEach((item) => { item.classList.add('toc-item-author'); });
        document.querySelectorAll('#journal-toc p > a').forEach((item) => { item.classList.add('toc-item-title'); });
        let new_target = '';
        let search_string = '';
        document.querySelectorAll('#journal-toc p > a').forEach((item) => {
            new_target = new URL(window.location.origin);
            search_string = '"' + item.text.trim() + '"';
            new_target.searchParams.set('s', search_string);
            item.setAttribute('href', new_target);
        });

    }, false);

})();