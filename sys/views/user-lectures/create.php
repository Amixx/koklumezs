<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\UserLectures */

$this->title = 'Piešķirt lekciju';
$this->params['breadcrumbs'][] = ['label' => 'Piešķirtās lekcijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-lectures-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'students' => $students,
        'lectures' => $lectures,
        'outofLectures' => $outofLectures
    ]) ?>

</div>
