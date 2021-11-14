<?php

use yii\helpers\Html;

$this->title = $subplan['plan'] ? $subplan['plan']['name'] : "";
\yii\web\YiiAsset::register($this);
?>
<div class="difficulties-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <h3><?= Yii::t('app', 'Plan files') ?></h3>
    <?php if (!empty($planFiles)) { ?>
        <?php foreach ($planFiles as $file) { ?>
            <p><a href="<?= $file['file'] ?>" target="_blank"><?= $file['title'] ?></a></p>
        <?php } ?>
    <?php } else { ?>
        <p><?= Yii::t('app', 'This plan has no files') ?>!</p>
    <?php } ?>
</div>