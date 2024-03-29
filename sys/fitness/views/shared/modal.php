<div class="modal fade" id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog <?= isset($large) && $large ? 'modal-lg' : '' ?>" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title" id="modal-title"><?= $title ?></h3>
            </div>
            <div class="modal-body">
                <?php if (isset($body)) {
                    echo $body;
                } else if (isset($bodyFileName)) {
                    echo $this->render($bodyFileName, isset($bodyFileParams) ? $bodyFileParams : []);
                } ?>
            </div>
        </div>
    </div>
</div>