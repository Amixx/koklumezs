<?php

use app\widgets\Alert;
use app\widgets\ChatRoom;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\models\School;
use app\models\Users;
use app\models\SchoolTeacher;

$isGuest = Yii::$app->user->isGuest;
$isAdmin = !$isGuest && Yii::$app->user->identity->user_level == 'Admin';
$isTeacher = !$isGuest && Yii::$app->user->identity->user_level == 'Teacher';
$isStudent = !$isGuest && Yii::$app->user->identity->user_level == 'Student';

$school = null;
if ($isTeacher) {
    $school = School::getByTeacher(Yii::$app->user->identity->id);
    $chatButtonText = "Chat with students";
} else if ($isStudent) {
    $school = School::getByStudent(Yii::$app->user->identity->id);
    $chatButtonText = "Chat with teacher";
    $schoolTeacher = SchoolTeacher::getBySchoolId($school['id']);
}

$wrapperBackground = $school != null && $school->background_image != null ? "url($school->background_image)" : "white";
$logo = $school != null && $school->logo != null ? "url($school->logo)" : "white";

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

        <div
            id="logo"
            title="<?= Yii::$app->name ?>"
            class="school-logo <?= $isAdmin ? 'admin' : '' ?>"
            style="background-image: <?= $logo ?>;">
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
            $navItems[] = [
                'label' => \Yii::t('app',  'Subscription plans'),
                'url' => ['/school-sub-plans'], 
                'active' =>  in_array(\Yii::$app->controller->id, ['school-sub-plans']),
                'items' => [
                    ['label' => \Yii::t('app',  'Subscription plans'), 'url' => ['/school-sub-plans']],
                    ['label' => \Yii::t('app',  'Plan parts'), 'url' => ['/plan-parts']],
                    ['label' => \Yii::t('app',  'Plan pauses'), 'url' => ['/student-subplan-pauses']],
                ],
                'options' => ['class' => 'nav-item dropdown']
            ];
            $navItems[] = ['label' => '+', 'url' => ['/school-sub-plans/create'], 'active' =>  in_array(\Yii::$app->controller->id, ['school-sub-plans']),];
            $navItems[] = [
                'label' => \Yii::t('app',  'Metrics'),
                'active' =>  in_array(\Yii::$app->controller->id, ['user-lecture-evaluations']) and Yii::$app->controller->action->actionMethod != "actionComments",
                'items' => [
                    ['label' => \Yii::t('app',  'Student evaluations'), 'url' => ['/user-lecture-evaluations']],
                    ['label' => \Yii::t('app',  'Sent invoices'), 'url' => ['/sent-invoices']],
                ],
                'options' => ['class' => 'nav-item dropdown']
            ];
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
            $navItems[] = ['label' => \Yii::t('app',  'FAQs'), 'url' => ['/school-faqs/for-students'], 'active' =>  in_array(\Yii::$app->controller->id, ['school-faqs'])];

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

        <?php
        $renderChat = $isStudent || $isTeacher;

        if($renderChat){
            $recipientId = $isStudent ? $schoolTeacher['user']['id'] : null;
        ?>
            <button class="btn btn-success teacher-communication-button" id="chat-toggle-button" data-toggle="modal" data-target="#chatModal">
                <?= \Yii::t('app',  $chatButtonText) ?>
                <div id="notification-badges">               
                <?php if (Users::isCurrentUserTeacher()) {?>     
                    <span class="chat-unread-count-groups"></span>
                <?php } ?>
                <span class="chat-unread-count"></span>
                </div>
            </button>
            <?=        
            ChatRoom::widget([
                'url' => \yii\helpers\Url::to(['/chat/send-chat']),
                'userModel' =>  \app\models\User::className(),
                'recipientId' => $recipientId,
            ]); ?>
        <?php } ?>        
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
    <script src="<?= Yii::$app->request->baseUrl ?>/js/Youtube.min.js"></script>
</body>

</html>
<?php $this->endPage() ?>