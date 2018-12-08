$(document).ready(function () {
  
    $("#edit").on('click',function () {
        
        var id = $("#id").val();
        var notice = $("#notice").val();
        if(!notice){
            toastr.warning('请填写完整信息');
        }else{
            $.ajax({
                type: 'POST',
                url:'edit_do',
                data: {
                    id:id
                    notice:notice
                },
                dataType: 'json',
                success: function (data) {
                    // console.log(data);
                    if(data == 'success'){
                        
                        toastr.success('修改成功！');
                    }else{
                        toastr.error('修改失败');
                        return false;
                    }
                }
            });
        }
    });
});