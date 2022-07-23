<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
['label' => \Yii::t('app',  'Templates'), 'url' => ['index']];

\yii\web\YiiAsset::register($this);
?>
<div class="lectures-view">

    <h1><?= Yii::t('app', 'Template: ') ?> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app',  'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(\Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => \Yii::t('app', 'Do you really want to delete this entry?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'description',
        ],
    ]) ?>

    <?php if ($model->templateExerciseSets && !empty($model->templateExerciseSets)) { ?>
        <h3><?= Yii::t('app', 'Added exercises') ?></h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nr.</th>
                    <th><?= Yii::t('app', 'Exercise') ?></th>
                    <th><?= Yii::t('app', 'Weight') ?></th>
                    <th><?= Yii::t('app', 'Repetitions') ?></th>
                    <th><?= Yii::t('app', 'Time (seconds)') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->templateExerciseSets as $key => $tempExSet) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $tempExSet->exerciseSet->exercise['name'] ?></td>
                        <td><?= $tempExSet->weight ?></td>
                        <td><?= $tempExSet->exerciseSet->reps ?></td>
                        <td><?= $tempExSet->exerciseSet->time_seconds ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>