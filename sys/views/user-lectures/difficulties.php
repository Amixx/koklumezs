<button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#paramsCollapse" aria-expanded="<?= empty($selected) ? 'false' : 'true' ?>" aria-controls="paramsCollapse">
    <?= \Yii::t('app',  'Parameters') ?>
</button>
<div class="<?= empty($selected) ? 'collapse' : '' ?>" id="paramsCollapse">
    <div class="card card-body">
        <h2><?= \Yii::t('app',  'Parameters') ?></h2>
        <hr />
        <?php
        foreach ($difficulties as $id => $name) {  ?>
            <div class="form-group field-studentgoals">
                <label class="control-label" for="difficulties-title<?= $id ?>"><?= $name ?></label>
                <select id="difficulties-title<?= $id ?>" class="form-control" name="difficulties[<?= $id ?>]" aria-required="true" aria-invalid="false">
                    <option value=""></option>
                    <?php for ($a = 1; $a <= 10; $a++) {
                        $selected = (isset($selected[$id]) && ($selected[$id] == $a)) ? 'selected' : '';
                    ?>
                        <option value="<?= $a ?>" <?= $selected ?>><?= $a ?></option>
                    <?php } ?>
                </select>
                <div class="help-block"></div>
            </div>
        <?php } ?>
    </div>
</div>