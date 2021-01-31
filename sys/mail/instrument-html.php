<div>
    <p>Jaunais skolēns <?= $model['fullname'] ?> (<?= $model['email'] ?>) vēlas īrēt instrumentu!</p>
    <p>Adrese: <strong><?= $model['address'] ?></strong>.</p>
    <p>Telefona nr.: <strong><?= $model['phone_number'] ?></strong>.</p>
    <?php if($model['color'] != null) { ?>
        <p>Kokles krāsa: <strong><?= $model['color'] ?></strong>.</p>
    <?php } ?>
</div>