<?php

use yii\helpers\Html;
?>

<head>
    <?= $this->render('gtag') ?>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#222">
    <link rel="shortcut icon" href="<?= Yii::$app->request->baseUrl; ?>/favicon.png?v=1" type="image/png" />
    <link rel="apple-touch-icon" href="<?= Yii::$app->request->baseUrl; ?>/favicon.png?v=1" type="image/png" />
    <link rel="manifest" href="<?= Yii::$app->request->baseUrl; ?>/manifest.webmanifest">
    <?php $this->registerCsrfMetaTags() ?> <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>