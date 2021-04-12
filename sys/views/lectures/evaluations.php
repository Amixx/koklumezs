<h2><?= \Yii::t('app',  'Evaluations') ?></h2>
<hr />
<?php
foreach ($evaluations as $id => $evaluation) {
    $inputId = "evaluations-title-" . $evaluation['id'];
    $name = "evaluations[" . $evaluation['id'] . "]";
    $check = isset($lectureEvaluations[$evaluation['id']]) || $evaluation['is_video_param']
        ? 'checked'
        : '';
?>
    <div class="form-group field-evaluations-title custom-control custom-checkbox mr-sm-2">
        <input type="checkbox" class="custom-control-input" id="<?= $inputId ?>" name="<?= $name ?>" <?= $check ?> value="1" aria-required="false" aria-invalid="false" />
        <label class="custom-control-label" for="<?= $inputId ?>">
            <?= \Yii::t('app',  $evaluation['title']) ?>
            <small>[<?= \Yii::t('app',  $evaluation['type']) ?>]</small>
        </label>
        <div class="help-block"></div>
    </div>
<?php } ?>