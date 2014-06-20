<?php
	class Tool{
		
		/*	fileDir($path)
		 *	 取出所有文档目录
		 *	该方法需要传递一个存储文档的路径参数
		 *	这个方法会在分词的时候调用，也用来计算文档总数
		 *	返回文件数组
		 */
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
		
		/*
		 * 	fileStr($file_name)
		 * 	获取每篇文档的内容
		 * 	该方法需要传递一个文件名参数，通过文件名获取文档内容
		 * 	返回文本内容
		 */
		static public function fileStr($file_name){			
			$str=file_get_contents(TEXT_PATH.'/'.$file_name);	//读取文本	
			return $str;
		}
		

		/*
		 * 	segment($str)
		 * 	利用scws分词，并把分词结果存入数据库
		 * 	该方法需要传递一个参数，如果参数是数组，则数组保存内容应该是文件名，通过文件名获取文件内容进行分词，
		 * 	如果参数是字符串，则该字符串应该是用户的查询词
		 * 	
		 */
		static function segment($str){
			$db=DB::getDB();
			if(!$scws=scws_new()) exit('创建SCWS对象失败！');		//创建SCWS
			$scws->set_charset('gbk');	//设置字符集
			if(!$scws->set_dict('C:\Program Files\scws\dict.xdb')) exit('词典路径设置失败！');
			$scws->set_multi(1);
			$scws->set_ignore(true);	//忽略标点
			
			if(is_string($str)){
				$scws->send_text($str);
				$top=$scws->get_tops(50);
				return $top;
			}else if(is_array($str)){
				for($i=0;$i<TEXT_COUNT;$i++){
					$file_name=$str[$i];	//文件名
					$text=self::fileStr($file_name);	//获取文本内容
					$text_len=mb_strlen($text,'gbk');	//文本长度
					$scws->send_text($text);
					$top=$scws->get_tops(1000);
					DB::insertDB($top,$file_name,$text_len,$db);
				}
			}
			$db->close();
			$db=null;
		}
		
		/*
		 * 	sim($query,$weight=null)
		 * 	计算相似度
		 * 	该方法接收两个参数，查询词以及对应的权重
		 * 	计算查询词与文档的相似度，并返回
		 */
		static public function sim($query,$weight=null){
			$sim=array();//相似度
			$query_vsm=self::query_vsm($query,$weight);	//查询式向量空间
			$query_vsm_keys=array_keys($query_vsm);
			$query_mod=self::mod($query_vsm);	//查询式的模

			$db=DB::getDB();
			$results=$db->query("SELECT vsm_keys,vsm_values,file_name FROM news");
			while(!!$rows=$results->fetch_assoc()){
				//$words_arr=explode(',',$rows['words_str']);
				$vsm_keys=explode(',',$rows['vsm_keys']);
				$vsm_values=explode(',',$rows['vsm_values']);
				$vsm_arr=array_combine($vsm_keys,$vsm_values);

				$vsm_mod=self::mod($vsm_values);	//文档的模

				$inner_mul=0;	//内积
				$count=0;	//计算前
				for($i=0;$i<count($query_vsm_keys);$i++){
					if(array_key_exists($query_vsm_keys[$i],$vsm_arr)){
						$inner_mul+=$query_vsm[$query_vsm_keys[$i]]*$vsm_arr[$query_vsm_keys[$i]];
						$count++;
					}
				}
				if($count!=count($query_vsm_keys)){
					continue;
				}
//				for($i=0;$i<count($query_vsm_keys);$i++){
//					if(array_key_exists($query_vsm_keys[$i],$vsm_arr)){
//						$inner_mul+=$query_vsm[$query_vsm_keys[$i]]*$vsm_arr[$query_vsm_keys[$i]];	
//					}
//					
//				}
				$mod_mul=$query_mod*$vsm_mod;	//模相乘
				if($mod_mul==0){
					exit('除数不能为0');
				}
				$sim[($rows['file_name'])]=number_format($inner_mul/$mod_mul,6);	//相似度
			}
//			$sim_keys_arr=array_keys($sim);
//			$sim_str=implode(',',$sim);
//			$sim_keys_str=implode(',',$sim_keys_arr);
			//$sim_input=$sim_keys_str.'|'.$sim_str;
			//$db->query("INSERT INTO sim (sim) VALUES ('$sim_input')");
			DB::unDB($results,$db);
			
			$sim=array_filter($sim,'filter_zero');//过滤相似度为0的值
			arsort($sim);
			return $sim;
	
		}
		

		
		//求向量空间的模
		static public function mod($vsm_arr){
			$vsm_squ=array_map('call_mod',$vsm_arr);	//对每个值求平方
			$vsm_sum=array_sum($vsm_squ);	//计算数组所有值的和
			$vsm_mod=sqrt($vsm_sum);		//求模
			
			return number_format($vsm_mod,6);
		}
		

		/*
		 * 	query_vsm($query,$weight=null)
		 * 	计算查询词的特征向量计算
		 * 	在词典中查找查询词，默认赋予权重为1
		 * 	返回查询词的特征向量
		 */
		static public function query_vsm($query,$weight=null){
			
			$query_seg=array();
			$dic=file_get_contents('dic.txt');
			$dic_keys=explode(',',$dic);
			$vsm_arr=array();	//向量空间
			
			if(!empty($weight)){
				$query=explode(',', $query);
				$weight=explode(',', $weight);
				for($i=0;$i<count($weight);$i++){
					$query_seg=self::segment($query[$i]);
					
					for($j=0;$j<count($query_seg);$j++){
						if(in_array($query_seg[$j]['word'],$dic_keys)){
							$key=array_search($query_seg[$j]['word'],$dic_keys);//获取对应值的数字索引
							$vsm_arr[$key]=$weight[$i];
						}
					}
				}
			}else{
				$query_seg=self::segment($query);
			
				for($i=0;$i<count($query_seg);$i++){
					if(in_array($query_seg[$i]['word'],$dic_keys)){
						$key=array_search($query_seg[$i]['word'],$dic_keys);//获取对应值的数字索引
						$vsm_arr[$key]=1;
					}
				}
			}
			if($vsm_arr==null){
				exit('无搜索结果！');
			}else{
				return $vsm_arr;
			}
			
		}
		
	
		/*
		 * 	tf_df()
		 * 	计算文档的tf*idf，并存入数据库
		 */
		static public function tf_df(){
			//取出df
			$dic=file_get_contents('dic.txt');
			$df=file_get_contents('df.txt');
			$dic_keys=explode(',',$dic);
			$df_values=explode(',',$df);
			//$df=array_combine($df_keys,$df_values);	//关联数组
			$df=$dic=null;
			$db=DB::getDB();
			$results=$db->query("SELECT words_str,times_str,text_len,file_name FROM news");

			while(!!$rows=$results->fetch_assoc()){
				$words_arr=explode(',',$rows['words_str']);
				$times_arr=explode(',',$rows['times_str']);
				for($i=0;$i<count($words_arr);$i++){	
					$tf[$i]=number_format($times_arr[$i]/$rows['text_len'],6);
					$key=array_search($words_arr[$i],$dic_keys);
	
					if($df_values[$key]==0||$df_values[$key]=='0'){
						exit('除数不能为0');
					}
					$idf[$i]=number_format(log(TEXT_COUNT/$df_values[$key]),6);
					$vsm[$key]=number_format($tf[$i]*$idf[$i],6);
				}

//				$tf=implode(',',$tf);
//				$idf=implode(',',$idf);
				$vsm_keys_arr=array_keys($vsm);
				$vsm_keys_str=implode(',',$vsm_keys_arr);
				$vsm_values_str=implode(',',$vsm);

				$db->query("UPDATE news SET vsm_keys='$vsm_keys_str',vsm_values='$vsm_values_str' WHERE file_name='{$rows['file_name']}'");
				$tf=null;
				$idf=null;
				$vsm=null;
			}
			DB::unDB($results,$db);
		}
		

		/*
		 * 	dic()
		 * 	获取并存储词表、文档频率
		 * 	该方法是用segment（）方法得到的分词结果进行词表存储以及文档频率的计算
		 */
		static public function dic(){
			$db=DB::getDB();
			$dic=array();
			$results=$db->query("SELECT words_str FROM news");
			while(!!$rows=$results->fetch_assoc()){
//				if(!get_magic_quotes_gpc()){
//					$words_str=stripslashes($rows['words_str']);
//				}else{
				$words_str=$rows['words_str'];

				$words_arr=explode(',',$words_str);
				$dic=array_merge($dic,$words_arr);
			}
			
			$df=array_count_values($dic);	//文档频率
			//ksort($df);	//混排会出问题
			$df_keys_arr=array_keys($df);
			$df_values_arr=array_values($df);
		
			$df_values_str=implode(',',$df_values_arr);
			$df_keys_str=implode(',',$df_keys_arr);
			
			//使用df代替词表		
//			$dic=array_unique($dic);
//			sort($dic);
//			$dic=implode(',',$dic);
			
			//把词表存储起来
			$fp=fopen('dic.txt','w');
			if(!$fp) exit('词典打开失败！');
			if(!fwrite($fp,$df_keys_str)) exit('词典写入失败！');
			fclose($fp);
			
			//把文档频率写入文件
			$fp=fopen('df.txt','w');
			if(!$fp) exit('df.txt打开失败！');
			if(!fwrite($fp,$df_values_str)) exit('文档频率写入失败！');
			fclose($fp);
			//销毁数据库
			DB::unDB($results,$db);
		}
			

		/*
		 * 	strLimit($str,$len)
		 * 	截词
		 * 	该方法主要用于排序结果的标题、摘要的截取
		 * 	需要存到两个参数，第一个参数表示要截取的文本，第二个参数表示要截取的长度
		 *	返回截取结果
		 */
		static public function strLimit($str,$len){
			if(mb_strlen($str,'gbk')>$len){
				$str=mb_substr($str,0,$len,'gbk').'...';
			}
			return $str;
		}
		
		
		/*
		 * 	highLight($query)
		 * 	用户前端显示结果的高亮显示
		 * 	该方法需要传递一个参数，表示要高亮的词
		 * 	返回高亮后的代码
		 */
		static public function highLight($query){	
			$q=Tool::segment($query);	
			$highLight=$q_arr=$q_replace=array();
			for($i=0;$i<count($q);$i++){
				$q_arr[]=$q[$i]['word'];
				$q_replace[]='<font color="red">'.$q[$i]['word'].'</font>';
			}
			$highLight[0]=$q_arr;
			$highLight[1]=$q_replace;
			return $highLight;
		}
		
		/*
		 * 	keyword($keys)
		 * 	抽取关键词
		 * 	该方法用于多个文档的关键词抽取，传递的参数是一个文件数组
		 * 	返回抽取到的关键词
		 */
		static public function keyword($keys){
			$keyword=array();	//放置特征词的数组
			$db=DB::getDB();
		
			//$keys_str=implode(',',$keys);
			for($i=0;$i<count($keys);$i++){	
				$result=$db->query("SELECT words_str,vsm_values FROM news WHERE file_name='$keys[$i]'");
				if(!$result){
					exit('特征词抽取失败');
				}
				$row=$result->fetch_assoc();
				$words_arr=explode(',',$row['words_str']);
				$vsm_values_arr=explode(',',$row['vsm_values']);
				$vsm=array_combine($words_arr,$vsm_values_arr);
				arsort($vsm);
				$vsm=array_slice($vsm,0,5);	//取权重前5的元素
				$key=array_keys($vsm);	
				$keyword=array_merge($keyword,$key);
			}		
				$keyword=array_unique($keyword);
				$keyword=array_filter($keyword,'filter_number');
				rsort($keyword);
				return $keyword;
		}
		
		/*
		 * 	getFileKeys($fileName)
		 * 	抽取关键词
		 * 	该方法用于单个文档的关键词抽取，传递的参数是一个文件名字符串
		 * 	返回抽取到的关键词
		 */
		static public function getFileKeys($fileName){
			$db=DB::getDB();
			$result=$db->query("SELECT words_str,vsm_values FROM news WHERE file_name='$fileName'");
			if(!$result){
				exit('特征词抽取失败');
			}
			$row=$result->fetch_assoc();
			$words_arr=explode(',',$row['words_str']);
			$vsm_values_arr=explode(',',$row['vsm_values']);
			$vsm=array_combine($words_arr,$vsm_values_arr);
			arsort($vsm);
			$vsm=array_slice($vsm,0,5);	//取权重前5的元素
			$key=array_keys($vsm);
			$key=array_filter($key,'filter_number');
			return $key;
		}
		
	}


?>

