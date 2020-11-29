<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = \Yii::t('app', 'Create a FAQ');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'FAQs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="difficulties-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>