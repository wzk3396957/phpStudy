$(document).ready(function () {
     $("#add_detail").on('click',function () {
            
            var id = $('#id').val();
            var money = $("#money").val();
            var integral = $("#integral").val();
            
           
            if(!money || !integral){
                toastr.warning('请填写完整信息');
            }else{
                $.ajax({
                    type: 'POST',
                    url:'do_edit',
                    data: {
                        id:id,
                        money:money,
                        integral:integral
                    },
                    dataType: 'json',
                    success: function (data) {
                        // console.log(data);
                        if(data == '1'){
                            toastr.success('修改成功!');
                            window.setTimeout("window.location='https://msd.suyongw.com/Integral/showList'",1000);
                        }else{
                            toastr.error('修改失败');
                            return false;
                        }
                    }
                });
            }
        });
});