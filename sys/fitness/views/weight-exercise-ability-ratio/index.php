<?php

use app\fitness\models\Tag;
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = \Yii::t('app', 'Weight exercise ability ratios');

?>
<div class="lectures-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Create weight exercise ability ratio'), ['create'], ['class' => 'btn btn-success']) ?>
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
            [
                'attribute' => 'exercise_id_1',
                'value' => function ($dataProvider) {
                    return $dataProvider['exercise1']['name'];
                }
            ],
            [
                'attribute' => 'exercise_id_2',
                'value' => function ($dataProvider) {
                    return $dataProvider['exercise2']['name'];
                }
            ],
            'ratio_percent',
        ],
    ]); ?>
</div>