<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\School;

$school = null;
$schoolId = array_key_exists('s', $this->params) ? $this->params['s'] : null;
$lang = array_key_exists('l', $this->params) ? $this->params['l'] : 'lv';

if (isset($schoolId)) {
    $school = School::findOne($schoolId);
    $signupUrl = "/site/sign-up?s=" . $schoolId . "&l=" . $lang;
}

$wrapperBackground = $school != null && $school->background_image != null ? "url($school->background_image)" : "white";;
$logo = $school != null && $school->logo != null ? "url($school->logo)" : "white";

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-133287428-1"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-133287428-1');
    </script>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#222">
    <link rel="shortcut icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.png?v=1" type="image/png" />
    <link rel="apple-touch-icon" href="<?php echo Yii::$app->request->baseUrl; ?>/favicon.png?v=1" type="image/png" />
    <link rel="manifest" href="<?php echo Yii::$app->request->baseUrl; ?>/manifest.webmanifest">
    <?php $this->registerCsrfMetaTags() ?> <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="background-overlay"></div>
    <div class="wrap" style="background: <?= $wrapperBackground ?>">
        <?php
        ob_start(); ?>

        <div
            id="logo"
            title="<?= Yii::$app->name ?>"
            class="school-logo"
            style="background-image: <?= $logo ?>;">
        </div>
        <?php
        $logo = ob_get_clean();
        NavBar::begin([
            'brandLabel' => $logo,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
                'id' => 'navbar',
            ],
        ]);
        ?>
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
        </script>
        <?php
        if (isset($schoolId)) { $navItems[] = ['label' => \Yii::t('app',  'Sign up'), 'url' => [$signupUrl]]; 

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $navItems
            ]);
        }
        NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; koklumezs.lv <?= date('Y') ?></p>
            <p class="pull-right"></p>
        </div>
    </footer>

    <div id="install-prompt" class="a2hs"><?= \Yii::t('app',  'Add to home screen') ?></div>

    <?php $this->endBody() ?>

    <script>
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
</body>

</html>
<?php $this->endPage() ?>