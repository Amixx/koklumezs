<div class="row">
    <div class="col-md-11">
        <?= $description ?>
    </div>
    <div class="col-md-1">
        <?php if ($lecturefiles) { ?>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" data-display="static" aria-haspopup="true" aria-expanded="false">
                    <?= \Yii::t('app', 'Notes');?>
                </button>
                <div class="dropdown-menu dropdown-menu-lg-left">
                    <?= $files = $this->render('docs', ['lecturefiles' => $lecturefiles, 'docs' => $docs]);?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
</hr>
