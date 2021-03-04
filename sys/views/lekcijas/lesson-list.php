<?php
use yii\helpers\Url;

?>

<ul class="lesson-list">
    <?php foreach ($lessons as $lesson) {  ?>
    <li>
        <?php if($currentLessonEvaluated) { ?>
        <p>
            <a
                class="lesson-column-item"
                href="<?= Url::to(['lekcijas/lekcija','id' => $lesson->id]); ?>"
            ><?= $lesson->title ?></a>
        </p>            
        <?php } else {
            
            $modalType = "lesson-" . str_replace( ' ', '_', $lesson->title);
            echo $this->render('alertEvaluation', [
                'idPostfix' => $modalType,
                'force' => false,
                'difficultyEvaluation' => null,
                'redirectLessonId' => $lesson->id,
            ]); ?>

            <a class="lesson-column-item" data-toggle="modal" data-target="#alertEvaluation-<?= $modalType ?>"><?= $lesson->title ?></a>
        <?php } ?>    
    </li>
    <?php } ?>
</ul>
