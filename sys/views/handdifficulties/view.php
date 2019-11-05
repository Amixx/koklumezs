<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Handdifficulties */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Kategorijas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="handdifficulties-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Rediģēt', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Dzēst', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Vai Jūs tiešām vēlaties dzēst šo ierakstu?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'hand',
            'category:ntext',
        ],
    ]) ?>

</div>