<?php
	require_once 'init.inc.php';
	if(isset($_GET['filename'])&&file_exists(TEXT_PATH.'/'.$_GET['filename'])){
		$str=Tool::fileStr($_GET['filename']);
		$str=preg_replace('/ |	|　|	/','',$str);	//去掉中文全角半角空格
		$highLight=Tool::highLight($_GET['query']);
		$str=str_replace($highLight[0],$highLight[1],$str);
	}else{
		exit('对不起，您要找的新闻不存在！');
	}
	

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>盲反馈检索系统</title>
<link rel="stylesheet" type="text/css" href="style/news.css"/>
<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/news.js"></script>
</head>
<body>
	<div id="input">
			<input type="text" name="query" class="text" value="<?php echo $_GET['query']?>" autofocus="autofocus"  placeholder="输入检索词"/>
			<input type="button" name="button" class="button" value="搜索"/>
	</div>
	<div id="desc">
		<p><span>检索词：<?php echo $_GET['query']?></span> | 文件名：<?php echo $_GET['filename']?></p>
		<p><?php echo $str;?></p>
	</div>
</body>
</html>