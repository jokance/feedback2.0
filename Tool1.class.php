<?php
	class Tool{
		

		//ȡ�������ĵ�Ŀ¼
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
		
		//��ȡÿƪ�ĵ������ݺͳ���
		static public function fileStr(){
			$file_dir=self::fileDir(TEXT_PATH);
			$str=array();	//�洢�ĵ��ͳ���
			for($i=0;$i<count($file_dir);$i++){
				$str[$i]['con']=file_get_contents(TEXT_PATH.'/'.$file_dir[$i]);	//��ȡ�ı�
				$str[$i]['con']=preg_replace('/\s+/','',$str[$i]['con']);		//�����ո��������Ͳ��ʺ�Ӣ�ļ���
				$str[$i]['len']=mb_strlen($str[$i]['con'],'gbk');		//�ı�����
			}
			return $str;
		}
		
		//scws�ִ�
		static function segment($str){
			
			if(!$scws=scws_new()) exit('����SCWS����ʧ�ܣ�');		//����SCWS
			$scws->set_charset('gbk');	//�����ַ���
			if(!$scws->set_dict('C:\Program Files\scws\dict.xdb')) exit('�ʵ�·������ʧ�ܣ�');
			$scws->set_multi(1);
			$scws->set_ignore(true);	//���Ա��
			
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
		
		//�������ƶ�
		static public function sim($query){
			$sim=array();//���ƶ�
			$query_vsm=self::query_vsm($query);	//��ѯʽ�����ռ䣬��������
			$query_vsm_keys=array_keys($query_vsm);
			//$query_vsm_str=file_get_contents(ROOT_PATH.'/query_vsm/1.txt');	//���ļ���ȡ����������
			//$query_vsm_arr=explode(',',$query_vsm_str);
			$query_mod=self::mod($query_vsm);	//��ѯʽ��ģ
			$files_name=self::fileDir(ROOT_PATH.'/vsm');	//�洢�ĵ��������ռ���ļ�Ŀ¼��
			for($i=0;$i<count($files_name);$i++){
				$vsm_str=file_get_contents(ROOT_PATH.'/vsm/'.$files_name[$i]);
				$vsm_arr=explode('|',$vsm_str);	

				$vsm_keys=explode(',',$vsm_arr[0]);
				$vsm_values=explode(',',$vsm_arr[1]);
				$vsm_arr=array_combine($vsm_keys,$vsm_values);
				$vsm_mod=self::mod($vsm_arr);	//�ĵ���ģ
				$inner_mul=0;	//�ڻ�
				for($i=0;$i<count($query_vsm_keys);$i++){
					if(array_key_exists($query_vsm_keys[$i],$vsm_arr)){
						$inner_mul+=$query_vsm[$query_vsm_keys[$i]]*$vsm_arr[$query_vsm_keys[$i]];
					}
				}
				//��һ�ּ����ڻ�����
//				$vsm_inter=array_intersect_key($vsm_arr,$query_vsm);
//				$query_inter=array_intersect_key($query_vsm,$vsm_arr);
//				$inner_mul=self::arr_inner_mul($vsm_inter,$query_inter);
				
				$mod_mul=$query_mod*$vsm_mod;	//ģ���
				$sim[$files_name[$i]]=$inner_mul/$mod_mul;	//���ƶ�
			}
			//�����ƶ�д���ļ�
			$sim_key_arr=array_keys($sim);
			$sim_keys_str=implode(',',$sim_key_arr);
			$sim_str=implode(',',$sim);
			$fp=fopen(ROOT_PATH.'/sim/1.txt','w');	//��һ�β�ѯ�����ƶ�
			fwrite($fp,$sim_keys_str.'|'.$sim_str);
			fclose($fp);
			return $sim;
		
			
		}
		
		//��������������ڻ�
		static public function arr_inner_mul($arr1,$arr2){
			call_mul($m,$n);
			$arr_mul=array_map('call_mul',$arr1,$arr2);
			$inner_mul=array_sum($arr_mul);
			return $inner_mul;
		}
		
		//�������ռ��ģ
		static public function mod($vsm_arr){
			call_mod($n);
			$vsm_squ=array_map('call_mod',$vsm_arr);	//��ÿ��ֵ��ƽ��
			$vsm_sum=array_sum($vsm_squ);	//������������ֵ�ĺ�
			$vsm_mod=sqrt($vsm_sum);		//��ģ
			
			return $vsm_mod;
		}
		
		//��ѯ�ʵ�������������
		static public function query_vsm($query){
			$query_seg=self::segment($query);
			$dic_str=file_get_contents('dic.txt');
			$dic_arr=explode(',',$dic_str);
			$vsm_arr=array();	//�����ռ�
			
			for($i=0;$i<count($query_seg);$i++){
				if(in_array($query_seg[$i]['word'],$dic_arr)){
					$vsm_arr[($query_seg[$i]['word'])]=1;
				}
				//��vsmд���ļ�
				$vsm_values=implode(',',$vsm_arr);
				$vsm_keys=array_keys($vsm_arr);
				$vsm_keys=implode(',',$vsm_keys);
				$fp=fopen(ROOT_PATH.'/query_vsm/1.txt','w');
				fwrite($fp,$vsm_keys.'|'.$vsm_values);
				fclose($fp);
			}
			return $vsm_arr;
		}
		
		//����tf\idf
		static public function tf_df($seg){
			$str=self::fileStr();
			$file_dir=self::fileDir(TEXT_PATH);
			$df=array();
			for($i=0;$i<count($seg);$i++){
				for($j=0;$j<count($seg[$i]);$j++){	
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
			return $seg;
		}
		
		//��ȡ���洢�ʱ�
		static public function dic($seg){
			$dic=array();	//�ʱ�
			for($i=0;$i<count($seg);$i++){
				for($j=0;$j<count($seg[$i]);$j++){
					array_push($dic,$seg[$i][$j]['word']);	
				}
			}
			
			$dic=array_unique($dic);
			sort($dic);
			$dic=implode(',',$dic);
			
			//�Ѵʱ�洢����
			$fp=fopen('dic.txt','w');
			if(!$fp) exit('�ʵ��ʧ�ܣ�');
			if(!fwrite($fp,$dic)) exit('�ʵ�д��ʧ�ܣ�');
			fclose($fp);
		}	

		
		//��������,��д���ļ�(tf/idf�Լ�����seg��)
		static public function vsm($seg){
			$file_dir=self::fileDir(TEXT_PATH);
			$dic_str=file_get_contents('dic.txt');
			$dic_arr=explode(',',$dic_str);
			
			$vsm_arr=array();	//�����ռ�
			
			for($i=0;$i<count($seg);$i++){
				for($j=0;$j<count($seg[$i]);$j++){
					if(in_array($seg[$i][$j]['word'],$dic_arr)){
						$vsm_arr[($seg[$i][$j]['word'])]=$seg[$i][$j]['tf']*$seg[$i][$j]['idf'];
					}
				}
				//��vsmд���ļ�
				$vsm_values=implode(',',$vsm_arr);
				$vsm_keys=array_keys($vsm_arr);
				$vsm_keys=implode(',',$vsm_keys);
				$fp=fopen(ROOT_PATH.'/vsm/'.$file_dir[$i],'w');
				fwrite($fp,$vsm_keys.'|'.$vsm_values);
				fclose($fp);
				$vsm_arr=null;	//���
			}
		}
		
		
	}


?>