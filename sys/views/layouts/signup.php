<?php

use app\widgets\Alert;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\bootstrap\Nav;

$layoutHelper = $this->params['layoutHelper'];
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

        <div id="logo" title="<?= Yii::$app->name ?>" class="school-logo" style="background-image: <?= $logo ?>;">
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

        <?php
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [['label' => \Yii::t('app',  'Log in'), 'url' => [$layoutHelper->getLoginUrl()]]]
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

    <?= $this->render('components/footer') ?>
    <?php $this->endBody() ?>
    <?php $this->render('components/script'); ?>
</body>

</html>
<?php $this->endPage() ?>