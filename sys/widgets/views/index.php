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
            <div class="slimScrollDiv" style="position: relative; overflow: scroll; width: auto; height: 350px;">
                <div id="chat-box" class="box-body chat" style="overflow: hidden; width: auto; height: 350px;">
                    <?= $data ?>
                </div>
                <div class="slimScrollBar" style="background: none repeat scroll 0% 0% rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 187.126px;"></div>
                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; background: none repeat scroll 0% 0% rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div>
                    
            </div>
            <div class="box-footer">
                <div class="input-group">
                    <input name="Chat[message]" id="chat_message" placeholder="<?= Yii::t('app', 'Type message'); ?>..." class="form-control">
                    <div class="input-group-btn">
                        <button class="btn btn-success btn-send-comment" data-url="<?=$url;?>" data-model="<?=$userModel;?>" data-recipient_id="<?= $recipientId ?>"><i class="glyphicon glyphicon-send"></i></button>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?= Yii::t('app', 'Close'); ?></button>
      </div>
    </div>

  </div>
</div>

