<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->title = $model->name;
['label' => \Yii::t('app', 'Exercises'), 'url' => ['index']];

\yii\web\YiiAsset::register($this);

function createBooleanColumn($attribute)
{
    return [
        'attribute' => $attribute,
        'value' => function ($dataProvider) use ($attribute) {
            return Yii::t('app', $dataProvider[$attribute] ? 'Yes' : 'No');
        }
    ];
}

?>
<div class="lectures-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(\Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'name',
            'description',
            createBooleanColumn('is_archived'),
            createBooleanColumn('is_ready'),
            createBooleanColumn('is_pause'),
            createBooleanColumn('needs_evaluation'),
            createBooleanColumn('is_bodyweight'),

            createBooleanColumn('has_time'),
            createBooleanColumn('has_resistance_bands'),
            createBooleanColumn('has_mode'),
            createBooleanColumn('has_incline_percent'),
            createBooleanColumn('has_pace'),
            createBooleanColumn('has_speed'),
            createBooleanColumn('has_pulse'),
            createBooleanColumn('has_height'),

            [
                'attribute' => 'popularity_type',
                'value' => function ($dataProvider) {
                    return Yii::t('app',
                        $dataProvider['popularity_type'] === 'POPULAR'
                            ? 'Popular'
                            : ($dataProvider['popularity_type'] === 'AVERAGE' ? 'Average popularity' : 'Rare')
                    );
                }
            ],
            'video',
            'technique_video',
            [
                'attribute' => 'exerciseTags',
                'label' => Yii::t('app', 'Exercise tags'),
                'value' => function ($dataProvider) {
                    if (empty($dataProvider->exerciseTags)) return '';
                    return join(', ', array_map(function ($tag) {
                        return $tag['tag']['value'];
                    }, $dataProvider->exerciseTags));
                }
            ]
        ],
    ]) ?>

    <div style="display:flex; flex-wrap:wrap; justify-content: space-between; align-items:center">
        <h3><?= Yii::t('app', 'Exercise videos') ?></h3>

        <div>
            <a href="<?= Url::to(['fitness-exercise-videos/create', 'exercise_id' => $model->id]) ?>"
               class="btn btn-primary">
                Pievienot video
            </a>
        </div>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th><?= Yii::t('app', 'Repetitions') ?></th>
            <th><?= Yii::t('app', 'Time (seconds)') ?></th>
            <th><?= Yii::t('app', 'Video') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($model->videos as $key => $video) { ?>
            <tr>
                <td><?= $video->reps ?></td>
                <td><?= $video->time_seconds ?></td>
                <td><?= $video->value ?></td>
                <td>
                    <form action="<?= Url::to(['fitness-exercise-videos/update', 'id' => $video->id]) ?>"
                          method="POST"
                          style="display:inline-block">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
                               value="<?= Yii::$app->request->csrfToken; ?>"/>
                        <button class="btn btn-primary">Rediģēt</button>
                    </form>
                    <form action="<?= Url::to(['fitness-exercise-videos/delete', 'id' => $video->id, 'exercise_id' => $model->id]) ?>"
                          method="POST" style="display:inline-block">
                        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
                               value="<?= Yii::$app->request->csrfToken; ?>"/>
                        <button class="btn btn-danger">Dzēst</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <div>
        <h3><?= Yii::t('app', 'Interchangeable exercises') ?></h3>
        <div>
            <?php foreach ($model->getInterchangeableOtherExercises() as $exercise) { ?>
                <span><?= $exercise['name'] ?></span>,
            <?php } ?>
        </div>
    </div>
</div>