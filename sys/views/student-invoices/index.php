<?php

use yii\helpers\Html;

$this->title = \Yii::t('app', 'Unpaid invoices');
?>
<div class="difficulties-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col"><?= \Yii::t('app', 'Invoice number') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Plan name') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Invoice date') ?></th>
            <th scope="col"><?= \Yii::t('app', 'Plan price (monthly)') ?></th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <?php if (!is_null($invoices)) {
            $a = 1;
            foreach ($invoices as $invoice) { ?>
                <tr>
                    <td><?= $a ?></td>
                    <td><?= $invoice['invoice_number'] ?></td>
                    <td><?= $invoice['plan_name'] ?></td>
                    <td class="text-center"
                        style="white-space:nowrap"><?= date_format(new \DateTime($invoice['sent_date']), "Y-m-d") ?></td>
                    <td><?= $invoice['plan_price'] ?></td>
                    <td>
                        <button class="btn btn-primary payment-link"
                                data-invoice-id="<?= $invoice['id'] ?>"><?= Yii::t('app', 'Pay') ?></button>
                    </td>
                </tr>
                <?php $a++;
            }
        } ?>
        </tbody>
    </table>
    <?= $this->render("@app/views/shared/modal", [
        'id' => 'checkout-modal',
        'title' => 'Maksājumi',
        'bodyFileName' => "/student-invoices/invoice-payment-modal-body",
    ]); ?>
</div>