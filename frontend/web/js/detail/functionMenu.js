(function() {

    var tmpl2 = "\n    <div class=\"popQrcode\" v-show=\"showQrcode\" @click=\"showQrcode=false;\" transition=\"fade\"  >\n        <div class=\"info_popQrcode\" @click.stop>\n            <div class=\"canvas_info_popQrcode\" v-el:qc></div>\n            <div class=\"tip_info_popQrcode\">请到活动现场出示</div>\n            <div class=\"detail_info_popQrcode\">\n                <div class=\"name_detail_ip\">\n                    <div class=\"left_name_dip\">{{certInfo._name}}</div>\n                    <div class=\"right_name_dip\">{{certInfo._tel}}</div>\n                </div>\n                <div class=\"fee_detail_ip\">\n                    <div class=\"left_fee_dip\">报名费</div>\n                    <div class=\"right_fee_dip\">{{certInfo._fee}}</div>\n                </div>\n                <div class=\"time_detail_ip\">\n                    <div class=\"left_time_dip\">报名时间</div>\n                    <div class=\"right_time_dip\">{{certInfo._date}}</div>\n                </div>\n            </div>\n        </div>\n        <div class=\"exit_popQrcode\"></div>\n    </div>\n    ";
    var pq = Vue.extend({
        template: tmpl2,
        created: function() {
            if ("undefined" != typeof _info) {
                this.hotelInfo = _info._hotelInfo;
                this.certInfo = _info._certInfo;
                var that = this;
            }
        },
        data: function() {
            return {
                showQrcode: false,
                hotelInfo: {

                },
                certInfo: {

                }
            }
        },
        events: {
            "childShowQr": function() {
                var $qr = $(this.$els.qc);
                if (0 == $qr.find('canvas').length) {
                    $qr.qrcode({
                        width: 210,
                        height: 210,
                        text: this.certInfo._qrUrl
                    });
                }
                this.showQrcode = true;
            }
        },
        methods: {
            "hideQr": function() {

            }
        }
    });
    Vue.component('cmp-pq', pq);


var tmpl = "\n        <div class=\"menu_detail_vue\" v-if=\"show\" style=\"display:none\" v-show=\"show\"\n            \"\n        >\n                       <template v-if=\"!ir\"> <a :href=\"hotelInfo._hotline?'tel:'+hotelInfo._hotline:'javascript:void(0)'\">\n                <div class=\"boxTele_menu_dv\" @click=\"hasTel(hotelInfo._hotline)\">\n                <span class=\"iconTele_menu_dv\"></span>\n                <div class=\"txtTele_menu_dv\">Hotline</div>\n                </div>\n            </a>\n            <div class=\"boxCancel_menu_dv\"\n                v-if=\"2==os\"\n                @click=\"cancelEnroll\"\n            > \n                <span class=\"iconCancel_menu_dv\"></span>\n                <div class=\"txtCancel_menu_dv\">Cancel</div>\n            </div>\n            <div class=\"boxProof_menu_dv\"\n                v-if=\"!hotelInfo._checkedIn&&certInfo._qrUrl\"\n                @click=\"showEnrollQr()\"\n            >\n                <span class=\"iconProof_menu_dv\"></span>\n                <div class=\"txtProof_menu_dv\">凭证</div>\n            </div>\n            <div class=\"boxReview_menu_dv\"\n                v-if=\"enabled\"\n             @click=\"goTo('')\"\n            >\n                <span class=\"iconReview_menu_dv\"></span>\n                <div class=\"txtReview_menu_dv\"></div>\n            </div>\n\n            <div class=\"state_menu_dv\" v-if=\"(hotelInfo._isFull)&&1==os\">\n                {{(hotelInfo._checkedIn?'Checked-In'}}\n            </div>\n            <button class=\"bookAction_menu_dv\" ontouchstart=\"\" \n             v-if=\"!hotelInfo._isJoined&&(!hotelInfo._isFull||2==os)\"\n             @click=\"bookAction\"\n             :class=\"{'wp':2==os}\"\n              >\n                {{2==os?'Book(':'Book'}}{{2==os?tld+')':''}}\n                </button></template>\n                <template v-else>\n                 <button ontouchstart=\"\"\n                  class=\"bookAction_menu_dv\" \n                  @click=\"goTo('')\"\n                    v-if=\"otherInfo._isOwner\"\n                  >\n                    {{si=='2'?'':''}}\n                </button>\n                </template>\n        </div>\n    ";
var fm = Vue.extend({
        template: tmpl,
        created: function() {
            if ("undefined" != typeof _info) {
                this.show = true;
                this.hotelInfo = _info._hotelInfo;
                this.otherInfo = _info._otherInfo;
                this.certInfo = _info._certInfo;
                if (this.hotelInfo._isJoined) {
                    if (this.hotelInfo._checkedIn) {
                        this.txtState = 'Checked-In';
                    } else {
                        this.txtState = 'Paid';
                    }
                }
            }
        },
        props: ['os', 'tl','ir','si'],
        data: function() {
            return {
                show: false,
                txtState: 'I want to pay',
                txtAction: 'I want to pay',
                tld: '30:00',
                hotelInfo: {

                },
                otherInfo: {

                },
                certInfo: {

                }
            }
        },
        events:{
            "timeCount":function(){
                if(this.tl){
                    this.timeGoing();
                }
            }
        },
        methods: {
            "getCurrentState":function(){

            },
            "hasTel":function(number){
                if(!number){
                    this.$dispatch('popTip', 'No hot-line');
                }
            },
            "bookAction": function() {
                if (2 != this.os) {
                    this.$dispatch('informShowBook');
                } else {
                    this.$dispatch('informShowPay');
                }
            },
            timeGoing: function() {
                var that = this;
                var tg = function() {
                    setTimeout(function() {
                        that.tl--;
                        if (0 >= that.tl || that.hotelInfo._isJoined) {
                            that.tl = 0;
                            clearTimeout(tg);
                        } else {
                            var part1 = ~~(that.tl / 60);
                            if (part1 < 10) {
                                part1 = '0' + part1;
                            }
                            var part2 = (that.tl % 60);
                            if (part2 < 10) {
                                part2 = '0' + part2;
                            }
                            that.tld = part1 + ':' + part2;
                            tg();
                        }
                    }, 1000);
                }
                tg();
            },
            "goTo": function(url) {
                location.href = url;
            },
            "showEnrollQr": function() {
                this.$dispatch("showQr")
            },
            "cancelEnroll": function() {
                var that = this;
                $.ajax({
                    type: "POST",
                    url: '/post/api:144',
                    data: {
                        hotel_id: this.otherInfo._id
                    },
                    success: function(data) {
                        if (0 != data.state) {
                            that.$dispatch('popTip', data.msg);
                            return;
                        }
                        that.os = 1;
                    }
                })
            },
        }
    });
    Vue.component('cmp-fm', fm);
})();
