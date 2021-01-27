<?php
function getPaymentTypeText($paymentType){
    switch($paymentType){
        case 'rent': return 'īrē';

        case 'buy': return 'pērk';

        case 'payments': return 'pērk uz nomaksu';
    }
}

function getDeliveryTypeText($deliveryType){
    switch($deliveryType){
        case 'omniva': return 'Omniva';

        case 'lvpost': return 'Latvijas pasts';

        case 'express': return 'Ekspress kurjers';

        case 'foreign': return 'ārpus latvijas';
    }
}
?>

<div>
    <p>Jaunais skolēns <?= $model['fullname'] ?> (<?= $model['email'] ?>) vēlas iegādāties kokli!</p>
    <p>Pirkuma veids: <strong><?= getPaymentTypeText($model['payment_type']) ?></strong>.</p>
    <p>Piegādes veids: <strong><?= getDeliveryTypeText($model['delivery_type']) ?></strong>.</p>
    <p>Adrese: <strong><?= $model['address'] ?></strong>.</p>
    <p>Telefona nr.: <strong><?= $model['phone_number'] ?></strong>.</p>
    <?php if($model['color'] != null) { ?>
    <p>Kokles krāsa: <strong><?= $model['color'] ?></strong>.</p>
    <?php } ?>
</div>