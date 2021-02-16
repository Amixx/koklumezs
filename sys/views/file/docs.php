<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    $path_info = pathinfo($file['file']);
    if (in_array(strtolower($path_info['extension']), $docs)) {
        $hasFiles = true;
    }
}
if ($hasFiles) {
?>
    <hr />
    <div class="row">
        <?php foreach ($lecturefiles as $id => $file) {
            $path_info = pathinfo($file['file']);
            if (!in_array(strtolower($path_info['extension']), $docs)) {
                continue;
            }
        ?>
            <div class="col-md-3 text-left">
                <a target="_blank" href="<?= $file['file'] ?>"><?= $file['title'] ?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>