Jūsu skolā reģistrējies jauns skolēns - <?= $user['first_name'] ?> <?= $user['last_name'] ?>.
E-pasts: <?= $user['email'] ?>.
<?php if($user['phone_number']) { ?>.
    Telefona numurs: <?= $user['phone_number'] ?>.
<?php } ?>
Valoda: <?= $user['language'] ?>.
