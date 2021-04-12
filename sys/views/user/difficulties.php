<div class="row">
    <div class="col-md-6">
        <h2><?= \Yii::t('app',  'Parameters at the moment') ?></h2>
        <?php
        foreach ($difficulties as $id => $name) {  ?>
            <div class="form-group field-studentgoals">
                <label class="control-label" for="studentgoals-title-now<?= $id ?>"><?= \Yii::t('app',  $name) ?></label>
                <select id="studentgoals-title-now<?= $id ?>" class="form-control" name="studentgoals[now][<?= $id ?>]" aria-required="true" aria-invalid="false">
                    <option value=""></option>
                    <?php for ($a = 1; $a <= 10; $a++) {
                        $selected = (isset($studentGoals['Šobrīd'][$id]) && ($studentGoals['Šobrīd'][$id] == $a)) ? 'selected' : '';
                    ?>
                        <option value="<?= $a ?>" <?= $selected ?>><?= $a ?></option>
                    <?php } ?>
                </select>
                <div class="help-block"></div>
            </div>
        <?php } ?>
    </div>
    <div class="col-md-6">
        <h2><?= \Yii::t('app',  'Needed parameters') ?></h2>
        <?php
        foreach ($difficulties as $id => $name) { ?>
            <div class="form-group field-studentgoals">
                <label class="control-label" for="studentgoals-title-future<?= $id ?>"><?= \Yii::t('app',  $name) ?></label>
                <select id="studentgoals-title-future<?= $id ?>" class="form-control" name="studentgoals[future][<?= $id ?>]" aria-required="true" aria-invalid="false">
                    <option value=""></option>
                    <?php for ($a = 1; $a <= 10; $a++) {
                        $selected = (isset($studentGoals['Vēlamais'][$id]) && ($studentGoals['Vēlamais'][$id] == $a)) ? 'selected' : '';
                    ?>
                        <option value="<?= $a ?>" <?= $selected  ?>><?= $a ?></option>
                    <?php } ?>
                </select>
                <div class="help-block"></div>
            </div>
        <?php } ?>
    </div>
</div>