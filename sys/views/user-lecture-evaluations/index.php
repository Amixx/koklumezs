<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;

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
            [
                'attribute' => 'lecture_id',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter'=> Html::dropDownList('UserlectureevaluationsSearch[lecture_id]',isset($get['UserlectureevaluationsSearch']['lecture_id']) ? $get['UserlectureevaluationsSearch']['lecture_id'] : '' ,$lectures,['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ], 
            //'evaluation_id',
            [
                'attribute' => 'evaluation_id',
                'format' => 'raw',
                'value' => 'evalua.title',
                'filter'=> Html::dropDownList('UserlectureevaluationsSearch[evaluation_id]',isset($get['UserlectureevaluationsSearch']['evaluation_id']) ? $get['UserlectureevaluationsSearch']['evaluation_id'] : '' ,$evaluations,['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ], 
            [
                'attribute' => 'user_id',
                'format' => 'raw',
                'value' => 'student.email',
                'filter'=> Html::dropDownList('UserlectureevaluationsSearch[user_id]',isset($get['UserlectureevaluationsSearch']['user_id']) ? $get['UserlectureevaluationsSearch']['user_id'] : '' ,$students,['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ],
            'evaluation:ntext',
            [
                'attribute' => 'created',
                'value' => 'created',
                'filter' => DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'created',
                        'language' => 'lv',
                        'dateFormat' => 'yyyy-MM-dd',
                    ]),
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
