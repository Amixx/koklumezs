<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesfilesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Faili';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lecturesfiles-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Pievienot failu', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title:ntext',
            'file:ntext',
            [
                'attribute' => 'lecture_id',
                'format' => 'raw',
                'value' => 'lecture.title',
                'filter'=> Html::dropDownList('UserLecturesSearch[lecture_id]',isset($get['UserLecturesSearch']['lecture_id']) ? $get['UserLecturesSearch']['lecture_id'] : '' ,$lectures,['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ], 

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
