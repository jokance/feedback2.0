<?php

	require_once 'init.inc.php';		//���������ļ�
	
	$file_dir=Tool::fileDir();		//�ĵ�����
	
	$str=array();	//�洢�ĵ��ͳ���
	for($i=0;$i<count($file_dir);$i++){
		$str[$i]['con']=file_get_contents(TEXT_PATH.'/'.$file_dir[$i]);	//��ȡ�ı�
		$str[$i]['con']=preg_replace('/\s+/','',$str[$i]['con']);		//�����ո��������Ͳ��ʺ�Ӣ�ļ���
		$str[$i]['len']=mb_strlen($str[$i]['con'],'gbk');		//�ı�����
	}
	
	
	$dic=array();	//�ʱ�
	
	$df=array();	//�ĵ�Ƶ��
/*		
	$seg=Tool::segment($str);	//�õ��ִʽ��
	print_r($seg);

	for($i=0;$i<count($seg);$i++){
		for($j=0;$j<count($seg[$i]);$j++){
			//array_push($dic,$seg[$i][$j]['word']);	
			$seg[$i][$j]['tf']=$seg[$i][$j]['times']/$str[$i]['len'];	//��Ƶtf
			array_push($df,$seg[$i][$j]['word']);	//���дʺϲ�,df
		}
	}
	$df=array_count_values($df);	//�ĵ�Ƶ��df
	
	for($i=0;$i<count($seg);$i++){
		for($j=0;$j<count($seg[$i]);$j++){
			$seg[$i][$j]['df']=$df[($seg[$i][$j]['word'])];	//��ÿ���ʸ���df
			$seg[$i][$j]['idf']=log(count($file_dir)/$seg[$i][$j]['df']);	//idf
		}
	}	
	
	$dic_str=file_get_contents('dic.txt');
	$dic_arr=explode(',',$dic_str);
	
	$vsm_arr=array();	//�����ռ�
	for($i=0;$i<count($dic_arr);$i++){
		$vsm_arr[$dic_arr[$i]]=0;	//��ʼ��Ϊ0
	}
	
	//VSM
	for($i=0;$i<count($seg);$i++){
		for($j=0;$j<count($seg[$i]);$j++){
			if(in_array($seg[$i][$j]['word'],$dic_arr)){
				$vsm_arr[($seg[$i][$j]['word'])]=$seg[$i][$j]['tf']*$seg[$i][$j]['idf'];
			}
		}
		
		
		//��vsmд���ļ�
//		$vsm_str=implode(',',$vsm_arr);
//		$fp=fopen(ROOT_PATH.'/vsm/'.$file_dir[$i],'w');
//		fwrite($fp,$vsm_str);
//		fclose($fp);
	}

//	$dic=array_unique($dic);
//	sort($dic);
//	$dic=implode(',',$dic);
	
	//�Ѵʱ�洢����
//	$fp=fopen('dic.txt','w');
//	fwrite($fp,$dic);
//	fclose($fp);
	
	//ȡ���ʱ�
	
*/
?>

<?php
// hightman, SCWS v4 (built as php_extension)
// 2007/06/02
//
// view the source code
//
//require_once 'init.inc.php';		//���������ļ�
////	
//$str=file_get_contents(TEXT_PATH.'/10.txt');		//��ȡ�ı�
//// do the segment
//$cws = scws_new();
//$cws->set_charset('gbk');
//$cws->set_rule('C:\Program Files\scws\rules.ini');
//$cws->set_dict('C:\Program Files\scws\dict.xdb');
//
////
//// use default dictionary & rules
//$cws->send_text($str);
//
// while (!!$res = $cws->get_result())
//    {
//        foreach ($res as $tmp)
//        {
//            if ($tmp['len'] == 1 && $tmp['word'] == "\r")
//                continue;
//            if ($tmp['len'] == 1 && $tmp['word'] == "\n")
//                echo $tmp['word'];
//            else if ($showa)
//                printf("%s/%s ", $tmp['word'], $tmp['attr']);
//            else
//                printf("%s ", $tmp['word']);
//        }
//        flush();
//    }
?>