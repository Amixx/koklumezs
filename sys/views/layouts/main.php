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

$isGuest = Yii::$app->user->isGuest;
$isAdmin = !$isGuest && Yii::$app->user->identity->user_level == 'Admin';
$isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
$isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

$school = null;
if ($isTeacher) {
    $school = School::getByTeacher(Yii::$app->user->identity->id);
} else if ($isStudent) {
    $school = School::getByStudent(Yii::$app->user->identity->id);
}

$wrapperBackground = $school != null && $school->background_image != null ? "url($school->background_image)" : "white";

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
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

    <div class="wrap" style="background: <?= $wrapperBackground ?>">
        <?php
        ob_start(); ?>

        <div id="logo" title="<?= Yii::$app->name ?>" class="<?= $isAdmin ? 'admin' : '' ?>">
            <!--?xml version="1.0" encoding="UTF-8"?-->
            <svg preserveAspectRatio="xMidYMid meet" data-bbox="1.3 1.3 176 176" viewBox="0 0 178.6 178.6" xmlns="http://www.w3.org/2000/svg" data-type="ugc" role="img">
                <g>
                    <path fill="none" stroke="#fff" stroke-miterlimit="10" stroke-width="2.59" d="M177.3 89.3c0 48.601-39.399 88-88 88s-88-39.399-88-88 39.399-88 88-88 88 39.399 88 88z"></path>
                    <path d="M35.07 35.47c-.55-.35-.64-.58-1.61.55l-18 20.36s-.48 1.37.78 1.52a1.52 1.52 0 0 0 1.22-1.63l12.88-7.4 5.7-11.66s.52-.7 0-1.07-.67-.48-.97-.67z" fill="#fff"></path>
                    <path d="M86.92 55.48a1.39 1.39 0 0 0-2 .3L67.57 75.4s-.75 1-.08 1.78a1.37 1.37 0 0 0 1.37.39 1.67 1.67 0 0 0 1.38-2.06L81 71.32 87.51 57a1.26 1.26 0 0 0-.59-1.52z" fill="#fff"></path>
                    <path d="M35.81 55.39a1.65 1.65 0 0 0-2 .3L16.47 75.31a1.63 1.63 0 0 0-.08 1.78 1.43 1.43 0 0 0 1.37.61 2 2 0 0 0 1.81-2.46l13.68-5.64L36 58.35s.73-2.35-.19-2.96z" fill="#fff"></path>
                    <path d="M40.26 35.47c.55-.35.64-.58 1.61.55l18 20.36s.48 1.37-.78 1.52a1.52 1.52 0 0 1-1.22-1.63L45 48.87 39.27 37.2s-.52-.7 0-1.07.73-.47.99-.66z" fill="#fff"></path>
                    <path d="M137.92 54.53c-.59-.38-.7-.63-1.74.6l-19.52 22.08s-.52 1.49.84 1.65a1.65 1.65 0 0 0 1.33-1.77l14-8L139 56.41s.56-.76 0-1.16-.76-.52-1.08-.72z" fill="#fff"></path>
                    <path d="M143.53 54.53c.59-.38.7-.63 1.74.6l19.52 22.09s.52 1.49-.84 1.65a1.65 1.65 0 0 1-1.33-1.77l-14-8-6.18-12.65s-.56-.76 0-1.16.76-.56 1.09-.76z" fill="#fff"></path>
                    <path d="M91.23 55.48a1.39 1.39 0 0 1 2 .3l17.34 19.62s.75 1 .08 1.78a1.37 1.37 0 0 1-1.37.39 1.67 1.67 0 0 1-1.38-2.06l-10.75-4.19L90.64 57a1.26 1.26 0 0 1 .59-1.52z" fill="#fff"></path>
                    <path d="M39.52 55.39a1.65 1.65 0 0 1 2 .3l17.34 19.62a1.63 1.63 0 0 1 .08 1.78 1.43 1.43 0 0 1-1.37.61 2 2 0 0 1-1.81-2.46l-13.68-5.63-2.78-11.26s-.7-2.35.22-2.96z" fill="#fff"></path>
                    <path d="M92.8 35.27l7.7 5.73 9.14 13.43-5.18 3.39L91.93 39l-.64-1-.66-1.18a.45.45 0 0 1 .14-.59l1.5-1a.45.45 0 0 1 .53.04z" fill="#fff"></path>
                    <path d="M42 76.32l13 12.49 7.16 10.51-6.21 4.06-15-22.56-.76-1.16-.79-1.41a.54.54 0 0 1 .16-.7l1.8-1.23a.54.54 0 0 1 .64 0z" fill="#fff"></path>
                    <path d="M94.36 77.45L104.6 86a14.62 14.62 0 0 1 2.4 2.66l5.6 8a2 2 0 0 1-.6 2.81l-6.41 3.82-14-23-.71-1.18a2.59 2.59 0 0 1-.18-2.1 1.24 1.24 0 0 1 .86-.7c.9-.31 2.62.99 2.8 1.14z" fill="#fff"></path>
                    <path d="M85.34 35.27L77.64 41 68.5 54.48l5.18 3.39L86.21 39l.64-1 .66-1.18a.45.45 0 0 0-.14-.59l-1.5-1a.45.45 0 0 0-.53.04z" fill="#fff"></path>
                    <path d="M33.36 76.32l-13 12.49-7.17 10.51 6.21 4.06 15-22.56.76-1.16.79-1.41a.54.54 0 0 0-.16-.7L34 76.31a.54.54 0 0 0-.64.01z" fill="#fff"></path>
                    <path d="M144.87 76.32l13 12.49L165 99.32l-6.21 4.06-15-22.56-.76-1.16-.79-1.41a.54.54 0 0 1 .16-.7l1.8-1.23a.54.54 0 0 1 .67 0z" fill="#fff"></path>
                    <path d="M136.58 76.32l-13 12.49-7.16 10.51 6.21 4.06 15-22.56.76-1.16.79-1.41a.54.54 0 0 0-.16-.7l-1.8-1.23a.54.54 0 0 0-.64 0z" fill="#fff"></path>
                    <path d="M83.78 77.45L73.54 86a14.62 14.62 0 0 0-2.4 2.66l-5.6 8a2 2 0 0 0 .6 2.81l6.41 3.82 14-23 .71-1.18A2.59 2.59 0 0 0 87.4 77a1.24 1.24 0 0 0-.86-.7c-.86-.3-2.54 1-2.76 1.15z" fill="#fff"></path>
                    <path d="M142.28 37.08L149 50.14 159.63 54a.64.64 0 0 1 .5.76.32.32 0 0 1-.35.24.37.37 0 0 1-.1-.49l-1-.31a1.57 1.57 0 0 0 .42 1.52 1.23 1.23 0 0 0 2.05-.87s.39-.78-1.42-2.59c-1.64-1.64-12.87-14.21-15-16.63a.7.7 0 0 0-.84-.15l-1.32.68a.7.7 0 0 0-.29.92z" fill="#fff"></path>
                    <path d="M139.15 37.15l-6.66 13L121.84 54a.64.64 0 0 0-.5.76.32.32 0 0 0 .35.24.37.37 0 0 0 .1-.49l1-.31a1.57 1.57 0 0 1-.42 1.52 1.23 1.23 0 0 1-2.05-.87s-.39-.78 1.42-2.59c1.63-1.63 12.75-14.08 15-16.59a.77.77 0 0 1 .92-.17l1.21.62a.77.77 0 0 1 .28 1.03z" fill="#fff"></path>
                    <path d="M91.12 16.47l6.7 13.06 10.65 3.87a.64.64 0 0 1 .5.76.32.32 0 0 1-.35.24.37.37 0 0 1-.1-.49l-1-.31a1.57 1.57 0 0 0 .48 1.5 1.23 1.23 0 0 0 2.05-.87s.39-.78-1.42-2.59C107 30 95.73 17.42 93.57 15a.7.7 0 0 0-.84-.15l-1.32.68a.7.7 0 0 0-.29.94z" fill="#fff"></path>
                    <path d="M88 16.53l-6.66 13-10.67 3.86a.64.64 0 0 0-.5.76.32.32 0 0 0 .35.24.37.37 0 0 0 .1-.49l1-.31a1.57 1.57 0 0 1-.42 1.52 1.23 1.23 0 0 1-2.05-.87s-.39-.78 1.42-2.59C72.16 30 83.27 17.56 85.52 15a.77.77 0 0 1 .92-.17l1.21.62a.77.77 0 0 1 .35 1.08z" fill="#fff"></path>
                    <path d="M42.55 115.07a1.11 1.11 0 0 0-.88.33 1.29 1.29 0 0 0-.29.88v22a1.31 1.31 0 0 0 .28.88 1.34 1.34 0 0 0 1.77 0 1.28 1.28 0 0 0 .29-.88v-22a1.32 1.32 0 0 0-.28-.88 1.11 1.11 0 0 0-.89-.33z" fill="#fff"></path>
                    <path d="M141.86 107.74l-3 3.34-3-3.34H3.23v2.38h.58a88 88 0 0 0 170.52 0h1v-2.38zM32.3 141.31a.5.5 0 0 1-.42.16h-1a1.19 1.19 0 0 1-.83-.29 1.16 1.16 0 0 1-.38-.72l-1.54-12.15-1-.12v12.09a1.26 1.26 0 0 1-.29.86 1.11 1.11 0 0 1-.88.33h-1.1a.37.37 0 0 1-.3-.12.45.45 0 0 1-.1-.3v-27.5a.45.45 0 0 1 .1-.3.37.37 0 0 1 .3-.12H26a1.24 1.24 0 0 1 .85.28 1 1 0 0 1 .32.75v11.72l1-.1 1.46-11.64a1.17 1.17 0 0 1 .38-.72 1.19 1.19 0 0 1 .83-.29h1a.5.5 0 0 1 .42.16.39.39 0 0 1 .09.34l-1.72 13.12L32.42 141a.45.45 0 0 1-.12.31zm14.13-3a3.28 3.28 0 0 1-1 2.55 4.1 4.1 0 0 1-2.87.93 4.91 4.91 0 0 1-1.6-.24 3.58 3.58 0 0 1-1.22-.7 3 3 0 0 1-.79-1.09 3.64 3.64 0 0 1-.27-1.45v-22a3.28 3.28 0 0 1 1-2.55 4.1 4.1 0 0 1 2.87-.93 4.94 4.94 0 0 1 1.59.24 3.57 3.57 0 0 1 1.24.7 3 3 0 0 1 .79 1.09 3.65 3.65 0 0 1 .27 1.45zm14.17 3a.5.5 0 0 1-.42.16h-1a1.19 1.19 0 0 1-.83-.29 1.16 1.16 0 0 1-.38-.72l-1.54-12.15-1-.12v12.09a1.26 1.26 0 0 1-.29.86 1.11 1.11 0 0 1-.88.33h-1.09a.37.37 0 0 1-.3-.12.45.45 0 0 1-.1-.3v-27.5a.45.45 0 0 1 .1-.3.37.37 0 0 1 .3-.12h1.13a1.24 1.24 0 0 1 .85.28 1 1 0 0 1 .32.75v11.72l1-.1 1.46-11.64a1.17 1.17 0 0 1 .38-.72 1.19 1.19 0 0 1 .83-.29h1a.5.5 0 0 1 .42.16.39.39 0 0 1 .09.34l-1.75 13.12L60.72 141a.45.45 0 0 1-.11.31zm9.86 6.53l-1.27 3.88a.11.11 0 0 1-.11.07h-.48c-.06 0-.16 0-.16-.11V145h2zm2.83-6.78a.37.37 0 0 1-.12.3.45.45 0 0 1-.3.1h-5.39a.37.37 0 0 1-.3-.12.45.45 0 0 1-.1-.3v-27.5a.45.45 0 0 1 .1-.3.37.37 0 0 1 .3-.12h1.13a1.24 1.24 0 0 1 .85.28 1 1 0 0 1 .32.75v25h2.49a1 1 0 0 1 .75.32 1.25 1.25 0 0 1 .28.85zm13.76-2.75a3.28 3.28 0 0 1-1 2.55 4.1 4.1 0 0 1-2.87.93 4.91 4.91 0 0 1-1.6-.24 3.58 3.58 0 0 1-1.22-.7 3 3 0 0 1-.79-1.09 3.64 3.64 0 0 1-.27-1.45v-24.76a.45.45 0 0 1 .1-.3.37.37 0 0 1 .3-.12h1.13a1.24 1.24 0 0 1 .85.28 1 1 0 0 1 .32.75v24.16a1.31 1.31 0 0 0 .28.88 1.34 1.34 0 0 0 1.77 0 1.28 1.28 0 0 0 .29-.88v-24.16a.94.94 0 0 1 .32-.75 1.24 1.24 0 0 1 .85-.28h1.13a.37.37 0 0 1 .3.12.46.46 0 0 1 .1.3zM117 141a.46.46 0 0 1-.1.3.37.37 0 0 1-.3.12h-1.13a1.14 1.14 0 0 1-.89-.33 1.16 1.16 0 0 1-.28-.86l.61-19.85-2.09 20a1 1 0 0 1-.47.72 1.6 1.6 0 0 1-.93.29 1.6 1.6 0 0 1-.93-.29 1 1 0 0 1-.47-.72l-2.11-20.07.61 19.89a1.16 1.16 0 0 1-.28.86 1.14 1.14 0 0 1-.89.33h-1.13a.37.37 0 0 1-.3-.12.45.45 0 0 1-.1-.3v-27.5a.45.45 0 0 1 .1-.3.37.37 0 0 1 .3-.12h1.38a1.78 1.78 0 0 1 .6.11 1.28 1.28 0 0 1 .6.54 4.53 4.53 0 0 1 .52 1.25 13.48 13.48 0 0 1 .38 2.24l1.76 17.13 1.78-17.13a16.77 16.77 0 0 1 .34-2.22 5 5 0 0 1 .45-1.25 1.21 1.21 0 0 1 .53-.55 1.43 1.43 0 0 1 .57-.12h1.54a.37.37 0 0 1 .3.12.46.46 0 0 1 .1.3zm12.77-26.83a1.24 1.24 0 0 1-.28.85.94.94 0 0 1-.75.32h-2.49v10.92H129a.46.46 0 0 1 .3.1.38.38 0 0 1 .12.3v.69a1.25 1.25 0 0 1-.28.85 1 1 0 0 1-.75.32h-2.08v10.63h2.49a1 1 0 0 1 .75.32 1.25 1.25 0 0 1 .28.85v.69a.37.37 0 0 1-.12.3.45.45 0 0 1-.3.1H124a.37.37 0 0 1-.3-.12.45.45 0 0 1-.1-.3v-27.5a.45.45 0 0 1 .1-.3.37.37 0 0 1 .3-.12h5.41a.46.46 0 0 1 .3.1.37.37 0 0 1 .12.3zm12.29 26.85a.37.37 0 0 1-.12.3.45.45 0 0 1-.3.1H136a.4.4 0 0 1-.31-.12.37.37 0 0 1-.09-.3l3.2-25.66h-1.92a1 1 0 0 1-.75-.32 1.25 1.25 0 0 1-.28-.85v-.69a.4.4 0 0 1 .12-.31.37.37 0 0 1 .3-.09h4.27a1.31 1.31 0 0 1 1 .29.92.92 0 0 1 .19.76l-3.3 25H141a1 1 0 0 1 .75.32 1.25 1.25 0 0 1 .28.85zm14.07-18.59a.46.46 0 0 1-.1.3.37.37 0 0 1-.3.12h-1.13a1.11 1.11 0 0 1-.88-.33 1.25 1.25 0 0 1-.29-.86v-5.43a1.28 1.28 0 0 0-.29-.88 1.34 1.34 0 0 0-1.77 0 1.31 1.31 0 0 0-.28.88 23.5 23.5 0 0 0 .21 3.32 20.12 20.12 0 0 0 .58 2.73 23.78 23.78 0 0 0 .82 2.37l.92 2.22q.46 1.11.92 2.26a23.41 23.41 0 0 1 .82 2.51 23 23 0 0 1 .58 3 28.23 28.23 0 0 1 .21 3.64 3.66 3.66 0 0 1-.27 1.45 3 3 0 0 1-.79 1.09 3.57 3.57 0 0 1-1.24.7 4.94 4.94 0 0 1-1.59.24 4.1 4.1 0 0 1-2.87-.93 3.28 3.28 0 0 1-1-2.55v-6.2a.45.45 0 0 1 .1-.3.37.37 0 0 1 .3-.12h1.13a1.11 1.11 0 0 1 .88.33 1.26 1.26 0 0 1 .29.86v5.43a1.29 1.29 0 0 0 .29.88 1.34 1.34 0 0 0 1.77 0 1.32 1.32 0 0 0 .28-.88 23.1 23.1 0 0 0-1.32-8.07q-.57-1.59-1.21-3t-1.21-3a24 24 0 0 1-.94-3.48 23.29 23.29 0 0 1-.37-4.46 3.64 3.64 0 0 1 .27-1.45 3 3 0 0 1 .79-1.09 3.58 3.58 0 0 1 1.22-.7 4.91 4.91 0 0 1 1.6-.24 4.1 4.1 0 0 1 2.87.93 3.28 3.28 0 0 1 1 2.55z" fill="#fff"></path>
                </g>
            </svg>
        </div>
        <?php
        $logo = ob_get_clean();
        $classes = 'navbar-inverse navbar-fixed-top';
        if ($isAdmin) $classes .= ' navbar-admin';

        NavBar::begin([
            'brandLabel' => $logo,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => $classes,
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
        $navItems = [];
        if ($isGuest) {
        } else {
            $navEnd = '<li>'
                . Html::beginForm(['/site/logout'], 'post')
                . Html::submitButton(
                    \Yii::t('app',  'Sign out') . ' (' . Yii::$app->user->identity->first_name . ' ' . Yii::$app->user->identity->last_name . ')',
                    ['class' => 'btn btn-link logout']
                )
                . Html::endForm()
                . '</li>';
        }
        if ($isGuest) {
            $navItems[] = ['label' => \Yii::t('app',  'Log in'), 'url' => ['/site/login']];
        } elseif ($isAdmin) {
            $navItems[] = ['label' => 'Piešķiršana', 'url' => ['/assign'], 'active' =>  in_array(\Yii::$app->controller->id, ['assign']),];
            $navItems[] = ['label' => 'Piešķirts', 'url' => ['/user-lectures'], 'active' =>  in_array(\Yii::$app->controller->id, ['user-lectures']),];
            $navItems[] = ['label' => \Yii::t('app',  'Lessons'), 'url' => ['/lectures'], 'active' =>  in_array(\Yii::$app->controller->id, ['lectures']),];
            $navItems[] = ['label' => 'Faili', 'url' => ['/lecturesfiles'], 'active' =>  in_array(\Yii::$app->controller->id, ['lecturesfiles']),];
            $navItems[] = ['label' => 'Parametri', 'url' => ['/difficulties'], 'active' =>  in_array(\Yii::$app->controller->id, ['difficulties']),];
            $navItems[] = ['label' => \Yii::t('app',  'Section visibility'), 'url' => ['/sections'], 'active' =>  in_array(\Yii::$app->controller->id, ['sections']),];
            $navItems[] = ['label' => \Yii::t('app',  'Evaluations'), 'url' => ['/evaluations'], 'active' =>  in_array(\Yii::$app->controller->id, ['evaluations'])];
            $navItems[] = ['label' => 'Lietotāji', 'url' => ['/user'], 'active' =>  in_array(\Yii::$app->controller->id, ['user'])];
            $navItems[] = ['label' => 'Lietotāju vērtējumi', 'url' => ['/user-lecture-evaluations'], 'active' =>  in_array(\Yii::$app->controller->id, ['user-lecture-evaluations'])];
            $navItems[] = ['label' => 'Izsūtītie e-pasti', 'url' => ['/sentlectures'], 'active' =>  in_array(\Yii::$app->controller->id, ['sentlectures'])];
            $navItems[] = $navEnd;
        } elseif ($isTeacher) {
            $navItems[] = ['label' => \Yii::t('app',  'School'), 'url' => ['/assign'], 'active' =>  in_array(\Yii::$app->controller->id, ['assign']),];
            $navItems[] = ['label' => '+', 'url' => ['/assign/userlectures'], 'active' =>  in_array(\Yii::$app->controller->id, ['assign']),];
            $navItems[] = ['label' => \Yii::t('app',  'Community'), 'url' => ['/user-lecture-evaluations/comments'], 'active' =>  in_array(\Yii::$app->controller->id, ['user-lecture-evaluations']) and Yii::$app->controller->action->actionMethod == "actionComments"];
            $navItems[] = ['label' => \Yii::t('app',  'Students'), 'url' => ['/user'], 'active' =>  in_array(\Yii::$app->controller->id, ['user'])];
            $navItems[] = ['label' => '+', 'url' => ['/user/create'], 'active' =>  in_array(\Yii::$app->controller->id, ['user'])];
            $navItems[] = ['label' => \Yii::t('app',  'Lessons'), 'url' => ['/lectures'], 'active' =>  in_array(\Yii::$app->controller->id, ['lectures']),];
            $navItems[] = ['label' => '+', 'url' => ['/lectures/create'], 'active' =>  in_array(\Yii::$app->controller->id, ['lectures']),];
            $navItems[] = ['label' => \Yii::t('app',  'Subscription plans'), 'url' => ['/school-sub-plans'], 'active' =>  in_array(\Yii::$app->controller->id, ['school-sub-plans']),];
            $navItems[] = ['label' => '+', 'url' => ['/school-sub-plans/create'], 'active' =>  in_array(\Yii::$app->controller->id, ['school-sub-plans']),];
            $navItems[] = ['label' => \Yii::t('app',  'Metrics'), 'url' => ['/user-lecture-evaluations'], 'active' =>  in_array(\Yii::$app->controller->id, ['user-lecture-evaluations']) and Yii::$app->controller->action->actionMethod != "actionComments"];
            $navItems[] = ['label' => \Yii::t('app',  'Settings'), 'url' => ['/school-settings'], 'active' =>  in_array(\Yii::$app->controller->id, ['school-settings'])];

            $navItems[] = $navEnd;
        } elseif ($isStudent) {
            $commentsItemText = \Yii::t('app',  'Newest comments');
            $unseenResponsesCount = array_key_exists('unseen_responses_count', $this->params) ? $this->params['unseen_responses_count'] : null;
            if ($unseenResponsesCount && $unseenResponsesCount > 0) {
                $commentsItemText .= " ($unseenResponsesCount)";
            }

            $navItems[] = [
                'label' => \Yii::t('app',  'Lessons'),
                'active' =>  in_array(\Yii::$app->controller->id, ['lekcijas']),
                'items' => [
                    ['label' => \Yii::t('app',  'New lessons'), 'url' => ['/lekcijas?type=new']],
                    ['label' => \Yii::t('app',  'Currently learning'), 'url' => ['/lekcijas?type=learning']],
                    ['label' => \Yii::t('app',  'Favourite lessons'), 'url' => ['/lekcijas?type=favourite']]
                ],
                'options' => ['class' => 'navbar-lessons-dropdown-toggle']
            ];
            $navItems[] = ['label' => \Yii::t('app',  'Sheet music'), 'url' => ['/file'], 'active' =>  in_array(\Yii::$app->controller->id, ['file'])];
            $navItems[] = ['label' => $commentsItemText, 'url' => ['/comment-responses'], 'active' =>  in_array(\Yii::$app->controller->id, ['comment-responses'])];
            $navItems[] = ['label' => \Yii::t('app',  'Archive'), 'url' => ['/archive'], 'active' =>  in_array(\Yii::$app->controller->id, ['archive'])];
            $navItems[] = ['label' => \Yii::t('app',  'Subscription plan'), 'url' => ['/student-sub-plans/view/?id='.Yii::$app->user->identity->id], 'active' =>  in_array(\Yii::$app->controller->id, ['student-sub-plans'])];

            $navItems[] = $navEnd;
        }

        $navbarClasses = 'navbar-nav navbar-right';
        if (!$isGuest && $isStudent) {
            $navbarClasses .= ' for-students';
        }

        echo Nav::widget([
            'options' => ['class' => $navbarClasses],
            'items' => $navItems
        ]);
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
        // if ('serviceWorker' in navigator) {
        //     navigator.serviceWorker.register('/pwabuilder-sw.js', {
        //             scope: "/"
        //         })
        //         .then(function(registration) {
        //             // console.log('Service worker registration successful, scope is: ', registration.scope);
        //         })
        //         .catch(function(error) {
        //             // console.log('Service worker registration failed, error: ', error);
        //         });
        // }

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