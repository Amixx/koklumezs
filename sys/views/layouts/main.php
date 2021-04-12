<?php

use app\widgets\Alert;
use app\widgets\ChatRoom;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\helpers\UserLayoutHelper;

$layoutHelper = new UserLayoutHelper();
$logo = $layoutHelper->getLogo();

AppAsset::register($this);
$this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<?= $this->render('components/head') ?>

<body>
    <?php $this->beginBody() ?>

    <div class="background-overlay"></div>
    <div class="wrap" style="background: <?= $layoutHelper->getWrapperBackground() ?>">
        <?php ob_start(); ?>

        <div id="logo" title="<?= Yii::$app->name ?>" class="<?= $layoutHelper->getLogoClass() ?>" style="background-image: <?= $logo ?>;">
        </div>
        <?= $layoutHelper->getChatButton() ?>
        <?php
        $logo = ob_get_clean();

        NavBar::begin([
            'brandLabel' => $logo,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => $layoutHelper->getNavbarClass(),
                'id' => 'navbar',
            ],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $layoutHelper->getNavItems()
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

    <?php
    if ($layoutHelper->shouldRenderChat()) {

        echo ChatRoom::widget([
            'url' => \yii\helpers\Url::to(['/chat/send-chat']),
            'userModel' =>  \app\models\User::class,
            'recipientId' => $layoutHelper->getChatRecipientId(),
        ]);
    } ?>

    <?= $this->render('components/footer') ?>
    <?php $this->endBody() ?>
    <?php $this->render('components/script'); ?>
</body>

</html>
<?php $this->endPage() ?>