<?php

use app\fitness\models\Tag;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Tags');

?>
<div class="lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create tag'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'value',
            'description',
            [
                'label' => 'Tips',
                'value' => function($dataProvider) {
                    return Tag::getTagTypeLabel($dataProvider['type']);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{update} {delete}',
            ],

        ],
    ]); ?>


</div>