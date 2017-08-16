(function() {
    var tmpl = "<div class=\"pop_insertVideo\" style=\"display:none\" @click='show=false' v-show='show'>\n        <div class=\"box_pop_iv\" @click.stop>\n            <div class=\"title_box_piv\">插入视频</div>\n            <input placeholder=\"腾讯视频的视频链接\" class=\"input_box_piv\" v-model=videoUrl v-el:video />\n            <button class=\"btn_box_piv\" :class=\"{'active':videoUrl.length>0}\" @click=\"insertVideoUrl\">确定</button>\n        </div>\n    </div>";
    $.FroalaEditor.DefineIcon('insertVideo', { NAME: 'video-camera' });
    $.FroalaEditor.RegisterCommand('insertVideo', {
        title: '插入视频',
        focus: false,
        undo: true,
        refreshAfterCallback: false,
        callback: function() {
            var that = this;
            that.selection.save();
            var insertVideo=vuePost.$refs.iv;
            insertVideo.bindEditor(that);
            insertVideo.show=true;
            setTimeout(function() {
                    insertVideo.reInit();
                    insertVideo.$els.video.focus();
                }, 0);
        }
    });

    function insertEditor(that, html) {
        that.selection.restore();
        if (document.activeElement) {
            var cfocus = document.activeElement.id;
            if (cfocus != $('.fr-view').get(0)) {
                $('.fr-view').focus();
            }
        }
        that.html.insert(html);
    }

     var insertVideo = Vue.extend({
                template: tmpl,
                created: function() {
                    setTimeout(function() {
                        this.$els.video.focus();
                    }.bind(this), 0);
                },
                data: function() {

                    return {
                        show: false,
                        videoUrl: '',
                        that:null
                    }
                },
                methods: {
                    bindEditor:function(that){
                        this.that=that;
                    },
                    insertVideoUrl: function() {
                     var vid='';
                     var vu=this.videoUrl;
                     var um=vu.match(/vid=([^]*)($|&)/);
                     if(!um){
                        um=vu.match(/[^]*\/(.*).html/);
                     }
                     if(um&&um.length>1){
                        vid=um[1];
                     }
                     if(0==vid.length){
                        if("undefined"!=typeof vuePost){      
                            vuePost.$broadcast('popMsg', '不是有效的腾讯视频链接');
                        }
                        return;
                     }

                        var width=(295).toFixed(0);
                        var height=(width*9/16).toFixed(0);
                        src = 'http://v.qq.com/iframe/player.html?vid=' + vid + '&width=' + width + '&height=' + height + '&auto=0';
                        var html='<p><iframe class="video_iframe"   height="' 
                        + height + '" width="' + width +
                        '" frameborder="0" src="' + src + 
                        '" allowfullscreen changeWidth=0></iframe></p>';
                        insertEditor(this.that,html);
                        this.show=false;

                    },
                    reInit: function() {
                        this.videoUrl = '';
                    }
                }
            });
     Vue.component('cmp-iv',insertVideo);
})();
