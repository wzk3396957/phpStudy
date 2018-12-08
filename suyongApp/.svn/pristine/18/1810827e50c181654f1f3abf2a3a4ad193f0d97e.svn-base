 $(document).ready(function () {
    $(".del").on('click',function(){
            var msg = "您真的确定要删除吗？\n\n请确认！"; 
            if (confirm(msg)==true){
                var id = $(this).attr('data');
                $.ajax({
                                type: "POST",
                                url: "del",
                                data: {id: id},// 你的formid
                                success: function (data) {
                                    if (data == '1') {
                                        toastr.success('删除成功');
                                        
                                         $("a[data = "+id+"]").parent().parent().remove();
                                    } else {
                                        toastr.error('发生异常');
                                    }

                                }
                            });
            }else{
                return false;
            }
        });
});