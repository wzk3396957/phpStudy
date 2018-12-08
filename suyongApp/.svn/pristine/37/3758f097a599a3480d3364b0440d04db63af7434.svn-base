$(document).ready(function () {
  
    $("#add_detail").on('click',function () {
        
        var id = $("#id").val();
        var up = $("#up").val();
        var upup = $("#upup").val();
        if(!up || !upup){
            toastr.warning('请填写完整信息');
        }else{
            $.ajax({
                type: 'POST',
                url:'edit_order?table=1',
                data: {
                    id:id,
                    up:up,
                    upup:upup
                },
                dataType: 'json',
                success: function (data) {
                    // console.log(data);
                    if(data == '1'){
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