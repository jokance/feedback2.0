<?php
	class DB{
		//�������ݿ�
		static public function getDB(){
			$db=new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
			if(mysqli_connect_errno()){
				echo '���ݿ����Ӵ���'.mysqli_connect_error();
				exit();
			}
			$db->set_charset('gbk');
			return $db;
		}

	
		//���ݿ�����
		static public function unDB(&$_result, &$_db){
			if (is_object($_result)) {
				$_result->free();
				$_result = null;
			}
			if (is_object($_db)) {
				$_db->close();
				$_db = null;
			}
		}
		
		//�������ݿ�
		static public function insertDB($top,$file_name,$text_len,$db){
			for($i=0;$i<count($top);$i++){
				$words_arr[]=$top[$i]['word'];
				$times_arr[]=$top[$i]['times'];
				//$attrs_arr[]=$top[$i]['attr'];
				//$weight_arr[]=$top[$i]['weight'];		
			}
//			if(!get_magic_quotes_gpc()){
//				$file_name=addslashes($file_name);
//				$words_str=addslashes(implode(',',$words_arr));
//				$times_str=addslashes(implode(',',$times_arr));
//				$attrs_str=addslashes(implode(',',$attrs_arr));
//			}else{
			$words_str=implode(',',$words_arr);
			$times_str=implode(',',$times_arr);
			//$attrs_str=implode(',',$attrs_arr);
			//$weight_str=implode(',',$weight_arr);
		
			$db->query("INSERT INTO news
								(
									file_name,
									words_str,
									times_str,
									text_len
								)
							VALUES
								(
									'$file_name',
									'$words_str',
									'$times_str',
									'$text_len'
								)
			");
			
			if($db->affected_rows){
				echo '���ݲ���ɹ�';
			}else{
				exit('���ݲ���ʧ��');
			}

		}
		

	}
?>