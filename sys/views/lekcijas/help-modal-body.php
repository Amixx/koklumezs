<div class='form-group'>
    <label for='need-help-message'><?= Yii::t('app', 'Hey! Write to us that did not reach you or remained unclear ... We will be happy to help you understand the task!') ?></label>
    <textarea class='form-control rounded-0' rows="5" name='need-help-message' id='need-help-message'></textarea>
</div>
<p class='alert alert-danger' id='need-help-error'><?= Yii::t('app', 'Enter a message') ?>!</p>
<div style='text-align:right'>
    <button class='btn btn-orange' id='submit-need-help-message' data-lessonid='$uLecture->lecture_id'><?= Yii::t('app', 'Submit message') ?></button>
</div>