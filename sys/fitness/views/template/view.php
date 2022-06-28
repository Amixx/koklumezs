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
            'created_at',
            'updated_at',
        ],
    ]) ?>

    <?php if ($model->templateExercises && !empty($model->templateExercises)) { ?>
        <h3><?= Yii::t('app', 'Added exercises') ?></h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nr.</th>
                    <th><?= Yii::t('app', 'Exercise') ?></th>
                    <th><?= Yii::t('app', 'Weight') ?></th>
                    <th><?= Yii::t('app', 'Repetitions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->templateExercises as $key => $tempEx) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $tempEx->exercise['name'] ?></td>
                        <td><?= $tempEx->weight ?></td>
                        <td><?= $tempEx->reps ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

</div>