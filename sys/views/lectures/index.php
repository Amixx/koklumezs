<?php

use yii\helpers\Html;
use yii\grid\GridView;
use  yii\jui\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\LecturesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lekcijas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Izveidot lekciju', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title',
           // 'description:ntext',
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
            [
                'attribute' => 'updated',
                'value' => 'updated',
                'filter' => DatePicker::widget([
                        'model'=>$searchModel,
                        'attribute'=>'updated',
                        'language' => 'lv',
                        'dateFormat' => 'yyyy-MM-dd',
                    ]),
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
            
            [
                'attribute' => 'author',
                'format' => 'raw',
                'value' => 'users.email',
                'filter'=> Html::dropDownList('LecturesSearch[author]',isset($get['LecturesSearch']['author']) ? $get['LecturesSearch']['author'] : '' ,$admins,['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ],     
            [
                'attribute' => 'complexity',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->complexity;
                },
                'filter'=> Html::dropDownList('LecturesSearch[complexity]',isset($get['LecturesSearch']['complexity']) ? $get['LecturesSearch']['complexity'] : '' ,app\models\Lectures::getComplexity(),['prompt'=>'-- Rādīt visus --','class' => 'form-control']),
            ],     
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
