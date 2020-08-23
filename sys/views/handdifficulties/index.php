<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\HanddifficultiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = \Yii::t('app',  'Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="handdifficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app',  'Add category'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            [
                'attribute' => 'hand',
                'format' => 'raw',
                'value' => function ($dataProvider) {
                    return $dataProvider->hand == 'left' ? \Yii::t('app',  'Left') : \Yii::t('app',  'Right');
                },
                'filter' => Html::dropDownList('HanddifficultiesSearch[hand]', isset($get['HanddifficultiesSearch']['hand']) ? $get['HanddifficultiesSearch']['hand'] : '', ['left' => \Yii::t('app',  'Left'), 'right' => \Yii::t('app',  'Right')], ['prompt' => '-- ' . \Yii::t('app',  'Show all') . ' --', 'class' => 'form-control']),
            ],
            'category:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>