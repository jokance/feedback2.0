
<?php
	require_once 'init.inc.php';

	if(isset($_POST)){
		$query=iconv('UTF-8','GBK',$_POST['search']);
		
		$highLight=Tool::highLight($query,$_POST['weight']);
		$sim=Tool::sim($query);	//相似度


		$sim_count=count($sim);	//相关文档数
		$keys=array_keys($sim);
		$values=array_values($sim);
		
		$keys_str=implode(',',$keys);
		$values_str=implode(',',$values);
		
		$fp=fopen('results/keys.txt','w');
		if(!$fp) exit('keys.txt打开失败');
		if(!fwrite($fp,$keys_str)) exit('keys.txt文件写入失败！');
		fclose($fp);
		$fp=fopen('results/values.txt','w');
		if(!$fp) exit('values.txt打开失败');
		if(!fwrite($fp,$values_str)) exit('values.txt文件写入失败！');
		fclose($fp);
		
		//关键词
		$datay=array();
		if(count($values)!=1){
			for($i=1;$i<count($values);$i++){
				$datay[]= -($values[$i]-$values[$i-1]);
			}
			$keywords_num=array_search(max($datay), $datay);
			$keywords_num=$keywords_num<10?10:$keywords_num+1;
		}else{
			$keywords_num=0;
		}
		if($sim_count<10){
			$count=$sim_count;
		}else{
			$count=10;
		}
		echo '<div class="list">';
		for($i=0;$i<$count;$i++){		
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
		}
		echo '</div>';
		echo '<div class="other"><p>检索出相关文档数：<span>'.$sim_count.'</span> 篇</p>';

		echo '<p style="margin:10px 0 0 0;color:#1AB267;">推荐关键字(点击添加)：</p>';
		echo '<form method="get" action="index.php"><input type="text" name="query" class="text" value=""/><input type="submit" value="确定" class="submit"/></form>';
		
		echo '<ul>';
		//关键词
		$keys=array_slice($keys,0,$keywords_num+1);
		$keywords=Tool::keyword($keys);
		for($i=0;$i<count($keywords);$i++){
			echo '<li><a href="index.php" onclick="return add(this)">'.$keywords[$i].'</a></li>';
		}
		 
		echo '</ul>';
		echo '</div>';
		echo '<div class="page"><ul>';
		for($i=0;$i<ceil($sim_count/10);$i++){
			echo '<li><a href="?page='.($i+1).'&search='.$query.'">'.($i+1).'</a></li>';
		}
		echo '</ul></div>';



	}





?>
