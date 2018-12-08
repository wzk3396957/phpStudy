$(document).ready(function () {
     $("#add_detail").on('click',function () {
            
            var id = $('#id').val();
            var strat = $("#strat").val();
            var over_km = $("#over_km").val();
            var how_much_km = $("#how_much_km").val();
            var area = $("#area").val();
           
            if(!strat || !over_km || !how_much_km || !area ){
                toastr.warning('请填写完整信息');
            }else{
                $.ajax({
                    type: 'POST',
                    url:'do_edit',
                    data: {
                        id:id,
                        strat:strat,
                        over_km:over_km,
                        how_much_km:how_much_km,
                        area:area
                    },
                    dataType: 'json',
                    success: function (data) {
                        // console.log(data);
                        if(data == '1'){
                            toastr.success('修改成功!请等待审核');
                            window.setTimeout("window.location='https://msd.suyongw.com/Km/showList'",1000);
                        }else{
                            toastr.error('修改失败');
                            return false;
                        }
                    }
                });
            }
        });
});