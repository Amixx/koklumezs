<?php
$messageText = preg_replace("/\n\s+/", "\n", rtrim(html_entity_decode(strip_tags($message))));

echo $messageText;
