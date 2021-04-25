<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="col-sm-6">
    <h3><?= Yii::t('app', $item['title']) ?></h3>

    <?= GridView::widget([
        'dataProvider' => $item['lessons'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'lesson.title',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}'
            ],
        ],
    ]); ?>

    <?php if ($item['message']) { ?>
        <h3>Ziņa</h3>
        <?= Html::a(Yii::t('app', 'Edit'), Url::to(["registration-messages/update", 'id' => $item['message']->id])) ?>
        <?= Html::a(Yii::t('app', 'Delete'), Url::to(["registration-messages/delete", 'id' => $item['message']->id])) ?>

        <div style="background:white; padding: 8px;">
            <?= Html::decode($item['message']->body); ?>
        </div>
    <?php } else { ?>
        <p>
            <?= Yii::t('app', 'No message created'); ?>
        </p>
        <?= Html::a(
            Yii::t('app', 'Create message'),
            Url::to(["registration-messages/create", 'withInstrument' => $item['wi'], 'withExperience' => $item['we']])
        ) ?>
    <?php } ?>
</div>