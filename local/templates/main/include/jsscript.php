<script>
    //svg sprite in local storage
    (function() {
        const src = '<?=SITE_TEMPLATE_PATH?>/img/sprite.svg',
            version = 103,
            site = 'energy-h.ru',
            storage = {
                version: localStorage.getItem('svg-sprite-version' + site),
                content: localStorage.getItem('svg-sprite-content' + site)
            };
        if (storage.version !== null && storage.content !== null) {
            if (version === Number(storage.version)) {
                appendSvg(storage.content);
            } else {
                svgRequest(src, version);
            }
        } else {
            svgRequest(src, version);
        }

        function svgRequest(src, version) {
            let request = new XMLHttpRequest();
            request.open('GET', src, true);
            request.send();
            request.onload = function(e) {
                if (request.status >= 200 && request.status < 400) {
                    appendSvg(request.responseText);
                    localStorage.setItem('svg-sprite-content' + site, request.responseText);
                    localStorage.setItem('svg-sprite-version' + site, String(version));
                }
            }
        }

        function appendSvg(content) {
            const div = document.createElement('div');
            div.innerHTML = content;
            document.body.insertBefore(div.children[0], document.body.childNodes[0]);
        }
    }());
</script>