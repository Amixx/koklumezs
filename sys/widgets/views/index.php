<div id="chatModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?= Yii::t('app', 'Chat'); ?></h4>
      </div>
      <div class="modal-body">
        <div class="box box-success">
          <div class="box-header ui-sortable-handle" style="cursor: move;">
            <i class="fa fa-comments-o"></i>
          </div>

          <div class="spinner-container" style="display: none;" id="chat-spinner">
            <div class="lds-roller">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
          </div>
          <?php if ($data) { ?>
            <div id="chat-content-container">
              <div style="position: relative; width: auto; height: 350px; overflow:hidden">
                <?php if ($data['userList']) { ?>
                  <div class="slimScrollDiv" style="overflow:auto; display:inline-block; width: 30%; height: 100%; border-right: 1px solid gainsboro;">
                    <ul id="chat-user-list" style="list-style-type:none; padding:0;">
                      <?= $data['userList'] ?>
                    </ul>
                  </div>
                <?php } ?>
                <div class="slimScrollDiv" id='chat-box-container' style="overflow: auto; display:inline-block; height: 100%; width: <?= $data['userList'] ? '69%' : '100%' ?>;">
                  <div id="chat-box" class="box-body chat" style="width: auto;">
                    <?= $data['content'] ?>
                  </div>
                  <div class="slimScrollBar" style="background: none repeat scroll 0% 0% rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 187.126px;"></div>
                  <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; background: none repeat scroll 0% 0% rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
                </div>
              </div>

              <div class="box-footer">
                <div class="input-group">
                  <input name="Chat[message]" id="chat_message" placeholder="<?= Yii::t('app', 'Type message'); ?>..." class="form-control">
                  <div class="input-group-btn">
                    <button class="btn btn-success btn-send-comment" data-url="<?= $url; ?>" data-model="<?= $userModel; ?>" data-recipient_id="<?= $recipientId ?>"><i class="glyphicon glyphicon-send"></i></button>
                  </div>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Close'); ?></button>
        </div>
      </div>
    </div>
  </div>
</div>