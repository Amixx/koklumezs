<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Lecturesfiles */

$this->title = $model->id;
['label' => \Yii::t('app',  'Files'), 'url' => ['index']];

\yii\web\YiiAsset::register($this);
?>
<div class="lecturesfiles-view">

    <h1><?= Html::encode($model->title) ?></h1>


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
            'title',
            'file:ntext',
            'thumb:ntext',
            'lecture_id',
        ],
    ]) ?>

</div>