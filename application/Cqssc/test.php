<?php
 $a = file_get_contents(dirname(__FILE__)."/send.json");
 $a = json_decode($a,true);
 var_dump($a['odds']['wuxin']['wuxinzhixuan']['zuhe']['odds']);
