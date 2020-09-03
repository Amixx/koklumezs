<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluations */

$this->title = \Yii::t('app',  'Create school evaluation');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  \Yii::t('app',  'School evaluations')), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluations-create">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>