<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DifficultiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sekciju redzamība';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'is_visible',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>