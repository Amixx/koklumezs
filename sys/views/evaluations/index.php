<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvaluationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app',  'Evaluations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluations-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app',  'Create evaluation'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'title:ntext',
            [
                'attribute' => 'type',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->type == 'zvaigznes' ? \Yii::t('app',  'Stars') : \Yii::t('app',  'Text');
                },
                'filter' => Html::dropDownList('EvaluationsSearch[type]', isset($get['EvaluationsSearch']['type']) ? $get['EvaluationsSearch']['type'] : '', ['zvaigznes' => \Yii::t('app',  'Stars'), 'teksts' => \Yii::t('app',  'Text')], ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>