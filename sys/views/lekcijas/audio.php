<?php
$hasFiles = false;
foreach ($lecturefiles as $id => $file) {
    if (isset($file['file']) and !empty($file['file']) && strpos($file['file'], "youtube") === false) {
        $path_info = pathinfo($file['file']);
        if (in_array(strtolower($path_info['extension']), $audio)) {
            $hasFiles = true;
        }
    }
    
}
if ($hasFiles) {
?>
    <div class="row">
        <?php foreach ($lecturefiles as $id => $file) {
            if(strpos($file['file'], "youtube") !== false) continue;

            $path_info = pathinfo($file['file']);
            if (!in_array(strtolower($path_info['extension']), $audio)) {
                continue;
            }
        ?>
            <div class="col-md-12">
                <p><?= $file['title'] ?></p>
                <audio controls>
                    <source src="<?= $file['file'] ?>" type="audio/<?= strtolower($path_info['extension']) ?>">
                    <?= \Yii::t('app',  'Your browser does not support the audio element') ?>.
                </audio>

            </div>
        <?php } ?>
    </div>
<?php } ?>