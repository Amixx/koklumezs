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
        <div class="col-md-12">
            <h3><?= \Yii::t('app',  'Related materials') ?>:</h3>
        </div>
        <?php foreach ($lecturefiles as $id => $file) {
            $path_info = pathinfo($file['file']);
            if (!in_array(strtolower($path_info['extension']), $docs)) {
                continue;
            }
        ?>
            <div class="col-md-3 text-center">
                <a target="_blank" href="<?= $file['file'] ?>"><?= $file['title'] ?></a>
            </div>
        <?php } ?>
    </div>
<?php } ?>