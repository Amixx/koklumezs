<?php if(!empty($lectureAudioFiles)){ ?>
    <div class="row">
        <?php foreach ($lectureAudioFiles as $id => $file) {
            $path_info = pathinfo($file['file']);
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
