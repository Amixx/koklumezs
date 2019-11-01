<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Userlectureevaluations */

$this->title = 'Izveidot studenta vērtējumu';
$this->params['breadcrumbs'][] = ['label' => 'Studentu vērtējumi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="userlectureevaluations-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
