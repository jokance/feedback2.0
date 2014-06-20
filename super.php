<?php

?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>盲反馈检索系统</title>
<link rel="stylesheet" type="text/css" href="style/super.css"/>

</head>
<body>
	<div id="main">
		<h1>高级检索</h1>
		<form method="get" action="index.php">
		<input type="hidden" name="query" value="fill"/>
		<input type="hidden" name="super"/>
			<dl>
				<dd>检索词1：<input type="text" name="query1" class="text" value=""/>  权值：<input type="number" name="weight1" min="1" max="10" /></dd>
				<dd>检索词2：<input type="text" name="query2" class="text" value=""/>  权值：<input type="number" name="weight2" min="1" max="10" /></dd>
				<dd>检索词3：<input type="text" name="query3" class="text" value=""/>  权值：<input type="number" name="weight3" min="1" max="10" /></dd>
				<dd><input type="submit" value="提交" class="submit"/></dd>
			</dl>
			
		
		</form>
	</div>
</body>
</html>