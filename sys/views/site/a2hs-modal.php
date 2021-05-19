<?php

$body = <<<EOD
<p>Lai nākamajās reizēs varētu ērtāk lietot e-skolu, instalē to kā lietotni!</p>
<div style='text-align:right'>
    <button id='a2hs-modal-close' class='btn btn-gray'>Varbūt vēlāk</button>
    <button id='a2hs-modal-install' class='btn btn-orange'>Instalēt</button>
</div>
EOD;

echo $this->render("@app/views/shared/modal", [
    'id' => 'a2hs-modal',
    'title' => 'Instalē kā lietotni',
    'body' => $body
]);
