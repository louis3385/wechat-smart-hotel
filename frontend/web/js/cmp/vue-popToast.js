(function() {
    /*var tmpl=`
        <div class="popToast2"
        style="display:none"
        v-show="show"
        transition='showToast'
        :class="{'top':'prompt'==type}"
        >
            <span class="icon_popToast" 
            v-show="showIcon"
            style="display:none"
            >
            </span>
            <span class="txt_popToast" v-text="msg"></span>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
        </div>
        `;*/

    
var tmpl = "\n        <div class=\"popToast2\"\n        style=\"display:none\"\n        v-show=\"show\"\n        transition='showToast'\n        :class=\"{'top':'prompt'==type}\"\n        >\n            <span class=\"icon_popToast\" \n            v-show=\"showIcon\"\n            style=\"display:none\"\n            >\n            </span>\n            <span class=\"txt_popToast\" v-text=\"msg\"></span>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      \n        </div>\n        ";
    var toast = Vue.extend({
        template: tmpl,
        data: function() {
            return {
                msg: '',
                show: false,
                showIcon:false,
                type:'error'
            }
        },
        methods: {
            "reInit":function(){
                var that=this;
               /* setTimeout(function(){
                    that.msg='';
                },600);*/
                this.show=false;
                this.showIcon=false;
                this.type='error';
            },
            "showPop": function(msg,type) {
                var that = this;
                that.msg = msg;
                that.show = true;
                if('undefined'!=typeof type&&'prompt'==type){
                    this.type=type;
                    this.showIcon=true;
                }
                setTimeout(function() {
                    that.reInit();
                }, 1800);
            }
        },
        events: {
            "popMsg": function(msg,type) {
                this.showPop(msg,type);
            }
        }
    });
    Vue.component('cmp-toast', toast);
})();
