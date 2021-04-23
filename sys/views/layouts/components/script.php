<div id="install-prompt" class="a2hs"><?= \Yii::t('app',  'Add to home screen') ?></div>
<script>
    var localhosts = ["localhost", "127.0.0.1"];
    var origin = window.location.origin;

    var IS_LOCAL = false;
    localhosts.forEach(function(host) {
        if (origin.indexOf(host) !== -1) {
            IS_LOCAL = true;
        }
    });

    var IS_PROD = !IS_LOCAL;

    function getUrl(url) {
        var prefix = IS_PROD ? '/sys' : '';
        return prefix + url;
    }

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register(getUrl('/sw.js'), {
                scope: getUrl('/')
            })
            .then(function(registration) {});

        navigator.serviceWorker.ready.then(function(registration) {});
    }


    /* When the user scrolls down, hide the navbar. When the user scrolls up, show the navbar */
    var prevScrollpos = window.pageYOffset;
    window.onscroll = function() {
        var currentScrollPos = window.pageYOffset;
        if (prevScrollpos > currentScrollPos) {
            document.getElementById("navbar").style.top = "0";
            document.getElementById("chat-btn-with-icons").style.top = "20px";
        } else {
            document.getElementById("navbar").style.top = "-90px";
            document.getElementById("chat-btn-with-icons").style.top = "-110px";
        }
        prevScrollpos = currentScrollPos;
    }

    window.addEventListener('beforeinstallprompt', function(e) {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;

        if (window.innerWidth < 769) {
            document.getElementById("install-prompt").style.display = "block";
        }
    });

    document.getElementById("install-prompt").addEventListener('click', (e) => {
        // Hide the app provided install promotion
        document.getElementById("install-prompt").style.background = "gainsboro";
        // Show the install prompt
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                document.getElementById("install-prompt").style.display = "none";
            } else {
                document.getElementById("install-prompt").style.background = "goldenrod";
            }
        });
    });
</script>