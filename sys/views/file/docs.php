<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    $path_info = pathinfo($file['file']);
    if (in_array(strtolower($path_info['extension']), $docs)) {
        $hasFiles = true;
    }
}
if ($hasFiles) {
    foreach ($lecturefiles as $id => $file) {
        $path_info = pathinfo($file['file']);
        if (!in_array(strtolower($path_info['extension']), $docs)) {
            continue;
    }
    ?>
    <a target="_blank" href="<?= $file['file'] ?>" class="file-file col-md-3 col-sm-4 col-xs-6 text-left">
        <?= $file['title'] ?>   
    </a>
    <?php } ?>
<?php } ?>