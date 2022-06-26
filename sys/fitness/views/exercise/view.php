<?php

use yii\helpers\Html;
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
            'first_set_video',
            'other_sets_video',
            'technique_video',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>