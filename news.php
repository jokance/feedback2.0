<?php
	require_once 'init.inc.php';
	if(isset($_GET['filename'])&&file_exists(TEXT_PATH.'/'.$_GET['filename'])){
		$str=Tool::fileStr($_GET['filename']);
		$str=preg_replace('/ |	|��|	/','',$str);	//ȥ������ȫ�ǰ�ǿո�
		$highLight=Tool::highLight($_GET['query']);
		$str=str_replace($highLight[0],$highLight[1],$str);
	}else{
		exit('�Բ�����Ҫ�ҵ����Ų����ڣ�');
	}
	

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>ä��������ϵͳ</title>
<link rel="stylesheet" type="text/css" href="style/news.css"/>
<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/news.js"></script>
</head>
<body>
	<div id="input">
			<input type="text" name="query" class="text" value="<?php echo $_GET['query']?>" autofocus="autofocus"  placeholder="���������"/>
			<input type="button" name="button" class="button" value="����"/>
	</div>
	<div id="desc">
		<p><span>�����ʣ�<?php echo $_GET['query']?></span> | �ļ�����<?php echo $_GET['filename']?></p>
		<p><?php echo $str;?></p>
	</div>
</body>
</html>