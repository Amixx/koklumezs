<div class="form-group related-lectures-form">
    <label><?= \Yii::t('app', 'Related lessons (not required)') ?></label>
    <select class="select2" style="width: 100%;" name="relatedLectures[]" multiple>
        <?php foreach ($lectures as $id => $lecture) { ?>
            <option value="<?= $id ?>" <?= in_array($id, $relatedLectures) ? ' selected' : '' ?>><?= $lecture ?></option>
        <?php } ?>
    </select>
</div>