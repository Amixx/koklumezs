Jaunais skolēns <?= $model['fullname'] ?> (<?= $model['email'] ?>) vēlas iegādāties kokli!

Pirkuma veids: <?= $model['payment_type'] == 'buy' ? 'pērk' : 'uz nomaksu' ?>
Jāpiegādā uz: <?= $model['delivery_type'] == 'local' ? 'Latviju' : 'ārzemēm' ?>
Adrese: <?= $model['address'] ?>
Telefona nr.: <?= $model['phone_number'] ?>
<?php if(isset($model['color']) && $model['color'] != null) { ?>
Kokles krāsa: <?= $model['color'] ?>
<?php } ?>