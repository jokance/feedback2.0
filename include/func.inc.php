<?php
	//计算两个数组乘积
	function call_mul($m,$n){	//回调函数
		return $m*$n;
	}
	
	//计算平方
	function call_mod($n){	//回调函数
		return $n*$n;
	}
	
	//过滤数组中值为0的元素
	function filter_zero($n){
		return $n!=0;

	}
	
	function filter_number($n){
		return intval($n)==0&&!is_int($n);
	}
?>