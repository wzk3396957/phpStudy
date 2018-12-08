var _Model = (function (windows) {
    var ShuStudio = function (name) {
        return new ShuStudio.fn.init(name)
    };

    ShuStudio.fn = ShuStudio.prototype = {
        constructor: ShuStudio,
        init: function (e) {
            this.id = e.id;
            this.title = e.title;
            this.body = e.body;
            this.open = e.open;
            this.way = e.way ? e.way : 'bounceIn';   //bounceIn   bounceInDown   bounceInUp   bounceInLeft   bounceInRight

            var html = '<div class="modal inmodal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">' +
                '    <div class="modal-dialog">' +
                '        <div class="modal-content animated ' + this.way + '" style="margin-top: 25%;width:700px" >' +
                '            <div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
                '                <span style="font-size: 15px;font-weight:bold" class="modal-title">' + this.title + '</span>' +
                '            </div>' +
                '            <div class="modal-body" style="height:500px; overflow-y:scroll;">' +
                this.body +
                '            </div>' +
                '            <div class="modal-footer">' +

                '            </div>' +
                '        </div>' +
                '    </div>' +
                '</div>';
            if (this.open) {
                $('#myModal').remove();
                $('body').append(html);
                $('#myModal').modal('show');
            } else {
                $(this.id).on('click', function () {
                    $('#myModal').remove();
                    $('body').append(html);
                    $('#myModal').modal('show');

                })
            }
        }
    };
    ShuStudio.fn.init.prototype = ShuStudio.fn;
    return ShuStudio;
})();

var _shu_SetLogo = (function (windows) {

    var ShuStudio = function (name) {
        return new ShuStudio.fn.init(name);
    };

    var Token = null;  // 上传 Token
    if ($.cookie('upload_token')) {
        Token = $.cookie('upload_token');
    } else {
        $.ajax({
            type: 'get',
            url: '/Base/qiniu_get_token',
            async: false,
            success: function (e) {
                if (e) {
                    Token = e;
                } else {
                    toastr.warning('请联系程序猿修复 >_<', '上传组件初始化失败');
                }
            },
            error: function () {
                toastr.warning('请联系程序猿修复 >_<', '上传组件发生严重错误');
            }
        });
    }

    ShuStudio.fn = ShuStudio.prototype = {
        constructor: ShuStudio,
        init: function (e) {
            var img = e.img ? 'https://res.suyongw.com/' + e.img : '';
            this.success = e.success;
            this.makeHtml(e.id, img, e.option);  // Load Html Code
            this.sizeControl = e.sizeControl;
        },
        makeHtml: function (id, img, option) {
            var H = img ? '<div class="singleImg">' +
                '<img src="' + img + '"></div>' :
                '<div class="singleImg" id="#add"><i class="fa fa-plus-square fa-3x" style="margin-left: 37%;margin-top: 37%;"></i></div>';
            $(id).html(H);
            this.onAddIMG(id, img, this.success); // 打开监听
        },
        onAddIMG: function (id, img, success) {
            $(id).on('click', '.singleImg', function () {
                $('#setLogoModal').remove();
                $('body').append('<div class="modal fade" id="setLogoModal">' +
                    '<div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" ' +
                    'aria-hidden="true">&times;</button><h4 class="modal-title">设置公司LOGO</h4></div><div class="modal-body">' +
                    '<!-- Webuploader HTML --></div></div></div></div>');

                $('#setLogoModal').find('.modal-body').html('<div class="row"><div class="col-md-6"><div class="image-crop">' +
                    '<img src="' + img + '"></div></div><div class="col-md-6"><h4>添加图片</h4><div class="img-preview img-preview-sm">' +
                    '</div><h4>请选择 - <small>png、jpg、jpeg、gif格式图片</small>.</h4>' +
                    '<div class="btn-group"><label title="选择上传图片" for="inputImage" class="btn btn-primary">' +
                    '<input type="file" accept="image/png|image/jpg|image/jpeg|image/gif" name="file" id="inputImage" class="hide">选取图片</label>' +
                    '<label title="Save image" id="download" class="btn btn-success" disabled="disabled">保存</label></div></div></div>');

                $('#setLogoModal').modal('show');
                if (img == null) {
                    $('#setLogoModal').find(".image-crop > img").css('display', 'none');
                }
                setTimeout(function () {
                    var $image = $('#setLogoModal').find(".image-crop > img");
                    $($image).cropper({
                        aspectRatio: self.sizeControl ? self.sizeControl : 'NaN',
                        preview: ".img-preview"
                    });

                    var $inputImage = $("#inputImage");

                    if (window.FileReader) {
                        $inputImage.change(function () {
                            $("#download").removeAttr('disabled');
                            var fileReader = new FileReader(),
                                files = this.files,
                                file;
                            if (!files.length) {
                                return false;
                            }
                            file = files[0];
                            if (/^image\/\w+$/.test(file.type)) {
                                fileReader.readAsDataURL(file);
                                fileReader.onload = function () {
                                    $inputImage.val("");
                                    $image.cropper("reset", true).cropper("replace", this.result);
                                };
                            } else {
                                toastr.warning("请选择图片文件");
                            }
                        });
                    } else {
                        $inputImage.addClass("hide");
                    }

                    var key = '';

                    $("#download").click(function () {
                        if (!$(this).attr('disabled')) {
                            $(this).attr('disabled', 'disabled');
                            $(this).html('上传中 <i class="fa fa-spinner fa-spin"></i>');
                            if (Token) {
                                var url = "https://upload-z2.qbox.me/putb64/-1"; //非华东空间需要根据注意事项 1 修改上传域名
                                var xhr = new XMLHttpRequest();
                                xhr.open("POST", url, true);
                                xhr.setRequestHeader("Content-Type", "application/octet-stream");
                                xhr.setRequestHeader("Authorization", "UpToken " + Token);
                                xhr.send($image.cropper("getDataURL").split('base64,')[1]);
                                xhr.onreadystatechange = function () {
                                    if (xhr.readyState == 4) {
                                        key = JSON.parse(xhr.responseText).key;
                                        if (success(key)) {
                                            toastr.success('设置成功!');
                                            $(id).html('<div class="singleImg"><img src="' + 'https://res.suyongw.com/' + key + '"></div>');
                                            $('#setCompany_logo').html('<div class="singleImg"><img src="' + 'https://res.suyongw.com/' + key + '"></div>');
                                            $('#setLogoModal').modal('hide');
                                        }
                                    }
                                };
                            }
                        }
                    });
                }, 200);
            });
        }
    };

    ShuStudio.fn.init.prototype = ShuStudio.fn;
    return ShuStudio;

})();

var _shu_Uploader = (function (windows) {
    var ShuStudio = function (name) {
        return new ShuStudio.fn.init(name);
    };
    var Token = null;  // 上传 Token
    if ($.cookie('token')) {
        Token = $.cookie('token');
    } else {
        $.ajax({
            type: 'get',
            url: '/Base/qiniu_get_token',
            async: false,
            success: function (e) {
                if (!e) {
                    toastr.warning('请联系程序猿修复 >_<', '上传组件初始化失败');
                }
                Token = e;
            },
            error: function () {
                toastr.warning('请联系程序猿修复 >_<', '上传组件发生严重错误');
            }
        });
    }

    ShuStudio.fn = ShuStudio.prototype = {
        constructor: ShuStudio,
        init: function (e) {
            this.success = e.success;
            this.delete = e.delete;
            this.makeHtml(e.id, e.img, e.option);  // Load Html Code
        },
        makeHtml: function (id, img, option) { // 构造页面元素 id = uploader_DIV img = src , key
            var H = ''; // 初始化图片组前端
            for (var i = 0; i < img.length; i++) {
                H += '<div class="singleImg"><span class="fa-stack" data-key="' + img[i].key + '" style="position: absolute;' +
                    'margin-left: 10em"><i class="fa fa-circle fa-stack-2x" ' +
                    'style="color: #fff;"></i><i class="fa fa-close fa-stack-1x" style="color: #333;"></i></span>' +
                    '<img src="' + img[i].src + '"></div>';
            }
            H += '<div class="singleImg" id="add"><i class="fa fa-plus-square fa-3x"></i><span>添加图片</span></div>';
            $(id).html(H);
            var deleteIMG = this.delete;
            $(id).on('click', 'span.fa-stack', function () {
                if (deleteIMG($(this).attr('data-key'))) {
                    $(this).parent('div.singleImg').fadeOut();
                }
            });
            this.onAddIMG(id, option, this.success); // 打开监听
        },
        onAddIMG: function (id, option, del_FUN) {
            var onStart = this.onStartModal;
            $(id).find('#add').on('click', function () {
                $('#uploaderModal').remove();
                $('body').append('<div class="modal fade" id="uploaderModal">' +
                    '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" ' +
                    'aria-hidden="true">&times;</button><h4 class="modal-title"><!-- 模态框（Modal）标题 --></h4></div><div class="modal-body">' +
                    '<!-- Webuploader HTML --></div></div></div></div>');
                $('#uploaderModal').find('.modal-title').html('上传图片 - <small class="text-danger">请选择png、jpg、jpeg、gif格式图片</small>');
                $('#uploaderModal').find('.modal-body').html('<div id="uploader" class="wu-example"><div class="queueList"><div id="dndArea" class="placeholder">' +
                    '<div id="filePicker"></div><p>点击添加图片或将照片拖到这里</p></div></div><div class="statusBar" style="display:none;">' +
                    '<div class="progress"><span class="text">0%</span><span class="percentage"></span></div><div class="info"></div><div class="btns">' +
                    '<div id="filePicker2"></div><div class="uploadBtn">开始上传</div></div></div></div>');
                $('#uploaderModal').modal('show');
                setTimeout(function () {
                    onStart(id, {
                        pick: {
                            id: '#filePicker',
                            label: option.label
                        },
                        dnd: '#uploader .queueList',
                        paste: document.body,
                        accept: option.accept,
                        disableGlobalDnd: true,
                        chunked: false,
                        compress: false,
                        server: 'https://upload-z2.qbox.me',
                        fileNumLimit: option.fileNumLimit,
                        fileSizeLimit: option.fileSizeLimit,
                        fileSingleSizeLimit: option.fileSingleSizeLimit,
                        formData: {
                            token: Token
                        }
                    }, del_FUN);
                }, 200);
            })
        },
        onStartModal: function (id, option, del_FUN) {
            var uploadSuccess = del_FUN;
            var images = []; // 上传元素
            var $ = jQuery,    // just in case. Make sure it's not an other libaray.
                $wrap = $('#uploader'),
                // 图片容器
                $queue = $('<ul class="filelist"></ul>')
                    .appendTo($wrap.find('.queueList')),
                // 状态栏，包括进度和控制按钮
                $statusBar = $wrap.find('.statusBar'),
                // 文件总体选择信息。
                $info = $statusBar.find('.info'),
                // 上传按钮
                $upload = $wrap.find('.uploadBtn'),
                // 没选择文件之前的内容。
                $placeHolder = $wrap.find('.placeholder'),
                // 总体进度条
                $progress = $statusBar.find('.progress').hide(),
                // 添加的文件数量
                fileCount = 0,
                // 添加的文件总大小
                fileSize = 0,
                // 优化retina, 在retina下这个值是2
                ratio = window.devicePixelRatio || 1,
                // 缩略图大小
                thumbnailWidth = 110 * ratio,
                thumbnailHeight = 110 * ratio,
                // 可能有pedding, ready, uploading, confirm, done.
                state = 'pedding',
                // 所有文件的进度信息，key为file id
                percentages = {},
                supportTransition = (function () {
                    var s = document.createElement('p').style,
                        r = 'transition' in s ||
                            'WebkitTransition' in s ||
                            'MozTransition' in s ||
                            'msTransition' in s ||
                            'OTransition' in s;
                    s = null;
                    return r;
                })(),
                // WebUploader实例
                uploader;
            if (!WebUploader.Uploader.support()) {
                alert('Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
                throw new Error('WebUploader does not support the browser you are using.');
            }
            // 实例化
            uploader = WebUploader.create(option);

            // 添加“添加文件”的按钮，
            uploader.addButton({
                id: '#filePicker2',
                label: '继续添加'
            });

            // 当有文件添加进来时执行，负责view的创建
            function addFile(file) {
                var $li = $('<li id="' + file.id + '">' +
                    '<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>' +
                    '<p class="progress"><span></span></p>' +
                    '</li>'),

                    $btns = $('<div class="file-panel">' +
                        '<span class="cancel">删除</span>' +
                        '<span class="rotateRight">向右旋转</span>' +
                        '<span class="rotateLeft">向左旋转</span></div>').appendTo($li),
                    $prgress = $li.find('p.progress span'),
                    $wrap = $li.find('p.imgWrap'),
                    $info = $('<p class="error"></p>'),

                    showError = function (code) {
                        switch (code) {
                            case 'exceed_size':
                                text = '文件大小超出';
                                break;

                            case 'interrupt':
                                text = '上传暂停';
                                break;

                            default:
                                text = '上传失败，请重试';
                                break;
                        }

                        $info.text(text).appendTo($li);
                    };

                if (file.getStatus() === 'invalid') {
                    showError(file.statusText);
                } else {
                    // @todo lazyload
                    $wrap.text('预览中');
                    uploader.makeThumb(file, function (error, src) {
                        if (error) {
                            $wrap.text('不能预览');
                            return;
                        }

                        var img = $('<img src="' + src + '">');
                        $wrap.empty().append(img);
                    }, thumbnailWidth, thumbnailHeight);

                    percentages[file.id] = [file.size, 0];
                    file.rotation = 0;
                }

                file.on('statuschange', function (cur, prev) {
                    if (prev === 'progress') {
                        $prgress.hide().width(0);
                    } else if (prev === 'queued') {
                        $li.off('mouseenter mouseleave');
                        $btns.remove();
                    }

                    // 成功
                    if (cur === 'error' || cur === 'invalid') {
                        console.log(file.statusText);
                        showError(file.statusText);
                        percentages[file.id][1] = 1;
                    } else if (cur === 'interrupt') {
                        showError('interrupt');
                    } else if (cur === 'queued') {
                        percentages[file.id][1] = 0;
                    } else if (cur === 'progress') {
                        $info.remove();
                        $prgress.css('display', 'block');
                    } else if (cur === 'complete') {
                        $li.append('<span class="success"></span>');
                    }

                    $li.removeClass('state-' + prev).addClass('state-' + cur);
                });

                $li.on('mouseenter', function () {
                    $btns.stop().animate({height: 30});
                });

                $li.on('mouseleave', function () {
                    $btns.stop().animate({height: 0});
                });

                $btns.on('click', 'span', function () {
                    var index = $(this).index(),
                        deg;

                    switch (index) {
                        case 0:
                            uploader.removeFile(file);
                            return;

                        case 1:
                            file.rotation += 90;
                            break;

                        case 2:
                            file.rotation -= 90;
                            break;
                    }

                    if (supportTransition) {
                        deg = 'rotate(' + file.rotation + 'deg)';
                        $wrap.css({
                            '-webkit-transform': deg,
                            '-mos-transform': deg,
                            '-o-transform': deg,
                            'transform': deg
                        });
                    } else {
                        $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');

                    }


                });

                $li.appendTo($queue);

            }

            // 负责view的销毁
            function removeFile(file) {
                var $li = $('#' + file.id);

                delete percentages[file.id];
                updateTotalProgress();
                $li.off().find('.file-panel').off().end().remove();
            }

            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;

                $.each(percentages, function (k, v) {
                    total += v[0];
                    loaded += v[0] * v[1];
                });

                percent = total ? loaded / total : 0;

                spans.eq(0).text(Math.round(percent * 100) + '%');
                spans.eq(1).css('width', Math.round(percent * 100) + '%');
                updateStatus();
            }

            function updateStatus() {
                var text = '', stats;

                if (state === 'ready') {
                    text = '选中' + fileCount + '张图片，共' +
                        WebUploader.formatSize(fileSize) + '。';
                } else if (state === 'confirm') {
                    stats = uploader.getStats();
                    if (stats.uploadFailNum) {
                        text = '已成功上传' + stats.successNum + '张照片至，' +
                            stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                    }

                } else {
                    stats = uploader.getStats();
                    text = '共' + fileCount + '张（' +
                        WebUploader.formatSize(fileSize) +
                        '），已上传' + stats.successNum + '张';

                    if (stats.uploadFailNum) {
                        text += '，失败' + stats.uploadFailNum + '张';
                    }
                }

                $info.html(text);
            }

            function setState(val) {
                var file, stats;

                if (val === state) {
                    return;
                }

                $upload.removeClass('state-' + state);
                $upload.addClass('state-' + val);
                state = val;

                switch (state) {
                    case 'pedding':
                        $placeHolder.removeClass('element-invisible');
                        $queue.parent().removeClass('filled');
                        $queue.hide();
                        $statusBar.addClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'ready':
                        $placeHolder.addClass('element-invisible');
                        $('#filePicker2').removeClass('element-invisible');
                        $queue.parent().addClass('filled');
                        $queue.show();
                        $statusBar.removeClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'uploading':
                        $('#filePicker2').addClass('element-invisible');
                        $progress.show();
                        $upload.text('暂停上传');
                        break;

                    case 'paused':
                        $progress.show();
                        $upload.text('继续上传');
                        break;

                    case 'confirm':
                        $progress.hide();
                        $upload.text('开始上传').addClass('disabled');

                        stats = uploader.getStats();
                        if (stats.successNum && !stats.uploadFailNum) {
                            setState('finish');
                            return;
                        }
                        break;
                    case 'finish':
                        stats = uploader.getStats();
                        if (stats.successNum) {
                            if (uploadSuccess(images)) {  // 成功后回复
                                var H = '';
                                for (var i = 0; i < images.length; i++) {
                                    H += '<div class="singleImg"><span class="fa-stack" data-key="' + images[i] + '" style="position: absolute;' +
                                        'margin-left: 10em"><i class="fa fa-circle fa-stack-2x" ' +
                                        'style="color: #fff;"></i><i class="fa fa-close fa-stack-1x" style="color: #333;"></i></span>' +
                                        '<img src="https://res.suyongw.com/' + images[i] + '"></div>';
                                }
                                $(id).find('div#add').before(H);
                                $('#uploaderModal').modal('hide');
                            }
                            images.length = 0;
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            // location.reload();
                        }
                        break;
                }

                updateStatus();
            }

            uploader.onUploadSuccess = function (file, response) {
                images.push(response.key);
                console.log('上传成功：地址 => https://res.suyongw.com/' + response.key); //这里可以得到后台返回的数据
            };

            uploader.onUploadProgress = function (file, percentage) {
                var $li = $('#' + file.id),
                    $percent = $li.find('.progress span');

                $percent.css('width', percentage * 100 + '%');
                percentages[file.id][1] = percentage;
                updateTotalProgress();
            };

            uploader.onFileQueued = function (file) {
                fileCount++;
                fileSize += file.size;

                if (fileCount === 1) {
                    $placeHolder.addClass('element-invisible');
                    $statusBar.show();
                }

                addFile(file);
                setState('ready');
                updateTotalProgress();
            };

            uploader.onFileDequeued = function (file) {
                fileCount--;
                fileSize -= file.size;

                if (!fileCount) {
                    setState('pedding');
                }

                removeFile(file);
                updateTotalProgress();

            };

            uploader.on('all', function (type) {
                var stats;
                switch (type) {
                    case 'uploadFinished':
                        setState('confirm');
                        break;

                    case 'startUpload':
                        setState('uploading');
                        break;

                    case 'stopUpload':
                        setState('paused');
                        break;

                }
            });

            uploader.onError = function (code) {
                alert('Eroor: ' + code);
            };

            $upload.on('click', function () {
                if ($(this).hasClass('disabled')) {
                    return false;
                }
                if (state === 'ready') {
                    uploader.upload();
                } else if (state === 'paused') {
                    uploader.upload();
                } else if (state === 'uploading') {
                    uploader.stop();
                }
            });

            $info.on('click', '.retry', function () {
                uploader.retry();
            });

            $info.on('click', '.ignore', function () {
                alert('todo');
            });

            $upload.addClass('state-' + state);

            updateTotalProgress();

        }

    };

    ShuStudio.fn.init.prototype = ShuStudio.fn;
    return ShuStudio;
})();


var _shu_Uploadervideo = (function (windows) {
    var ShuStudio = function (name) {
        return new ShuStudio.fn.init(name);
    };
    var Token = null;  // 上传 Token
    if ($.cookie('token')) {
        Token = $.cookie('token');
    } else {
        $.ajax({
            type: 'get',
            url: '/Base/qiniu_get_token',
            async: false,
            success: function (e) {
                if (!e) {
                    toastr.warning('请联系程序猿修复 >_<', '上传组件初始化失败');
                }
                Token = e;
            },
            error: function () {
                toastr.warning('请联系程序猿修复 >_<', '上传组件发生严重错误');
            }
        });
    }

    ShuStudio.fn = ShuStudio.prototype = {
        constructor: ShuStudio,
        init: function (e) {
            this.success = e.success;
            this.delete = e.delete;
            this.makeHtml(e.id, e.img, e.option);  // Load Html Code
        },
        makeHtml: function (id, img, option) { // 构造页面元素 id = uploader_DIV img = src , key
            var H = ''; // 初始化图片组前端
            for (var i = 0; i < img.length; i++) {
                H += '<div class="singleImg"><span class="fa-stack" data-key="' + img[i].key + '" style="position: absolute;' +
                    'margin-left: 10em"><i class="fa fa-circle fa-stack-2x" ' +
                    'style="color: #fff;"></i><i class="fa fa-close fa-stack-1x" style="color: #333;"></i></span>' +
                    '<a target= _blank href="' + img[i].src + '"><video src="' + img[i].src + '"></a></div>';
            }
            H += '<div class="singleImg" id="add"><i class="fa fa-plus-square fa-3x"></i><span>添加视频</span></div>';
            $(id).html(H);
            var deleteIMG = this.delete;
            $(id).on('click', 'span.fa-stack', function () {
                if (deleteIMG($(this).attr('data-key'))) {
                    $(this).parent('div.singleImg').fadeOut();
                }
            });
            this.onAddIMG(id, option, this.success); // 打开监听
        },
        onAddIMG: function (id, option, del_FUN) {
            var onStart = this.onStartModal;
            $(id).find('#add').on('click', function () {
                $('#uploaderModal').remove();
                $('body').append('<div class="modal fade" id="uploaderModal">' +
                    '<div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" ' +
                    'aria-hidden="true">&times;</button><h4 class="modal-title"><!-- 模态框（Modal）标题 --></h4></div><div class="modal-body">' +
                    '<!-- Webuploader HTML --></div></div></div></div>');
                $('#uploaderModal').find('.modal-title').html('上传视频 - <small class="text-danger">请选择视频</small>');
                $('#uploaderModal').find('.modal-body').html('<div id="uploader" class="wu-example"><div class="queueList"><div id="dndArea" class="placeholder">' +
                    '<div id="filePicker"></div><p>点击添加视频或将视频拖到这里</p></div></div><div class="statusBar" style="display:none;">' +
                    '<div class="progress"><span class="text">0%</span><span class="percentage"></span></div><div class="info"></div><div class="btns">' +
                    '<div id="filePicker2"></div><div class="uploadBtn">开始上传</div></div></div></div>');
                $('#uploaderModal').modal('show');
                setTimeout(function () {
                    onStart(id, {
                        pick: {
                            id: '#filePicker',
                            label: option.label
                        },
                        dnd: '#uploader .queueList',
                        paste: document.body,
                        accept: option.accept,
                        disableGlobalDnd: true,
                        chunked: false,
                        compress: false,
                        server: 'https://upload-z2.qbox.me',
                        fileNumLimit: option.fileNumLimit,
                        fileSizeLimit: option.fileSizeLimit,
                        fileSingleSizeLimit: option.fileSingleSizeLimit,
                        formData: {
                            token: Token
                        }
                    }, del_FUN);
                }, 200);
            })
        },
        onStartModal: function (id, option, del_FUN) {
            var uploadSuccess = del_FUN;
            var images = []; // 上传元素
            var $ = jQuery,    // just in case. Make sure it's not an other libaray.
                $wrap = $('#uploader'),
                // 图片容器
                $queue = $('<ul class="filelist"></ul>')
                    .appendTo($wrap.find('.queueList')),
                // 状态栏，包括进度和控制按钮
                $statusBar = $wrap.find('.statusBar'),
                // 文件总体选择信息。
                $info = $statusBar.find('.info'),
                // 上传按钮
                $upload = $wrap.find('.uploadBtn'),
                // 没选择文件之前的内容。
                $placeHolder = $wrap.find('.placeholder'),
                // 总体进度条
                $progress = $statusBar.find('.progress').hide(),
                // 添加的文件数量
                fileCount = 0,
                // 添加的文件总大小
                fileSize = 0,
                // 优化retina, 在retina下这个值是2
                ratio = window.devicePixelRatio || 1,
                // 缩略图大小
                thumbnailWidth = 110 * ratio,
                thumbnailHeight = 110 * ratio,
                // 可能有pedding, ready, uploading, confirm, done.
                state = 'pedding',
                // 所有文件的进度信息，key为file id
                percentages = {},
                supportTransition = (function () {
                    var s = document.createElement('p').style,
                        r = 'transition' in s ||
                            'WebkitTransition' in s ||
                            'MozTransition' in s ||
                            'msTransition' in s ||
                            'OTransition' in s;
                    s = null;
                    return r;
                })(),
                // WebUploader实例
                uploader;
            if (!WebUploader.Uploader.support()) {
                alert('Web Uploader 不支持您的浏览器！如果你使用的是IE浏览器，请尝试升级 flash 播放器');
                throw new Error('WebUploader does not support the browser you are using.');
            }
            // 实例化
            uploader = WebUploader.create(option);

            // 添加“添加文件”的按钮，
            uploader.addButton({
                id: '#filePicker2',
                label: '继续添加'
            });

            // 当有文件添加进来时执行，负责view的创建
            function addFile(file) {
                var $li = $('<li id="' + file.id + '">' +
                    '<p class="title">' + file.name + '</p>' +
                    '<p class="imgWrap"></p>' +
                    '<p class="progress"><span></span></p>' +
                    '</li>'),

                    $btns = $('<div class="file-panel">' +
                        '<span class="cancel">删除</span>' +
                        '<span class="rotateRight">向右旋转</span>' +
                        '<span class="rotateLeft">向左旋转</span></div>').appendTo($li),
                    $prgress = $li.find('p.progress span'),
                    $wrap = $li.find('p.imgWrap'),
                    $info = $('<p class="error"></p>'),

                    showError = function (code) {
                        switch (code) {
                            case 'exceed_size':
                                text = '文件大小超出';
                                break;

                            case 'interrupt':
                                text = '上传暂停';
                                break;

                            default:
                                text = '上传失败，请重试';
                                break;
                        }

                        $info.text(text).appendTo($li);
                    };

                if (file.getStatus() === 'invalid') {
                    showError(file.statusText);
                } else {
                    // @todo lazyload
                    $wrap.text('预览中');
                    uploader.makeThumb(file, function (error, src) {
                        if (error) {
                            $wrap.text('不能预览');
                            return;
                        }

                        var img = $('<img src="' + src + '">');
                        $wrap.empty().append(img);
                    }, thumbnailWidth, thumbnailHeight);

                    percentages[file.id] = [file.size, 0];
                    file.rotation = 0;
                }

                file.on('statuschange', function (cur, prev) {
                    if (prev === 'progress') {
                        $prgress.hide().width(0);
                    } else if (prev === 'queued') {
                        $li.off('mouseenter mouseleave');
                        $btns.remove();
                    }

                    // 成功
                    if (cur === 'error' || cur === 'invalid') {
                        console.log(file.statusText);
                        showError(file.statusText);
                        percentages[file.id][1] = 1;
                    } else if (cur === 'interrupt') {
                        showError('interrupt');
                    } else if (cur === 'queued') {
                        percentages[file.id][1] = 0;
                    } else if (cur === 'progress') {
                        $info.remove();
                        $prgress.css('display', 'block');
                    } else if (cur === 'complete') {
                        $li.append('<span class="success"></span>');
                    }

                    $li.removeClass('state-' + prev).addClass('state-' + cur);
                });

                $li.on('mouseenter', function () {
                    $btns.stop().animate({height: 30});
                });

                $li.on('mouseleave', function () {
                    $btns.stop().animate({height: 0});
                });

                $btns.on('click', 'span', function () {
                    var index = $(this).index(),
                        deg;

                    switch (index) {
                        case 0:
                            uploader.removeFile(file);
                            return;

                        case 1:
                            file.rotation += 90;
                            break;

                        case 2:
                            file.rotation -= 90;
                            break;
                    }

                    if (supportTransition) {
                        deg = 'rotate(' + file.rotation + 'deg)';
                        $wrap.css({
                            '-webkit-transform': deg,
                            '-mos-transform': deg,
                            '-o-transform': deg,
                            'transform': deg
                        });
                    } else {
                        $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');

                    }


                });

                $li.appendTo($queue);

            }

            // 负责view的销毁
            function removeFile(file) {
                var $li = $('#' + file.id);

                delete percentages[file.id];
                updateTotalProgress();
                $li.off().find('.file-panel').off().end().remove();
            }

            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;

                $.each(percentages, function (k, v) {
                    total += v[0];
                    loaded += v[0] * v[1];
                });

                percent = total ? loaded / total : 0;

                spans.eq(0).text(Math.round(percent * 100) + '%');
                spans.eq(1).css('width', Math.round(percent * 100) + '%');
                updateStatus();
            }

            function updateStatus() {
                var text = '', stats;

                if (state === 'ready') {
                    text = '选中' + fileCount + '张图片，共' +
                        WebUploader.formatSize(fileSize) + '。';
                } else if (state === 'confirm') {
                    stats = uploader.getStats();
                    if (stats.uploadFailNum) {
                        text = '已成功上传' + stats.successNum + '张照片至，' +
                            stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                    }

                } else {
                    stats = uploader.getStats();
                    text = '共' + fileCount + '张（' +
                        WebUploader.formatSize(fileSize) +
                        '），已上传' + stats.successNum + '张';

                    if (stats.uploadFailNum) {
                        text += '，失败' + stats.uploadFailNum + '张';
                    }
                }

                $info.html(text);
            }

            function setState(val) {
                var file, stats;

                if (val === state) {
                    return;
                }

                $upload.removeClass('state-' + state);
                $upload.addClass('state-' + val);
                state = val;

                switch (state) {
                    case 'pedding':
                        $placeHolder.removeClass('element-invisible');
                        $queue.parent().removeClass('filled');
                        $queue.hide();
                        $statusBar.addClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'ready':
                        $placeHolder.addClass('element-invisible');
                        $('#filePicker2').removeClass('element-invisible');
                        $queue.parent().addClass('filled');
                        $queue.show();
                        $statusBar.removeClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'uploading':
                        $('#filePicker2').addClass('element-invisible');
                        $progress.show();
                        $upload.text('暂停上传');
                        break;

                    case 'paused':
                        $progress.show();
                        $upload.text('继续上传');
                        break;

                    case 'confirm':
                        $progress.hide();
                        $upload.text('开始上传').addClass('disabled');

                        stats = uploader.getStats();
                        if (stats.successNum && !stats.uploadFailNum) {
                            setState('finish');
                            return;
                        }
                        break;
                    case 'finish':
                        stats = uploader.getStats();
                        if (stats.successNum) {
                            if (uploadSuccess(images)) {  // 成功后回复
                                var H = '';
                                for (var i = 0; i < images.length; i++) {
                                    H += '<div class="singleImg"><span class="fa-stack" data-key="' + images[i] + '" style="position: absolute;' +
                                        'margin-left: 10em"><i class="fa fa-circle fa-stack-2x" ' +
                                        'style="color: #fff;"></i><i class="fa fa-close fa-stack-1x" style="color: #333;"></i></span>' +
                                        '<a target= _blank href="https://res.suyongw.com/' + images[i] + '"><video src="https://res.suyongw.com/' + images[i] + '"></a></div>';
                                }
                                $(id).find('div#add').before(H);
                                $('#uploaderModal').modal('hide');
                            }
                            images.length = 0;
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            // location.reload();
                        }
                        break;
                }

                updateStatus();
            }

            uploader.onUploadSuccess = function (file, response) {
                images.push(response.key);
                console.log('上传成功：地址 => https://res.suyongw.com/' + response.key); //这里可以得到后台返回的数据
            };

            uploader.onUploadProgress = function (file, percentage) {
                var $li = $('#' + file.id),
                    $percent = $li.find('.progress span');

                $percent.css('width', percentage * 100 + '%');
                percentages[file.id][1] = percentage;
                updateTotalProgress();
            };

            uploader.onFileQueued = function (file) {
                fileCount++;
                fileSize += file.size;

                if (fileCount === 1) {
                    $placeHolder.addClass('element-invisible');
                    $statusBar.show();
                }

                addFile(file);
                setState('ready');
                updateTotalProgress();
            };

            uploader.onFileDequeued = function (file) {
                fileCount--;
                fileSize -= file.size;

                if (!fileCount) {
                    setState('pedding');
                }

                removeFile(file);
                updateTotalProgress();

            };

            uploader.on('all', function (type) {
                var stats;
                switch (type) {
                    case 'uploadFinished':
                        setState('confirm');
                        break;

                    case 'startUpload':
                        setState('uploading');
                        break;

                    case 'stopUpload':
                        setState('paused');
                        break;

                }
            });

            uploader.onError = function (code) {
                alert('Eroor: ' + code);
            };

            $upload.on('click', function () {
                if ($(this).hasClass('disabled')) {
                    return false;
                }
                if (state === 'ready') {
                    uploader.upload();
                } else if (state === 'paused') {
                    uploader.upload();
                } else if (state === 'uploading') {
                    uploader.stop();
                }
            });

            $info.on('click', '.retry', function () {
                uploader.retry();
            });

            $info.on('click', '.ignore', function () {
                alert('todo');
            });

            $upload.addClass('state-' + state);

            updateTotalProgress();

        }

    };

    ShuStudio.fn.init.prototype = ShuStudio.fn;
    return ShuStudio;
})();