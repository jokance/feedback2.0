<?php
	//������������˻�
	function call_mul($m,$n){	//�ص�����
		return $m*$n;
	}
	
	//����ƽ��
	function call_mod($n){	//�ص�����
		return $n*$n;
	}
	
	//����������ֵΪ0��Ԫ��
	function filter_zero($n){
		return $n!=0;

	}
	
	function filter_number($n){
		return intval($n)==0&&!is_int($n);
	}
?>