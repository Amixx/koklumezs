<div id="install-prompt" class="a2hs"><?= \Yii::t('app',  'Add to home screen') ?></div>
<script>
    /* When the user scrolls down, hide the navbar. When the user scrolls up, show the navbar */
    var prevScrollpos = window.pageYOffset;
    window.onscroll = function() {
        var currentScrollPos = window.pageYOffset;
        if (prevScrollpos > currentScrollPos) {
            document.getElementById("navbar").style.top = "0";
        } else {
            document.getElementById("navbar").style.top = "-90px";
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