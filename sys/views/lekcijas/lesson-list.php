<?php

use yii\helpers\Url;

?>

<ul class="lesson-list">
    <?php foreach ($userLessons as $userLesson) {  ?>
        <li>
            <?php if ($currentLessonEvaluated) { ?>
                <p>
                    <a class="lesson-column-item" href="<?= Url::to(['lekcijas/lekcija', 'id' => $userLesson->lecture_id]); ?>"><?= $userLesson->lecture->title ?></a>
                </p>
            <?php } else {

                $modalType = "lesson-" . str_replace(' ', '_', $userLesson->lecture->title);
                echo $this->render('alertEvaluation', [
                    'idPostfix' => $modalType,
                    'force' => false,
                    'difficultyEvaluation' => null,
                    'redirectLessonId' => $userLesson->lecture_id,
                ]); ?>

                <a class="lesson-column-item" data-toggle="modal" data-target="#alertEvaluation-<?= $modalType ?>"><?= $userLesson->lecture->title ?></a>
            <?php } ?>
        </li>
    <?php } ?>
</ul>