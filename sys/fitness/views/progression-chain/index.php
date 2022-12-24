<?php

use app\fitness\models\Tag;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Progression chains');

?>
<div class="lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create progression chain'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => \Yii::t('app', 'Actions'),
                'template' => '{update} {delete}',
            ],
            'title',
            [
                'label' => Yii::t('app', 'Associated weight exercise'),
                'value' => function ($dataProvider) {
                    $mainExercise = $dataProvider->getMainExercise();
                    return $mainExercise ? $mainExercise->weightExercise->name : '';
                }
            ],
        ],
    ]); ?>
</div>