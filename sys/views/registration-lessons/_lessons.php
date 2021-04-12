<?php

use yii\grid\GridView;

?>

<div class="col-sm-6">
    <h3><?= Yii::t('app', $title) ?></h3>

    <?= GridView::widget([
        'dataProvider' => $model,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'lesson.title',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}'
            ],
        ],
    ]); ?>
</div>