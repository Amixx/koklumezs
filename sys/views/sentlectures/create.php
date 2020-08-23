<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sentlectures */

$this->title = \Yii::t('app',  'Create send lectures');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Sent lectures'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sentlectures-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>