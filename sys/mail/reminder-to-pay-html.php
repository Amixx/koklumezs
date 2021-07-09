<?php
$monthsText = $lateMonthsCount === 1 ? "mēnesi" : "mēnešiem";
?>

<div class="password-reset">
    <p>Sveicieni!</p>
    <p>Izskatās, ka ir aizkavējusies rēķina apmaksa par <?= $lateMonthsCount ?> <?= $monthsText ?> plānam <?= $planName ?>. Lūgums veikt apmaksu nekavējoties.</p>
    <p>Ja ir kāda aizķeršanās vai neskaidrība - lūdzu, padod ziņu. Paldies!</p>
</div>