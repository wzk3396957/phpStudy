$(document).ready(function () {
     $("#add_detail").on('click',function () {
           
            var strat = $("#strat").val();
            var over_km = $("#over_km").val();
            var how_much_km = $("#how_much_km").val();
            var area = $("select[name='area']").val();
           
            if(!strat || !over_km || !how_much_km || !area ){
                toastr.warning('请填写完整信息');
            }else{
                $.ajax({
                    type: 'POST',
                    url:'do_set',
                    data: {
                        strat:strat,
                        over_km:over_km,
                        how_much_km:how_much_km,
                        area:area
                    },
                    dataType: 'json',
                    success: function (data) {
                        // console.log(data);
                        if(data == '1'){
                            toastr.success('设置成功!请等待审核');
                            window.setTimeout("window.location='https://msd.suyongw.com/Km/showList'",1000);
                        }else if(data == '2'){
                            toastr.error('已设置该区域');
                            return false;
                        }else{
                            toastr.error('设置失败');
                            return false;
                        }
                    }
                });
            }
        });
});