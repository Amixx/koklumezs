<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvaluationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Novērtējumi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluations-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Izveidot novērtējumu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title:ntext',
            'type',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
