<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript">
<?php 
		if(isset($_GET['query'])&&$_GET['query']!=''){
			$query=$_GET['query'];
		
?>
	$(function(){
		var query="<?php echo $query;?>";
		$.ajax({
			type:'POST',
			url:'main.php',
			data:{
				'search':query
			},
			success:function(data){
				$('#output').html(data);
			}
		});

	});

	
<?php }?>
</script>

	<div id="page">
		<?php 
			$keys_arr=explode(',',file_get_contents('results/keys.txt'));
			$values_arr=explode(',',file_get_contents('results/values.txt'));
			if(count($keys_arr)!=0){
				echo '��ҳ';
			}
		?>
	</div>
	
	
				<?php 
			if(isset($keys)&&$keys!=null){
				for($i=0;$i<PAGE_SIZE;$i++){		
					$str=Tool::fileStr($keys[$i]);
					$str=preg_replace('/ |	|��|	/','',$str);	//ȥ������ȫ�ǰ�ǿո�
					$title=Tool::strLimit($str,30);
					$desc=Tool::strLimit($str,200);
					$title=str_replace($highLight[0],$highLight[1],$title);
					$desc=str_replace($highLight[0],$highLight[1],$desc);
			?>
				<table><tr><td class="title"><a target="_blank" href="news.php?filename=<?php echo $keys[$i]?>&query=<?php echo $query;?>"><?php echo $title;?></a></td></tr><tr><td class="desc"><?php echo $desc?></td></tr></table>	
			<?php }}?>
			
			
			
			
				if($_GET['action']=='search'){
		if(isset($_POST['query'])&&$_POST['query']!=''){
//			$query=iconv('UTF-8','GBK',$_POST['search']);
			$query=$_POST['query'];
			$highLight=Tool::highLight($query);
			
			$sim=Tool::sim($query);	//���ƶ�
			$text_count=count($sim);	//�����ĵ�����
			arsort($sim);
			$sim=array_filter($sim,'filter_zero');//�������ƶ�Ϊ0��ֵ
	
			$sim_count=count($sim);	//����ĵ���
			$keys=array_keys($sim);
			$values=array_values($sim);
			$keys_str=implode(',',$keys);
			$values_str=implode(',',$values);
			
			$fp=fopen('results/keys.txt','w');
			if(!$fp) exit('keys.txt��ʧ��');
			if(!fwrite($fp,$keys_str)) exit('keys.txt�ļ�д��ʧ�ܣ�');
			fclose($fp);
			$fp=fopen('results/values.txt','w');
			if(!$fp) exit('values.txt��ʧ��');
			if(!fwrite($fp,$values_str)) exit('values.txt�ļ�д��ʧ�ܣ�');
			fclose($fp);
		}
	}
