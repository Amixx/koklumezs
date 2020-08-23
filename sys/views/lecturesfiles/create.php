<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Lecturesfiles */

$this->title = \Yii::t('app',  'Add file');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Files'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lecturesfiles-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'lectures' => $lectures,
        'get' => $get
    ]) ?>

</div>