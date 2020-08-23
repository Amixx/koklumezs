<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserLectures */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container">
    <div class="row">
        <div class="col col-md-12">
            <div class="related-lectures-form form-group">
                <h2><?= \Yii::t('app', 'Related lectures') ?></h2>
                <hr />
                <select class="select2" name="relatedLectures[]" multiple>
                    <?php foreach ($lectures as $id => $lecture) { ?>
                        <option value="<?= $id ?>" <?= in_array($id, $relatedLectures) ? ' selected' : '' ?>><?= $lecture ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
</div>