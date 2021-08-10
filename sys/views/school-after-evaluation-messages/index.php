<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Messages after evaluation');

?>
<div class="settings-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('message-table', [
        'messages' => $messages,
        'evaluation' => 2,
        'title' => \Yii::t('app', 'Super easy, boring')
    ]) ?>

    <?= $this->render('message-table', [
        'messages' => $messages,
        'evaluation' => 4,
        'title' => \Yii::t('app', 'Easy')
    ]) ?>

    <?= $this->render('message-table', [
        'messages' => $messages,
        'evaluation' => 6,
        'title' => \Yii::t('app', 'Goal')
    ]) ?>

    <?= $this->render('message-table', [
        'messages' => $messages,
        'evaluation' => 8,
        'title' => \Yii::t('app', 'Hard')
    ]) ?>

    <?= $this->render('message-table', [
        'messages' => $messages,
        'evaluation' => 10,
        'title' => \Yii::t('app', 'Challenging')
    ]) ?>

</div>