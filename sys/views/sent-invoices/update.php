<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = \Yii::t('app', 'Invoice No. ') . $model->invoice_number;
\Yii::t('app', 'Edit');
?>
<div>
    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'realInvoice' => $realInvoice,
    ]) ?>

    <p><?= Yii::t('app', 'Advance invoice sent date: '); ?><strong><?= $model->sent_date ?></strong>.</p>
    <?php if ($realInvoice != null) { ?>
        <p><?= Yii::t('app', 'Invoice paid date: '); ?><strong><?= $realInvoice->sent_date ?></strong>.</p>
    <?php } else { ?>
        <p><?= Yii::t('app', 'The invoice has not been paid yet'); ?>.</p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'invoice_number',
            'student.first_name',
            'student.last_name',
            'plan_name',
            'plan_price',
            'plan_start_date',
        ],
    ]) ?>
</div>