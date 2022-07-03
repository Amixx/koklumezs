<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = $model->name;
['label' => \Yii::t('app',  'Exercises'), 'url' => ['index']];

\yii\web\YiiAsset::register($this);
?>
<div class="lectures-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'name',
            'description',
            'technique_video',
        ],
    ]) ?>

    <?php if ($model->sets && !empty($model->sets)) { ?>
        <h3><?= Yii::t('app', 'Exercise sets') ?></h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?= Yii::t('app', 'Sequence no.') ?></th>
                    <th><?= Yii::t('app', 'Repetitions') ?></th>
                    <th><?= Yii::t('app', 'Video') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($model->sets as $key => $set) { ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= $set->reps ?></td>
                        <td><?= $set->video ?></td>
                        <td>
                            <form action="<?= Url::to(['fitness-exercise-sets/delete', 'id' => $set->id, 'exercise_id' => $model->id]) ?>" method="POST">
                                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
                                <button class="btn btn-danger">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>

    <a href="<?= Url::to(['fitness-exercise-sets/create', 'exercise_id' => $model->id]) ?>" class="btn btn-primary">
        Pievienot <?= $model->sets ? count($model->sets) + 1 : 1 ?>. piegājienu
    </a>
</div>