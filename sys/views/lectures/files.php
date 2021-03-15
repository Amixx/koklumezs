<h2><?= \Yii::t('app',  'Added files') ?></h2>
<hr />
<p>
    <a target="_blank" class="btn btn-success" href="<?= $link ?>"><?= \Yii::t('app',  'Add file') ?></a>
</p>
<?php if ($lecturefiles) {  ?>
    <table class="table table-striped table-bordered">
        <?php foreach ($lecturefiles as $type => $fileGroup) {
            if(empty($fileGroup)) continue;

            switch($type){
                case 'video': $typeText = 'Video faili';
                    break;
                case 'docs': $typeText = 'Dokumenti';
                    break;
                case 'audio': $typeText = 'Audio faili';
                    break;
                default: break;
            }
        ?>
            <tr>
                <th colspan="2" class="text-center"><?= $typeText ?></th>
            </tr>
            <?php foreach($fileGroup as $id => $file){
                $view = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/create', 'id' => $file['id']]);
                $up = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/update', 'id' => $file['id']]);
                $del = Yii::$app->urlManager->createAbsoluteUrl(['lecturesfiles/delete', 'id' => $file['id']]);
            ?>
            <tr>
                <td><?= $file['title'] ?></td>
                <td>
                    <a target="_blank" href="<?= $view ?>" title=<?= \Yii::t('app',  'View') ?> aria-label=<?= \Yii::t('app',  'View') ?> data-pjax="0"><span class="glyphicon glyphicon-eye-open"></span></a>
                    <a target="_blank" href="<?= $up ?>" title=<?= \Yii::t('app',  'Edit') ?> aria-label=<?= \Yii::t('app',  'Edit') ?> data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>
                    <a href="<?= $del ?>" title=<?= \Yii::t('app',  'Delete') ?> aria-label=<?= \Yii::t('app',  'Delete') ?> data-pjax="0" data-confirm=<?= \Yii::t('app',  'Do you really want to delete this file?') ?> data-method="post"><span class="glyphicon glyphicon-trash"></span></a>
                </td>
            </tr>
        <?php } } ?>
    </table>
<?php } ?>