$(function(){

	$('#input .button').click(function(){
		var query=$('#input .text').val();
		if(query!=''){
			window.location.href='index.php?query='+query;
		}
		
	});
	$('#input .text').keyup(function(e){
		var e=e||event;
		e.stopPropagation();
		e.preventDefault();
		if(e.keyCode==13){
			var query=$('#input .text').val();
			if(query!=''){
				window.location.href='index.php?query='+query;
			}
		}
	});
});