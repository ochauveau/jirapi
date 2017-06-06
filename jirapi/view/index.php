<html>
<head>
<meta charset="UTF-8">
<link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="css/style.css?<?=time()?>">
</head>
<body>
<div>
<?php
foreach($tab_var as $var){
?>
	<div class="<?=$var['result']?>">
	<span class="font"><?=$var['team']?></span>
	<div class="mini"><?=$var['label_item_effotfull']?> <?=$var['label_item_effotless']?></div>
	<div class="content2"><?=$var['velocity_and_engagement']?></div>
	<div class="content2"><?=$var['bv_done_and_total']?></div>
	<div class="content2"><?=$var['bug_and_debt']?></div>
	</div>
<?php
}
?>
</div>
</body>
</html>