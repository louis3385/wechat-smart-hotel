(function() {

    var self;
    var $loading,$input,
    uploadUrl='/file/upload64',
    focusEle='.fr-view',
    maxWidth=800,
    errImg="";
    $.FroalaEditor.DefineIcon('insertImage2', { NAME: 'image' });
    $.FroalaEditor.RegisterCommand('insertImage2', {
        title: '插入图片',
        focus: false,
        undo: true,
        refreshAfterCallback: false,
        callback: function() {
        	if($input&&$input.length>0){
        		$input.remove();
        	}
            $input = $('<input type="file" accept="image/*" style="display:none"/>').appendTo($('body')).click();
            self = this;
            self.selection.save();
            $input.on('change', function(e) {

                var files = e.target.files;
                if (0 == files.length) {
                    return;
                }
                $loading=$('.loading').show();

                var fr = new FileReader;
                fr.addEventListener('load', function(fe) {

                    var dr = fe.target.result;
                    compressImage(dr);

                });
                fr.readAsDataURL(files[0]);


            });

        }
    });


    var compressImage = function(dr) {

        var img = new Image();
        img.onload = function() {

            var imgWidth = img.naturalWidth,
                imgHeight = img.naturalHeight,
                canvas = document.createElement('canvas'),
                compressWidth = maxWidth;
            var ctx = canvas.getContext('2d'),
                compressHeight = compressWidth * imgHeight / imgWidth;
            if (imgWidth < maxWidth) {
                compressWidth = imgWidth;
                compressHeight = imgHeight;
            }
            var orientation = 1;
            EXIF.getData(img, function() {
                orientation = parseInt(EXIF.getTag(img, "Orientation"));
                orientation = orientation ? orientation : 1;
            });
            if (orientation <= 4) {
                canvas.setAttribute('height', compressHeight);
                canvas.setAttribute('width', compressWidth);
                if (3 == orientation || 4 == orientation) {
                    ctx.translate(compressWidth, compressHeight);
                    ctx.rotate(180 * Math.PI / 180);
                }
            } else {
                canvas.setAttribute('height', compressWidth);
                canvas.setAttribute('width', compressHeight);
                if (orientation == 5 || orientation == 6) {
                    ctx.translate(compressHeight, 0);
                    ctx.rotate(90 * Math.PI / 180);
                } else if (orientation == 7 || orientation == 8) {
                    ctx.translate(0, compressWidth);
                    ctx.rotate(270 * Math.PI / 180);
                }
            }
            var toType = img.src.match(/(image\/[^]*);/)[1];
            ctx.drawImage(img, 0, 0, imgWidth, imgHeight, 0, 0, compressWidth, compressHeight);
            //console.log(toType);
            var compressSrc = (canvas.toDataURL(toType, 0.9));
            console.log("compressed image size is " + ~~(compressSrc.length / 1024) + "kb");

            var formData = new FormData();
            formData.append('file', compressSrc);
            formData.append('width', compressWidth);
            formData.append('height', compressHeight);
            formData.append('random', +new Date());

            sendImg(formData,insertImage);

        }

        img.src = dr;

    }


    var sendImg = function(data, callback) {

        var xhr = new XMLHttpRequest();

        xhr.open('POST',uploadUrl,true);

        xhr.onload = function() {
            if (200 == xhr.status) {
                if ("function" == typeof callback) {
                    callback(xhr.responseText);
                }
            }
        }
        xhr.onerror = function() {
            $loading.hide();
            callback(errImg);
            console.log("网络错误 请重新上传");
        }
        xhr.ontimeout = function() {
        	$loading.hide();
            callback(errImg);
            console.log("上传超时 请重新上传");
        }
        xhr.timeout = 15000;
     /*   setTimeout(function(){
        	xhr.abort();
        	$loading.hide();
        	callback(errImg);
        },xhr.timeout);*/

        console.log("start to send data");
        xhr.send(data);
    }

    var insertImage=function(url){
    	self.selection.restore();
    	 if (document.activeElement) {
             var cfocus = document.activeElement.id;
             if (cfocus != $(focusEle).get(0)) {
                 $(focusEle).focus();
             }
         }

    	var html='<p><img src='+url+' style="max-width:100%" /></p>';
    	self.html.insert(html);
    	$loading.hide();
    }

})();
