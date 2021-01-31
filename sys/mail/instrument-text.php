Jaunais skolēns <?= $model['fullname'] ?> (<?= $model['email'] ?>) vēlas īrēt instrumentu!
Adrese: <?= $model['address'] ?>.
Telefona nr.: <?= $model['phone_number'] ?>.
<?php if($model['color'] != null) { ?>
    Kokles krāsa: <?= $model['color'] ?>.
<?php } ?>