<div class="password-reset">
    <p>Jaunais skolēns <?= $model['fullname'] ?> (<?= $model['email'] ?>) vēlas iegādāties kokli!</p>

    <p>Pirkuma veids: <strong><?= $model['payment_type'] == 'buy' ? 'pērk' : 'uz nomaksu' ?></strong>.</p>
    <p>Jāpiegādā uz: <strong><?= $model['delivery_type'] == 'local' ? 'Latviju' : 'ārzemēm' ?></strong>.</p>
    <p>Adrese: <strong><?= $model['address'] ?></strong>.</p>
    <p>Telefona nr.: <strong><?= $model['phone_number'] ?></strong>.</p>
    <?php if($model['color'] != null) { ?>
    <p>Kokles krāsa: <strong><?= $model['color'] ?></strong>.</p>
    <?php } ?>
</div>