<?php
$sum = 0;
foreach ($difficulties as $id => $name) {
    $continue = !isset($lectureDifficulties[$id]);
    if ($continue) {
        continue;
    }
    $sum += $lectureDifficulties[$id];
} ?>
<hr />
<div class="row">
    <div class="col-md-12">
        <h3>Nodarbības sarežģītība (lesson difficulty): <?= $sum ?></h3>
    </div>
    <?php
    foreach ($difficulties as $id => $name) {
        $continue = !isset($lectureDifficulties[$id]);
        if ($continue) {
            continue;
        }
    ?>
        <div class="col-md-3 text-center">
            <?= $name ?> (<?= $difficultiesEng[$id - 1] ?>): <?= $lectureDifficulties[$id] ?>
        </div>
    <?php } ?>
</div>