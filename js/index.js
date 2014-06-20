$(function(){
	//µã»÷¼ìË÷
	$('#input .button').click(function(){
		query();
	});
	//°´enter¼ü¼ìË÷
	$('#input .text').keyup(function(e){
		var e=e||event;
		e.preventDefault();
		if(e.keyCode==13){
			query();
		}
	});
	
});
function add(that){
	var keys=$('.other .text').val();
	var text=$(that).html()+' ';
	$('.other .text').val(keys+text);
	return false;
}


function query(){
	var query=$('#input .text').val();
	if(query==''){
		return false;
	}else{
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
	}
}
