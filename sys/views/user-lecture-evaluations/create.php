<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Userlectureevaluations */

$this->title = \Yii::t('app',  'Create student evaluation');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app',  'Student evaluations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="userlectureevaluations-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>