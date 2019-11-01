<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserlectureevaluationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Studentu vērtējumi';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="userlectureevaluations-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Izveidot studenta vērtējumu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'lecture_id',
            'evaluation_id',
            'user_id',
            'evaluation:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
