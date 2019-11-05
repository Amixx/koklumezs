<?php
$hasFiles = false;
foreach($lecturefiles as $id => $file){ 
    $path_info = pathinfo($file['file']);
    if(in_array(strtolower($path_info['extension']),$audio)){
        $hasFiles = true;
    }
}    
if($hasFiles){ 
?>
<div class="row">
<?php foreach($lecturefiles as $id => $file){ 
$path_info = pathinfo($file['file']);
    if(!in_array(strtolower($path_info['extension']),$audio)){
        continue;
    }
    ?>
    <div class="col-md-12">
        <p><?=$file['title']?></p>   
        <audio controls>
            <source src="<?=$file['file']?>" type="audio/<?=strtolower($path_info['extension'])?>">
            Your browser does not support the audio element.
        </audio>
    
    </div>      
<?php } ?>           
</div>
<hr /> 
<?php } ?>