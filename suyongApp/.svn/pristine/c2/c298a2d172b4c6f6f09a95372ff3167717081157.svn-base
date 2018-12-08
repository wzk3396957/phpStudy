$(document).ready(function(){
		$(".check").on('click',function(){
			 var id = $(this).attr('data_id');
			 var state = $(this).attr('data_state');
				$.ajax({
			                    type: "POST",
			                    url: "check",
			                    data: {id: id,state:state},// 你的formid
			                    success: function (data) {
			                        if (data == '1') {
			                            toastr.success('成功');
			                            $("a[data_id = "+id+"]").parent().parent().remove();
			                             // window.setTimeout("window.location='showList'",1000);
			                        } else {
			                            toastr.error('发生异常');
			                        }

			                    }
			                });
		});
});