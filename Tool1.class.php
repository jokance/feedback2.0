<?php
	class Tool{
		

		//取出所有文档目录
		static public function fileDir($path){
			$files_name=array();
			$dir=opendir($path);
		    while (false!==($file=readdir($dir))) {
        		if ($file != "." && $file != ".."){
            		$files_name[]=$file;
       		 	}
			}
			closedir($dir);
			return $files_name;
		}
		
		//获取每篇文档的内容和长度
		static public function fileStr(){
			$file_dir=self::fileDir(TEXT_PATH);
			$str=array();	//存储文档和长度
			for($i=0;$i<count($file_dir);$i++){
				$str[$i]['con']=file_get_contents(TEXT_PATH.'/'.$file_dir[$i]);	//读取文本
				$str[$i]['con']=preg_replace('/\s+/','',$str[$i]['con']);		//消除空格，这样做就不适合英文检索
				$str[$i]['len']=mb_strlen($str[$i]['con'],'gbk');		//文本长度
			}
			return $str;
		}
		
		//scws分词
		static function segment($str){
			
			if(!$scws=scws_new()) exit('创建SCWS对象失败！');		//创建SCWS
			$scws->set_charset('gbk');	//设置字符集
			if(!$scws->set_dict('C:\Program Files\scws\dict.xdb')) exit('词典路径设置失败！');
			$scws->set_multi(1);
			$scws->set_ignore(true);	//忽略标点
			
			if(is_string($str)){
				$scws->send_text($str);
				$top=$scws->get_tops(800);
			}else if(is_array($str)){
				for($i=0;$i<count($str);$i++){
					$scws->send_text($str[$i]['con']);
					$top[]=$scws->get_tops(800);
				}
			}

			return $top;
			
		}
		
		//计算相似度
		static public function sim($query){
			$sim=array();//相似度
			$query_vsm=self::query_vsm($query);	//查询式向量空间，关联索引
			$query_vsm_keys=array_keys($query_vsm);
			//$query_vsm_str=file_get_contents(ROOT_PATH.'/query_vsm/1.txt');	//从文件读取，数字索引
			//$query_vsm_arr=explode(',',$query_vsm_str);
			$query_mod=self::mod($query_vsm);	//查询式的模
			$files_name=self::fileDir(ROOT_PATH.'/vsm');	//存储文档集向量空间的文件目录集
			for($i=0;$i<count($files_name);$i++){
				$vsm_str=file_get_contents(ROOT_PATH.'/vsm/'.$files_name[$i]);
				$vsm_arr=explode('|',$vsm_str);	

				$vsm_keys=explode(',',$vsm_arr[0]);
				$vsm_values=explode(',',$vsm_arr[1]);
				$vsm_arr=array_combine($vsm_keys,$vsm_values);
				$vsm_mod=self::mod($vsm_arr);	//文档的模
				$inner_mul=0;	//内积
				for($i=0;$i<count($query_vsm_keys);$i++){
					if(array_key_exists($query_vsm_keys[$i],$vsm_arr)){
						$inner_mul+=$query_vsm[$query_vsm_keys[$i]]*$vsm_arr[$query_vsm_keys[$i]];
					}
				}
				//另一种计算内积方法
//				$vsm_inter=array_intersect_key($vsm_arr,$query_vsm);
//				$query_inter=array_intersect_key($query_vsm,$vsm_arr);
//				$inner_mul=self::arr_inner_mul($vsm_inter,$query_inter);
				
				$mod_mul=$query_mod*$vsm_mod;	//模相乘
				$sim[$files_name[$i]]=$inner_mul/$mod_mul;	//相似度
			}
			//将相似度写入文件
			$sim_key_arr=array_keys($sim);
			$sim_keys_str=implode(',',$sim_key_arr);
			$sim_str=implode(',',$sim);
			$fp=fopen(ROOT_PATH.'/sim/1.txt','w');	//第一次查询的相似度
			fwrite($fp,$sim_keys_str.'|'.$sim_str);
			fclose($fp);
			return $sim;
		
			
		}
		
		//计算两个数组的内积
		static public function arr_inner_mul($arr1,$arr2){
			call_mul($m,$n);
			$arr_mul=array_map('call_mul',$arr1,$arr2);
			$inner_mul=array_sum($arr_mul);
			return $inner_mul;
		}
		
		//求向量空间的模
		static public function mod($vsm_arr){
			call_mod($n);
			$vsm_squ=array_map('call_mod',$vsm_arr);	//对每个值求平方
			$vsm_sum=array_sum($vsm_squ);	//计算数组所有值的和
			$vsm_mod=sqrt($vsm_sum);		//求模
			
			return $vsm_mod;
		}
		
		//查询词的特征向量计算
		static public function query_vsm($query){
			$query_seg=self::segment($query);
			$dic_str=file_get_contents('dic.txt');
			$dic_arr=explode(',',$dic_str);
			$vsm_arr=array();	//向量空间
			
			for($i=0;$i<count($query_seg);$i++){
				if(in_array($query_seg[$i]['word'],$dic_arr)){
					$vsm_arr[($query_seg[$i]['word'])]=1;
				}
				//将vsm写入文件
				$vsm_values=implode(',',$vsm_arr);
				$vsm_keys=array_keys($vsm_arr);
				$vsm_keys=implode(',',$vsm_keys);
				$fp=fopen(ROOT_PATH.'/query_vsm/1.txt','w');
				fwrite($fp,$vsm_keys.'|'.$vsm_values);
				fclose($fp);
			}
			return $vsm_arr;
		}
		
		//计算tf\idf
		static public function tf_df($seg){
			$str=self::fileStr();
			$file_dir=self::fileDir(TEXT_PATH);
			$df=array();
			for($i=0;$i<count($seg);$i++){
				for($j=0;$j<count($seg[$i]);$j++){	
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
			return $seg;
		}
		
		//获取并存储词表
		static public function dic($seg){
			$dic=array();	//词表
			for($i=0;$i<count($seg);$i++){
				for($j=0;$j<count($seg[$i]);$j++){
					array_push($dic,$seg[$i][$j]['word']);	
				}
			}
			
			$dic=array_unique($dic);
			sort($dic);
			$dic=implode(',',$dic);
			
			//把词表存储起来
			$fp=fopen('dic.txt','w');
			if(!$fp) exit('词典打开失败！');
			if(!fwrite($fp,$dic)) exit('词典写入失败！');
			fclose($fp);
		}	

		
		//特征向量,并写入文件(tf/idf以及存在seg中)
		static public function vsm($seg){
			$file_dir=self::fileDir(TEXT_PATH);
			$dic_str=file_get_contents('dic.txt');
			$dic_arr=explode(',',$dic_str);
			
			$vsm_arr=array();	//向量空间
			
			for($i=0;$i<count($seg);$i++){
				for($j=0;$j<count($seg[$i]);$j++){
					if(in_array($seg[$i][$j]['word'],$dic_arr)){
						$vsm_arr[($seg[$i][$j]['word'])]=$seg[$i][$j]['tf']*$seg[$i][$j]['idf'];
					}
				}
				//将vsm写入文件
				$vsm_values=implode(',',$vsm_arr);
				$vsm_keys=array_keys($vsm_arr);
				$vsm_keys=implode(',',$vsm_keys);
				$fp=fopen(ROOT_PATH.'/vsm/'.$file_dir[$i],'w');
				fwrite($fp,$vsm_keys.'|'.$vsm_values);
				fclose($fp);
				$vsm_arr=null;	//清除
			}
		}
		
		
	}


?>