<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->title;
['label' => \Yii::t('app',  'School evaluations'), 'url' => ['index']];

\yii\web\YiiAsset::register($this);
?>
<div class="evaluations-view">

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
            'title:ntext',
            'type',
        ],
    ]) ?>

</div>