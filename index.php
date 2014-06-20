<?php 
	require_once 'init.inc.php';
	
	if(isset($_GET['page'])){
		$keys=explode(',',file_get_contents('results/keys.txt'));
		$values=explode(',',file_get_contents('results/values.txt'));
		$query=$_GET['search'];
		$highLight=Tool::highLight($query);
	}
?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gbk" />
<title>盲反馈检索系统</title>
<link rel="stylesheet" type="text/css" href="style/index.css"/>
<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript">
<?php 
		if(isset($_GET['query'])&&$_GET['query']!=''){
			$query=$queryWeight='';
			if(isset($_GET['super'])){
				for($i=1;$i<=(count($_GET)-2)/2;$i++){
					$query.=$_GET['query'.$i].',';
					$queryWeight.=$_GET['weight'.$i].',';
				}
			}else{
				$query=$_GET['query'];
			}
		
		
?>
	$(function(){
		var query="<?php echo $query;?>";
		var weight="<?php echo $queryWeight;?>";
		$.ajax({
			type:'POST',
			url:'main.php',
			data:{
				'search':query,
				'weight':weight
			},
			success:function(data){
				$('#output').html(data);
			}
		});

	});

	
<?php }?>
</script>

</head>
<body>
	<div id="input">
			<input type="text" name="query" class="text" value="<?php echo $query;?>" autofocus="autofocus"  placeholder="输入检索词"/>
			<input type="submit" name="submit" class="button" value="搜索"/>
			<p><a href="super.php" target="_blank" class="super">高级检索</a><a href="results.php" target="_blank">查看结果分析</a><a href="graph.php" target="_blank">图表</a></p>
			
	</div>
	<div id="output">
		
			<?php 
			
			if(isset($_GET['page'])){
				echo '<div class="list">';
				$count=0;
				for($i=($_GET['page']-1)*10;;$i++){	
					$str=Tool::fileStr($keys[$i]);
					$str=preg_replace('/ |	|　|	|\s/','',$str);	//去掉中文全角半角空格
					$title=Tool::strLimit($str,30);
					$desc=Tool::strLimit($str,200);
					$title=str_replace($highLight[0],$highLight[1],$title);
					$desc=str_replace($highLight[0],$highLight[1],$desc);
					$wordsArr=Tool::getFileKeys($keys[$i]);
					$wordsStr='';
					foreach ($wordsArr as $v){
						$wordsStr.='<span style="text-decoration:underline;">'.$v.'</span> ';
					}
					echo '<table><tr><td class="title"><a target="_blank" href="news.php?filename='.$keys[$i].'&query='.$query.'">'.$title.'</a></td></tr><tr><td class="desc">'.$desc.'<span style="color:#1AB267;">'.$keys[$i].'</span>';
					echo '<br/><span style="color:#999;font-size:10px;"> 关键词： </span><span style="color:#1AB267;">'.$wordsStr.'</span></td></tr></table>';
		
					$count++;
					if($count==10||$keys[$i+1]==null) break;
				}
				
				echo '</div>';
				echo '<div class="other"><p>检索出相关文档数：<span>'.count($keys).'</span> 篇</p>';
				
				echo '<p style="margin:10px 0 0 0;color:#1AB267;">推荐关键字(点击添加)：</p>';
				echo '<form method="get" action="index.php"><input type="text" name="query" class="text" value=""/><input type="submit" value="确定" class="submit"/></form>';
				
				echo '<ul>';
				//关键词
				$datay=array();
				for($i=1;$i<count($values);$i++){
					$datay[]= -($values[$i]-$values[$i-1]);
				}
				$keywords_num=array_search(max($datay), $datay);
				$keywords_num=$keywords_num<10?10:$keywords_num+1;
				$filename=array_slice($keys,0,$keywords_num+1);
				$keywords=Tool::keyword($filename);
				for($i=0;$i<count($keywords);$i++){
					echo '<li><a href="index.php" onclick="return add(this)">'.$keywords[$i].'</a></li>';
				}
					
				echo '</ul>';
				echo '</div>';
				echo '<div class="page"><ul>';
				for($i=0;$i<ceil(count($keys)/10);$i++){
					echo '<li><a href="?page='.($i+1).'&search='.$query.'">'.($i+1).'</a></li>';
				}
				echo '</ul></div>';
					
				}
			?>
		
	</div>

</body>
</html>








