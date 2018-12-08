$(document).ready(function () {
    init();
    //编辑器
    var editor = UE.getEditor('container',{
        //这里可以选择自己需要的工具按钮名称,此处仅选择如下五个
        toolbars:[['lineheight ','paragraph ','fontsize','indent','justifyleft','justifycenter','justifyright','justifyjustify','','Undo', 'Redo','bold','test','simpleupload']],
        //focus时自动清空初始化时的内容
        autoClearinitialContent:false,
        //关闭字数统计
        wordCount:false,
        //关闭elementPath
        elementPathEnabled:false,
        //默认的编辑区域高度
        initialFrameHeight:300,
        initialFrameWidth:700
        //更多其他参数，请参考ueditor.config.js中的配置项
    });

    //单图上传例子
    $.get('/Index/get_bcakground_image', function (img) {
        _shu_SetLogo({
            id: '#up_one_image',   //绑定的html选择器
            img: img,        //显示已有图片
            success: function (key) {
                $.ajax({
                    type: 'post',
                    url:'upload_bcakground_image',
                    data: {
                        key: key
                    },
                    dataType: 'json',
                    success: function (e) {
                        if (e == 'error') {
                            toastr.error('保存失败!', '请联系程序猿修复 >_<!');
                        }
                    }
                });
                return true;
            }
        });
    });
});

function init(){

    //多图上传例子
    $.get('/Index/get_images', function (e) {
        //限制上传的数量、1张则写0、2张则写1
        _shu_Uploader({
            id: '#up_images',
            img: e ? e : {}, // 第一次加载时通过Ajax初始化 格式指定 [{src: '', key: ''}, {src: '', key: ''}] JSON
            option: {
                label: '选择图片', // 模态框中新增图片按钮名称
                accept: {
                    title: 'Images', // 文件类型
                    extensions: 'gif,jpg,jpeg,png,mp4', // 允许的后缀名称
                    mimeTypes: 'image/gif,image/jpg,image/jpeg,image/png,'  // 允许的资源类型
                },
                fileNumLimit: 9 - ($('.singleImg').size() - 1), // 文件数量 $('.singleImg').size()-1;
                fileSizeLimit: 50 * 1024 * 1024,  // 总文件大小
                fileSingleSizeLimit: 5 * 1024 * 1024 // 单文件大小
            },
            success: function (keys) {  // 全部图片上传完成方法 keys = array(key,key,key····) 一组图片的key : 未拼接
                var res = JSON.stringify(keys);  //一维数组
                $.ajax({
                    type: "POST",
                    url: "/Index/upload_more_image",
                    data: {key: res},// 你的formid
                    dataType: 'json',
                    success: function (data) {
                        if (data == 'success') {
                            toastr.success('上传成功');
                        } else {
                            toastr.error('不得超过5张图片');
                            return false;
                        }
                    }
                });
                return true;  // 写入成功后返回 渲染前端
                // 保存写库 Ajax key = array
            },
            delete: function (key) { // 单图的删除方法
                var res = JSON.stringify(key);
                $.ajax({
                    type: "POST",
                    url: "/Index/del_more_image",
                    data: {key: key},// 你的formid
                    success: function (data) {
                        if (data == 'success') {
                            toastr.success('删除成功');
                        } else {
                            toastr.error('发生异常');
                        }

                    }
                });
                return true;
                // 删除单图 Ajax key = string
            }
        }); // 实例化 uploaderModel
    }, 'json');

}