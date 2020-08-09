<?php

use \yii2mod\rating\StarRating;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<hr />
<div class="row">
    <div class="col-md-12">
        <h3>Novērtē nodarbību/Evaluate the lesson</h3>
    </div>
</div>
<?php $form = ActiveForm::begin(); ?>
<?php
foreach ($evaluations as $id => $evaluation) {
    $continue = !isset($lectureEvaluations[$evaluation['id']]);
    if ($continue) {
        continue;
    }
    if ($evaluation['type'] == 'teksts') { ?>
        <div class="form-group field-election-election_description">
            <label class="control-label" for="election-<?= $evaluation['id'] ?>"><?= $evaluation['title'] ?>/<?= $evaluation['titleEng'] ?></label>
            <textarea id="evaluations-title-<?= $evaluation['id'] ?>" class="form-control" rows="6" name="evaluations[<?= $evaluation['id'] ?>]"><?= isset($userLectureEvaluations[$evaluation['id']]) ? $userLectureEvaluations[$evaluation['id']] : '' ?></textarea>
            <div class="help-block"></div>
        </div>
    <?php } else { ?>
        <div class="form-group field-election-election_description">
            <label class="control-label" for="election-<?= $evaluation['id'] ?>"><?= $evaluation['title'] ?>/<?= $evaluation['titleEng'] ?></label>
            <?= StarRating::widget([
                'name' => 'evaluations[' . $evaluation['id'] . ']',
                'value' => isset($userLectureEvaluations[$evaluation['id']]) ? $userLectureEvaluations[$evaluation['id']] : 0,
                'clientOptions' => [
                    // Your client options
                    'id' => 'election-' . $evaluation['id'],
                    'required' => 'required',
                    'scoreName' => 'evaluations[' . $evaluation['id'] . ']',
                    'number' => $evaluation['stars'],
                    'hints'    =>   !empty(unserialize($evaluation['star_text'])) ? unserialize($evaluation['star_text']) : [], // Hints used on each star.
                ],
            ]); ?>
            <div class="help-block hint"></div>
        </div>

    <?php } ?>
<?php } ?>
<div class="form-group">
    <?= Html::checkbox('add-to-favourites', false, ['label' => 'Pievienot mīļākajām nodarbībām/Add to favourites']) ?>
    <?= Html::checkbox('add-to-still-learning', false, ['label' => 'Pievienot nodarbībām, ko vēl mācos/Add to lessons I\'m still learning']) ?>
</div>
<?php if (!$force) { ?>
    <div class="form-group">
        <?= Html::submitButton('Iesniegt/Submit', ['class' => 'btn btn-success']) ?>
    </div>
<?php } ?>
</div>
<?php ActiveForm::end(); ?>