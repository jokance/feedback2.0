
<?php
	require_once 'init.inc.php';
//	echo Tool::fileStr('10.txt');
//	$file_dir=Tool::fileDir(TEXT_PATH);	//ȡ���ĵ����ļ���
//	print_r($file_dir);
//	$top=Tool::segment($file_dir);	//�ִ�,��һ��
//	print_r($top);
//	print_r(Tool::dic());	//�����ʱ�,�ڶ���
	Tool::tf_df();	//����tf/idf,������
//	print_r($seg);
//	Tool::vsm($seg);	//������������
//	print_r($vsm);

		/*
//	if(isset($_POST)){
//		$query=iconv('UTF-8','GBK',$_POST['search']);
		$query='����';
		$q=Tool::query_vsm($query);
		$q_keys=array_keys($q);
		$dic=file_get_contents('dic.txt');
		$dic_arr=explode(',',$dic);
		for($i=0;$i<count($q_keys);$i++){
			$q_arr[]=$dic_arr[$q_keys[$i]];
			$q_replace[]='<font color="red">'.$dic_arr[$q_keys[$i]].'</font>';
		}
	
		$sim=Tool::sim($query);



		//$not_sim_count=count(array_keys($sim,0));	//���ƶ�Ϊ0���ĵ���
		//$sim_count=count($sim);	//����ĵ���
		//$sim=array_slice($sim,0,$sim_count);

		$keys=array_keys($sim);
		//$values=array_values($sim);
	
		//�������ݿ�

		exit();
		for($i=0;$i<count($keys);$i++){		
			$str=Tool::fileStr($keys[$i]);
			$str=preg_replace('/ |	|��|	/','',$str);	//ȥ������ȫ�ǰ�ǿո�
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

//	$sim=Tool::sim('����������˾');
//	print_r($sim);
//	$a=array(1,2,3);
//	echo Tool::mod($a);
	
//	$dic_str=file_get_contents('df.txt');
//	$dic_arr=explode(',',$dic_str);
//	echo $dic_arr['˹�￵'];
//	print_r($dic_arr);	//�ʵ�����1337����,11878
//	$arr=Tool::query_vsm('���빫˾�����');
//	print_r($arr);
//	for($i=0;$i<TEXT_COUNT;$i++){
//		$str=file_get_contents(TEXT_PATH.'/'.($i+10).'.txt');
//		$str=str_replace('����','<font color="red">����</font>',$str);
//		$str=str_replace('��˾','<font color="red">��˾</font>',$str);
//		$str=str_replace('����','<font color="red">����</font>',$str);
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

//	$a=array('3G','�Ժ�22','213','34.123',0);
//	$a=array_filter($a,'filter_number');
//	print_r($a);
//	$s=(!is_int($a[4]));
//	echo $s;
?>
