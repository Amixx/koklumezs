<div class="password-reset">
    <p>Jūsu skolā reģistrējies jauns skolēns - <?= $user['first_name'] ?> <?= $user['last_name'] ?>.</p>
    <p>E-pasts: <?= $user['email'] ?>.</p>
    <?php if($user['phone_number']) { ?>
        <p>Telefona numurs: <?= $user['phone_number'] ?>.</p>
    <?php } ?>
    <p>Valoda: <?= $user['language'] ?>.</p>
</div>