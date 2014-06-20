<?php
	class Tool{
		
		/*	fileDir($path)
		 *	 ȡ�������ĵ�Ŀ¼
		 *	�÷�����Ҫ����һ���洢�ĵ���·������
		 *	����������ڷִʵ�ʱ����ã�Ҳ���������ĵ�����
		 *	�����ļ�����
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
		 * 	��ȡÿƪ�ĵ�������
		 * 	�÷�����Ҫ����һ���ļ���������ͨ���ļ�����ȡ�ĵ�����
		 * 	�����ı�����
		 */
		static public function fileStr($file_name){			
			$str=file_get_contents(TEXT_PATH.'/'.$file_name);	//��ȡ�ı�	
			return $str;
		}
		

		/*
		 * 	segment($str)
		 * 	����scws�ִʣ����ѷִʽ���������ݿ�
		 * 	�÷�����Ҫ����һ��������������������飬�����鱣������Ӧ�����ļ�����ͨ���ļ�����ȡ�ļ����ݽ��зִʣ�
		 * 	����������ַ���������ַ���Ӧ�����û��Ĳ�ѯ��
		 * 	
		 */
		static function segment($str){
			$db=DB::getDB();
			if(!$scws=scws_new()) exit('����SCWS����ʧ�ܣ�');		//����SCWS
			$scws->set_charset('gbk');	//�����ַ���
			if(!$scws->set_dict('C:\Program Files\scws\dict.xdb')) exit('�ʵ�·������ʧ�ܣ�');
			$scws->set_multi(1);
			$scws->set_ignore(true);	//���Ա��
			
			if(is_string($str)){
				$scws->send_text($str);
				$top=$scws->get_tops(50);
				return $top;
			}else if(is_array($str)){
				for($i=0;$i<TEXT_COUNT;$i++){
					$file_name=$str[$i];	//�ļ���
					$text=self::fileStr($file_name);	//��ȡ�ı�����
					$text_len=mb_strlen($text,'gbk');	//�ı�����
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
		 * 	�������ƶ�
		 * 	�÷�������������������ѯ���Լ���Ӧ��Ȩ��
		 * 	�����ѯ�����ĵ������ƶȣ�������
		 */
		static public function sim($query,$weight=null){
			$sim=array();//���ƶ�
			$query_vsm=self::query_vsm($query,$weight);	//��ѯʽ�����ռ�
			$query_vsm_keys=array_keys($query_vsm);
			$query_mod=self::mod($query_vsm);	//��ѯʽ��ģ

			$db=DB::getDB();
			$results=$db->query("SELECT vsm_keys,vsm_values,file_name FROM news");
			while(!!$rows=$results->fetch_assoc()){
				//$words_arr=explode(',',$rows['words_str']);
				$vsm_keys=explode(',',$rows['vsm_keys']);
				$vsm_values=explode(',',$rows['vsm_values']);
				$vsm_arr=array_combine($vsm_keys,$vsm_values);

				$vsm_mod=self::mod($vsm_values);	//�ĵ���ģ

				$inner_mul=0;	//�ڻ�
				$count=0;	//����ǰ
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
				$mod_mul=$query_mod*$vsm_mod;	//ģ���
				if($mod_mul==0){
					exit('��������Ϊ0');
				}
				$sim[($rows['file_name'])]=number_format($inner_mul/$mod_mul,6);	//���ƶ�
			}
//			$sim_keys_arr=array_keys($sim);
//			$sim_str=implode(',',$sim);
//			$sim_keys_str=implode(',',$sim_keys_arr);
			//$sim_input=$sim_keys_str.'|'.$sim_str;
			//$db->query("INSERT INTO sim (sim) VALUES ('$sim_input')");
			DB::unDB($results,$db);
			
			$sim=array_filter($sim,'filter_zero');//�������ƶ�Ϊ0��ֵ
			arsort($sim);
			return $sim;
	
		}
		

		
		//�������ռ��ģ
		static public function mod($vsm_arr){
			$vsm_squ=array_map('call_mod',$vsm_arr);	//��ÿ��ֵ��ƽ��
			$vsm_sum=array_sum($vsm_squ);	//������������ֵ�ĺ�
			$vsm_mod=sqrt($vsm_sum);		//��ģ
			
			return number_format($vsm_mod,6);
		}
		

		/*
		 * 	query_vsm($query,$weight=null)
		 * 	�����ѯ�ʵ�������������
		 * 	�ڴʵ��в��Ҳ�ѯ�ʣ�Ĭ�ϸ���Ȩ��Ϊ1
		 * 	���ز�ѯ�ʵ���������
		 */
		static public function query_vsm($query,$weight=null){
			
			$query_seg=array();
			$dic=file_get_contents('dic.txt');
			$dic_keys=explode(',',$dic);
			$vsm_arr=array();	//�����ռ�
			
			if(!empty($weight)){
				$query=explode(',', $query);
				$weight=explode(',', $weight);
				for($i=0;$i<count($weight);$i++){
					$query_seg=self::segment($query[$i]);
					
					for($j=0;$j<count($query_seg);$j++){
						if(in_array($query_seg[$j]['word'],$dic_keys)){
							$key=array_search($query_seg[$j]['word'],$dic_keys);//��ȡ��Ӧֵ����������
							$vsm_arr[$key]=$weight[$i];
						}
					}
				}
			}else{
				$query_seg=self::segment($query);
			
				for($i=0;$i<count($query_seg);$i++){
					if(in_array($query_seg[$i]['word'],$dic_keys)){
						$key=array_search($query_seg[$i]['word'],$dic_keys);//��ȡ��Ӧֵ����������
						$vsm_arr[$key]=1;
					}
				}
			}
			if($vsm_arr==null){
				exit('�����������');
			}else{
				return $vsm_arr;
			}
			
		}
		
	
		/*
		 * 	tf_df()
		 * 	�����ĵ���tf*idf�����������ݿ�
		 */
		static public function tf_df(){
			//ȡ��df
			$dic=file_get_contents('dic.txt');
			$df=file_get_contents('df.txt');
			$dic_keys=explode(',',$dic);
			$df_values=explode(',',$df);
			//$df=array_combine($df_keys,$df_values);	//��������
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
						exit('��������Ϊ0');
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
		 * 	��ȡ���洢�ʱ��ĵ�Ƶ��
		 * 	�÷�������segment���������õ��ķִʽ�����дʱ�洢�Լ��ĵ�Ƶ�ʵļ���
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
			
			$df=array_count_values($dic);	//�ĵ�Ƶ��
			//ksort($df);	//���Ż������
			$df_keys_arr=array_keys($df);
			$df_values_arr=array_values($df);
		
			$df_values_str=implode(',',$df_values_arr);
			$df_keys_str=implode(',',$df_keys_arr);
			
			//ʹ��df����ʱ�		
//			$dic=array_unique($dic);
//			sort($dic);
//			$dic=implode(',',$dic);
			
			//�Ѵʱ�洢����
			$fp=fopen('dic.txt','w');
			if(!$fp) exit('�ʵ��ʧ�ܣ�');
			if(!fwrite($fp,$df_keys_str)) exit('�ʵ�д��ʧ�ܣ�');
			fclose($fp);
			
			//���ĵ�Ƶ��д���ļ�
			$fp=fopen('df.txt','w');
			if(!$fp) exit('df.txt��ʧ�ܣ�');
			if(!fwrite($fp,$df_values_str)) exit('�ĵ�Ƶ��д��ʧ�ܣ�');
			fclose($fp);
			//�������ݿ�
			DB::unDB($results,$db);
		}
			

		/*
		 * 	strLimit($str,$len)
		 * 	�ش�
		 * 	�÷�����Ҫ�����������ı��⡢ժҪ�Ľ�ȡ
		 * 	��Ҫ�浽������������һ��������ʾҪ��ȡ���ı����ڶ���������ʾҪ��ȡ�ĳ���
		 *	���ؽ�ȡ���
		 */
		static public function strLimit($str,$len){
			if(mb_strlen($str,'gbk')>$len){
				$str=mb_substr($str,0,$len,'gbk').'...';
			}
			return $str;
		}
		
		
		/*
		 * 	highLight($query)
		 * 	�û�ǰ����ʾ����ĸ�����ʾ
		 * 	�÷�����Ҫ����һ����������ʾҪ�����Ĵ�
		 * 	���ظ�����Ĵ���
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
		 * 	��ȡ�ؼ���
		 * 	�÷������ڶ���ĵ��Ĺؼ��ʳ�ȡ�����ݵĲ�����һ���ļ�����
		 * 	���س�ȡ���Ĺؼ���
		 */
		static public function keyword($keys){
			$keyword=array();	//���������ʵ�����
			$db=DB::getDB();
		
			//$keys_str=implode(',',$keys);
			for($i=0;$i<count($keys);$i++){	
				$result=$db->query("SELECT words_str,vsm_values FROM news WHERE file_name='$keys[$i]'");
				if(!$result){
					exit('�����ʳ�ȡʧ��');
				}
				$row=$result->fetch_assoc();
				$words_arr=explode(',',$row['words_str']);
				$vsm_values_arr=explode(',',$row['vsm_values']);
				$vsm=array_combine($words_arr,$vsm_values_arr);
				arsort($vsm);
				$vsm=array_slice($vsm,0,5);	//ȡȨ��ǰ5��Ԫ��
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
		 * 	��ȡ�ؼ���
		 * 	�÷������ڵ����ĵ��Ĺؼ��ʳ�ȡ�����ݵĲ�����һ���ļ����ַ���
		 * 	���س�ȡ���Ĺؼ���
		 */
		static public function getFileKeys($fileName){
			$db=DB::getDB();
			$result=$db->query("SELECT words_str,vsm_values FROM news WHERE file_name='$fileName'");
			if(!$result){
				exit('�����ʳ�ȡʧ��');
			}
			$row=$result->fetch_assoc();
			$words_arr=explode(',',$row['words_str']);
			$vsm_values_arr=explode(',',$row['vsm_values']);
			$vsm=array_combine($words_arr,$vsm_values_arr);
			arsort($vsm);
			$vsm=array_slice($vsm,0,5);	//ȡȨ��ǰ5��Ԫ��
			$key=array_keys($vsm);
			$key=array_filter($key,'filter_number');
			return $key;
		}
		
	}


?>

