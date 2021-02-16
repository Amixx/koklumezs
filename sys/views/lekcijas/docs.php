<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    $path_info = pathinfo($file['file']);
    if (in_array(strtolower($path_info['extension']), $docs)) {
        $hasFiles = true;
    }
}
if ($hasFiles) { ?>
    <div>
        <?php
        foreach ($lecturefiles as $id => $file) {
            $path_info = pathinfo($file['file']);
            if (!in_array(strtolower($path_info['extension']), $docs)) {
                continue;
            }
        ?>
            <div class="col-md-12 text-left">
                <a target="_blank" href="<?= $file['file'] ?>"><?= $file['title'] ?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>