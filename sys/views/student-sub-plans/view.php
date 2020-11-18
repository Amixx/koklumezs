<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\Difficulties */

$this->title = $subplan['plan']['name'];
\yii\web\YiiAsset::register($this);
?>
<div class="difficulties-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $subplan,
        'attributes' => [
            'start_date',
            'sent_invoices_count',
            'plan.name',
        ],
    ]) ?>

    <h3><?= Yii::t('app', 'Plan files') ?></h3>
    <?php 
    foreach($planFiles as $file){ ?>
    <p>
        <a href="<?= $file['file'] ?>" target="_blank"><?= $file['title'] ?></a>
    </p>
        
    <?php }
    ?>
</div>