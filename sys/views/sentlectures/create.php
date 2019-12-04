<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sentlectures */

$this->title = 'Create Sentlectures';
$this->params['breadcrumbs'][] = ['label' => 'Sentlectures', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sentlectures-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
