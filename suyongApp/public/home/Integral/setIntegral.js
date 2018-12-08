$(document).ready(function () {
     $("#add_detail").on('click',function () {
           
            var money = $("#money").val();
            var integral = $("#integral").val();
            
           
            if(!money || !integral){
                toastr.warning('请填写完整信息');
            }else{
                $.ajax({
                    type: 'POST',
                    url:'do_set',
                    data: {
                        money:money,
                        integral:integral
                    },
                    dataType: 'json',
                    success: function (data) {
                        // console.log(data);
                        if(data == '1'){
                            toastr.success('设置成功!');
                            window.setTimeout("window.location='https://msd.suyongw.com/Integral/showList'",1000);
                        }else{
                            toastr.error('设置失败');
                            return false;
                        }
                    }
                });
            }
        });
});