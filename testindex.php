<?php

	require_once 'init.inc.php';		//引入配置文件
	
	$file_dir=Tool::fileDir();		//文档总数
	
	$str=array();	//存储文档和长度
	for($i=0;$i<count($file_dir);$i++){
		$str[$i]['con']=file_get_contents(TEXT_PATH.'/'.$file_dir[$i]);	//读取文本
		$str[$i]['con']=preg_replace('/\s+/','',$str[$i]['con']);		//消除空格，这样做就不适合英文检索
		$str[$i]['len']=mb_strlen($str[$i]['con'],'gbk');		//文本长度
	}
	
	
	$dic=array();	//词表
	
	$df=array();	//文档频率
/*		
	$seg=Tool::segment($str);	//得到分词结果
	print_r($seg);

	for($i=0;$i<count($seg);$i++){
		for($j=0;$j<count($seg[$i]);$j++){
			//array_push($dic,$seg[$i][$j]['word']);	
			$seg[$i][$j]['tf']=$seg[$i][$j]['times']/$str[$i]['len'];	//词频tf
			array_push($df,$seg[$i][$j]['word']);	//所有词合并,df
		}
	}
	$df=array_count_values($df);	//文档频率df
	
	for($i=0;$i<count($seg);$i++){
		for($j=0;$j<count($seg[$i]);$j++){
			$seg[$i][$j]['df']=$df[($seg[$i][$j]['word'])];	//给每个词赋予df
			$seg[$i][$j]['idf']=log(count($file_dir)/$seg[$i][$j]['df']);	//idf
		}
	}	
	
	$dic_str=file_get_contents('dic.txt');
	$dic_arr=explode(',',$dic_str);
	
	$vsm_arr=array();	//向量空间
	for($i=0;$i<count($dic_arr);$i++){
		$vsm_arr[$dic_arr[$i]]=0;	//初始化为0
	}
	
	//VSM
	for($i=0;$i<count($seg);$i++){
		for($j=0;$j<count($seg[$i]);$j++){
			if(in_array($seg[$i][$j]['word'],$dic_arr)){
				$vsm_arr[($seg[$i][$j]['word'])]=$seg[$i][$j]['tf']*$seg[$i][$j]['idf'];
			}
		}
		
		
		//将vsm写入文件
//		$vsm_str=implode(',',$vsm_arr);
//		$fp=fopen(ROOT_PATH.'/vsm/'.$file_dir[$i],'w');
//		fwrite($fp,$vsm_str);
//		fclose($fp);
	}

//	$dic=array_unique($dic);
//	sort($dic);
//	$dic=implode(',',$dic);
	
	//把词表存储起来
//	$fp=fopen('dic.txt','w');
//	fwrite($fp,$dic);
//	fclose($fp);
	
	//取出词表
	
*/
?>

<?php
// hightman, SCWS v4 (built as php_extension)
// 2007/06/02
//
// view the source code
//
//require_once 'init.inc.php';		//引入配置文件
////	
//$str=file_get_contents(TEXT_PATH.'/10.txt');		//读取文本
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