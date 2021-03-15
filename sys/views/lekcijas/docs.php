<?php if (!empty($lecturefiles)) { ?>
    <div>
        <?php foreach ($lecturefiles as $id => $file) { ?>
            <div class="col-md-12 text-left">
                <a target="_blank" href="<?= $file['file'] ?>"><?= $file['title'] ?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>