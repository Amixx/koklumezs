<?php if (!empty($lastLectures)) { ?>
    <h2><?= \Yii::t('app',  'Lesson history') ?></h2>
    <hr />
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col"><?= \Yii::t('app',  'Date') ?></th>
                <?php foreach ($lastLectures as $lecture) { ?>
                    <th scope="col">
                        <?= isset($userLecturesTimes[$lecture->id])
                            ? $userLecturesTimes[$lecture->id]
                            : ''
                        ?>
                    </th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= \Yii::t('app',  'Lesson') ?></td>
                <?php foreach ($lastLectures as $lecture) { ?>
                    <td><?= $lecture->title ?></td>
                <?php } ?>
            </tr>
            <?php foreach ($difficulties as $diffId => $diff) { ?>
                <tr>
                    <td><?= $diff ?></td>
                    <?php foreach ($lastLectures as $lecture) { ?>
                        <td>
                            <?=
                            isset($lectureDifficulties[$lecture->id][$diffId])
                                ? $lectureDifficulties[$lecture->id][$diffId]
                                : '-'
                            ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>

        </tbody>
    </table>
<?php } ?>