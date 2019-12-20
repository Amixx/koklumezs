<h2>Novērtējumi</h2>
<hr />      
<?php      
foreach($evaluations as $id => $evaluation){?>
    <div class="form-group field-evaluations-title custom-control custom-checkbox mr-sm-2">
        <input type="checkbox" class="custom-control-input" id="evaluations-title-<?=$evaluation['id']?>" name="evaluations[<?=$evaluation['id']?>]" <?=isset($lectureEvaluations[$evaluation['id']]) ? 'checked' : ($evaluation['is_video_param'] ? '' : 'checked')?> value="1" aria-required="false" aria-invalid="false" />
        <label class="custom-control-label" for="evaluations-title-<?=$evaluation['id']?>"><?=$evaluation['title']?> <small>[<?=$evaluation['type']?>]</small></label>
        <div class="help-block"></div> 
    </div>         
<?php } ?>   