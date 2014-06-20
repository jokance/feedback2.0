
<?php
	require_once 'init.inc.php';
//	echo Tool::fileStr('10.txt');
//	$file_dir=Tool::fileDir(TEXT_PATH);	//取出文档集文件名
//	print_r($file_dir);
//	$top=Tool::segment($file_dir);	//分词,第一步
//	print_r($top);
//	print_r(Tool::dic());	//建立词表,第二步
	Tool::tf_df();	//计算tf/idf,第三步
//	print_r($seg);
//	Tool::vsm($seg);	//计算特征向量
//	print_r($vsm);

		/*
//	if(isset($_POST)){
//		$query=iconv('UTF-8','GBK',$_POST['search']);
		$query='戴尔';
		$q=Tool::query_vsm($query);
		$q_keys=array_keys($q);
		$dic=file_get_contents('dic.txt');
		$dic_arr=explode(',',$dic);
		for($i=0;$i<count($q_keys);$i++){
			$q_arr[]=$dic_arr[$q_keys[$i]];
			$q_replace[]='<font color="red">'.$dic_arr[$q_keys[$i]].'</font>';
		}
	
		$sim=Tool::sim($query);



		//$not_sim_count=count(array_keys($sim,0));	//相似度为0的文档数
		//$sim_count=count($sim);	//相关文档数
		//$sim=array_slice($sim,0,$sim_count);

		$keys=array_keys($sim);
		//$values=array_values($sim);
	
		//存入数据库

		exit();
		for($i=0;$i<count($keys);$i++){		
			$str=Tool::fileStr($keys[$i]);
			$str=preg_replace('/ |	|　|	/','',$str);	//去掉中文全角半角空格
			$title=Tool::strLimit($str,30);
			$desc=Tool::strLimit($str,200);
			$title=str_replace($q_arr,$q_replace,$title);
			$desc=str_replace($q_arr,$q_replace,$desc);
//			$title=Tool::highLight($query,$title);
//			$desc=Tool::highLight($query,$desc);
			echo '<table><tr><td class="title"><a target="_blank" href="news.php?filename='.$keys[$i].'&query='.$query.'">'.$title.'</a></td></tr><tr><td class="desc">'.$desc.'</td></tr></table>';
		}

	//}
*/

//	$sim=Tool::sim('联想美国公司');
//	print_r($sim);
//	$a=array(1,2,3);
//	echo Tool::mod($a);
	
//	$dic_str=file_get_contents('df.txt');
//	$dic_arr=explode(',',$dic_str);
//	echo $dic_arr['斯达康'];
//	print_r($dic_arr);	//词典中有1337个词,11878
//	$arr=Tool::query_vsm('联想公司促销活动');
//	print_r($arr);
//	for($i=0;$i<TEXT_COUNT;$i++){
//		$str=file_get_contents(TEXT_PATH.'/'.($i+10).'.txt');
//		$str=str_replace('联想','<font color="red">联想</font>',$str);
//		$str=str_replace('公司','<font color="red">公司</font>',$str);
//		$str=str_replace('促销','<font color="red">促销</font>',$str);
//		echo $str.'<br/><br/>';
//	}
//	$db=DB::getDB();
//	$result=$db->query("SELECT * FROM news");
//	while(!!$row=$result->fetch_assoc()){
//		print_r($row);
//	}
//	echo get_magic_quotes_gpc();
//	$a=array();
//	$a[10]=0;
//	print_r($a);
	
//	$keys=array_slice($keys,0,5);
//	print_r(Tool::keyword($keys));

//	$a=array('3G','迷糊22','213','34.123',0);
//	$a=array_filter($a,'filter_number');
//	print_r($a);
//	$s=(!is_int($a[4]));
//	echo $s;
?>
