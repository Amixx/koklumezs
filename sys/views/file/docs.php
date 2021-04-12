<?php
foreach ($lecturefiles as $file) { ?>
    <a target="_blank" href="<?= $file['file'] ?>" class="file-file col-md-3 col-sm-4 col-xs-6 text-left">
        <?= $file['title'] ?>
    </a>
<?php } ?>