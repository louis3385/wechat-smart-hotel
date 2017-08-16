(function() {
    //var tmpl = "\n        <div class=\"bookProcess_detail_vue\"\n            v-if=\"show\"\n            @click=\"hideBook\"\n            transition=\"fade\"\n        >\n            <div class=\"container_ec_dv\"\n            transition=\"rise\"\n            @click.stop\n            v-if=\"showRoomType\"\n            >\n                <div class=\"title_container_ecdv\">-Room Type-</div>\n                <div class=\"box_container_ecdv\">\n                    <div class=\"leaf_box_cedcdv\" \n                    v-for=\"ci in roomTypes\"\n                    >\n                        <label\n                         :for=\"'qwe'+$index\"\n                         @click=\"selectPayItem(ci._id)\"\n                         >\n                        <div class=\"detail_leaf_bcv\">\n                            <div >\n                                <span class=\"name_leaf_lcv\">{{ci._name}}</span>\n                                <span class=\"price_leaf_lcv\">￥{{ci._payItemPrice}}</span>\n                            </div>\n                            <div class=\"check_leaf_lcv\"  >\n                                <input type=\"radio\" name=\"itemSelected\" style=\"display:none\" :id=\"'qwe'+$index\"\n                                :checked='$index==0'\n                                 />\n                                <div class=\"icon_check_llcv\"></div>\n                            </div>\n                            </div>\n                        <div class=\"left_box_bcd\">{{payItemStatus(ci)}}</div>\n                        </div>\n                        </label>\n                </div>\n                <button class=\"btn_container_ecdv\"\n                    @click=\"fillBookInfo\"\n                    ontouchstart=\"\"\n                >Next</button>\n                <div class=\"deal_container_ecdv\">\n                    <span class=\"icon_deal_cev\"></span>\n                    <span class=\"txt_deal_cev\">I have read and accepted the Payment Agreement</span>\n                </div>\n            </div>\n\n            <div class=\"bookInfo_ec_dv\"\n                transition=\"rise\"\n                @click.stop\n                v-if=\"showPayInfo\"\n            >\n                <div class=\"close_bookInfo_ecdv\"></div>\n                <div class=\"container_bookInfo_ecdv\">\n                    <div class=\"leaf_container_eev\" v-for=\"ea in eaop\">\n                        <div class=\"txt_container_eev\">{{ea.name}}</div>\n                        <input class=\"input_container_eev\" type=\"text\" v-model=ea.txt />\n                    </div>\n                    <button class=\"btn_container_eev\" @click=\"book\" ontouchstart=\"\">Pay</button>\n                    <div class=\"support_container_eev\">This service is powered by WeChat Smart Hotel</div>\n                </div>\n            </div>\n\n        </div>\n    ";
    //var tmpl = "\n        <div class=\"bookProcess_detail_vue\"\n            v-if=\"show\"\n            @click=\"hideBook\"\n            transition=\"fade\"\n        >\n            <div class=\"container_ec_dv\"\n            transition=\"rise\"\n            @click.stop\n            v-if=\"showRoomType\"\n            >\n                <div class=\"title_container_ecdv\">-Room Type-</div>\n                <div class=\"box_container_ecdv\">\n                    <div class=\"leaf_box_cedcdv\" \n                    v-for=\"ci in roomTypes\"\n                    >\n                        <label\n                         :for=\"'qwe'+$index\"\n                         @click=\"selectPayItem(ci._id)\"\n                         >\n                        <div class=\"detail_leaf_bcv\">\n                            <div >\n                                <span class=\"name_leaf_lcv\">{{ci._name}}</span>\n                                <span class=\"price_leaf_lcv\">￥{{ci._payItemPrice}}</span>\n                            </div>\n                            <div class=\"check_leaf_lcv\"  >\n                                <input type=\"radio\" name=\"itemSelected\" style=\"display:none\" :id=\"'qwe'+$index\"\n                                :checked='$index==0'\n                                 />\n                                <div class=\"icon_check_llcv\"></div>\n                            </div>\n                            </div>\n                        <div class=\"left_box_bcd\">{{payItemStatus(ci)}}</div>\n                        </div>\n                        </label>\n                </div>\n                <button class=\"btn_container_ecdv\"\n                    @click=\"fillBookInfo\"\n                    ontouchstart=\"\"\n                >Next</button>\n                <div class=\"deal_container_ecdv\">\n                    <span class=\"icon_deal_cev\"></span>\n                    <span class=\"txt_deal_cev\">I have read and accepted the Payment Agreement</span>\n                </div>\n            </div>\n\n            <div class=\"bookInfo_ec_dv\"\n                transition=\"rise\"\n                @click.stop\n                v-if=\"showPayInfo\"\n            >\n                <div class=\"close_bookInfo_ecdv\"></div>\n                <div class=\"container_bookInfo_ecdv\">\n                    <div class=\"leaf_container_eev\" v-for=\"ea in eaop\">\n                        <div class=\"txt_container_eev\">{{ea.name}}</div>\n                        <input class=\"input_container_eev\" type=\"text\" v-model=ea.txt />\n                    </div>\n\n  <div class=\"banner_tc\">\n\t\t\t\tDate and Time\n\t\t\t</div>\n\t\t\t<div class=\"skin_tc\" >\n\t\t\t\t<div class=\"startDate_skin_tc\" :class=\"{'set':startTime}\">\n\t\t\t\t<input id=\"\" class=\"datepicker input_startDate_stc\" \n\t\t\t\tplaceholder='Checkin at'\n\t\t\t\tv-model=startTime\n\t\t\t\t />\n\t\t\t\t\t<div class=\"arrow_stc\"></div>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"endDate_skin_tc\" :class=\"{'set':endTime}\">\n\t\t\t\t<input class=\"datepicker2 input_endDate_stc\"\n\t\t\t\t placeholder='Checkout at'\n\t\t\t\t v-model=endTime\n\t\t\t\t  />\n\t\t\t\t\t<div class=\"arrow_stc\"></div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t                  <button class=\"btn_container_eev\" @click=\"book\" ontouchstart=\"\">Pay</button>\n                    <div class=\"support_container_eev\">This service is powered by WeChat Smart Hotel</div>\n                </div>\n            </div>\n\n        </div>\n    ";
    var tmpl = "\n        <div class=\"bookProcess_detail_vue\"\n            v-if=\"show\"\n            @click=\"hideBook\"\n            transition=\"fade\"\n        >\n            <div class=\"container_ec_dv\"\n            transition=\"rise\"\n            @click.stop\n            v-if=\"showRoomType\"\n            >\n                <div class=\"title_container_ecdv\">-Room Type-</div>\n                <div class=\"box_container_ecdv\">\n                    <div class=\"leaf_box_cedcdv\" \n                    v-for=\"ci in roomTypes\"\n                    >\n                        <label\n                         :for=\"'qwe'+$index\"\n                         @click=\"selectPayItem(ci._id)\"\n                         >\n                        <div class=\"detail_leaf_bcv\">\n                            <div >\n                                <span class=\"name_leaf_lcv\">{{ci._name}}</span>\n                                <span class=\"price_leaf_lcv\">￥{{ci._payItemPrice}}</span>\n                            </div>\n                            <div class=\"check_leaf_lcv\"  >\n                                <input type=\"radio\" name=\"itemSelected\" style=\"display:none\" :id=\"'qwe'+$index\"\n                                :checked='$index==0'\n                                 />\n                                <div class=\"icon_check_llcv\"></div>\n                            </div>\n                            </div>\n                        <div class=\"left_box_bcd\">{{payItemStatus(ci)}}</div>\n                        </div>\n                        </label>\n                </div>\n                <button class=\"btn_container_ecdv\"\n                    @click=\"fillBookInfo\"\n                    ontouchstart=\"\"\n                >Next</button>\n                <div class=\"deal_container_ecdv\">\n                    <span class=\"icon_deal_cev\"></span>\n                    <span class=\"txt_deal_cev\">I have read and accepted the Payment Agreement</span>\n                </div>\n            </div>\n\n            <div class=\"bookInfo_ec_dv\"\n                transition=\"rise\"\n                @click.stop\n                v-if=\"showPayInfo\"\n            >\n                <div class=\"close_bookInfo_ecdv\"></div>\n                <div class=\"container_bookInfo_ecdv\">\n                    <div class=\"leaf_container_eev\" v-for=\"ea in eaop\">\n                        <div class=\"txt_container_eev\">{{ea.name}}</div>\n                        <input class=\"input_container_eev\" type=\"text\" v-model=ea.txt />\n                    </div>\n\n  <div class=\"banner_tc\">\n\t\t\t\tDate and Time\n\t\t\t</div>\n\t\t\t<div class=\"skin_tc\" >\n\t\t\t\t<div class=\"startDate_skin_tc\" :class=\"{'set':startTime}\">\n\t\t\t\t<input id=\"datePicker\" class=\"datepicker input_startDate_stc\" \n\t\t\t\tplaceholder='Checkin at'\n\t\t\t\tv-model=startTime\n\t\t\t\t />\n\t\t\t\t\t<div class=\"arrow_stc\"></div>\n\t\t\t\t</div>\n\t\t\t\t<div class=\"endDate_skin_tc\" :class=\"{'set':endTime}\">\n\t\t\t\t<input  id=\"datePicker2\"class=\"datepicker2 input_endDate_stc\"\n\t\t\t\t placeholder='Checkout at'\n\t\t\t\t v-model=endTime\n\t\t\t\t  />\n\t\t\t\t\t<div class=\"arrow_stc\"></div>\n\t\t\t\t</div>\n\t\t\t</div>\n\t\t                  <button class=\"btn_container_eev\" @click=\"book\" ontouchstart=\"\">Pay</button>\n                    <div class=\"support_container_eev\">This service is powered by WeChat Smart Hotel</div>\n                </div>\n            </div>\n\n        </div>\n    ";
    var bookProcess = Vue.extend({
        template: tmpl,
        methods: {
            "paySuccess": function(data) {
                var that = this;
                that.os = 1;
                that.hotelInfo._isJoined = true;
                //凭证相关
                var uj = data.user_join;
                that.certInfo._qrUrl = uj.url;
                that.certInfo._name = uj.big_name;
                that.certInfo._tel = uj.mobile;
                that.certInfo._fee = uj.fee;
                that.certInfo._date = uj.date;
                that.reInit();
                that.show = false;
                that.$dispatch('popTip', 'Pay successfully', 'prompt');
                that.$dispatch('informGetJL');
            },
            "checkMoney": function() {
                var that = this;
                $.ajax({
                    type: 'POST',
                    url: '/post/api:18',
                    data: {
                        order_id: this.oi.payOrderId,
                        hotel_id: this.id
                    },
                    success: function(data) {
                        if (0 != data.state) {
                            that.$dispatch('popTip', data.errormessage);
                            return;
                        }
                        that.paySuccess(data);

                    },
                    error: function() {
                        that.$dispatch('popTip', '服务器出错--payOrderMoney');
                    }
                })
            },
            "prepareOrder": function() {
                var that = this;
                payOrder(that.oi.payOrderId, function() {
                    that.checkMoney();
                }, function() {
                    that.$dispatch('popTip', '服务器出错--payOrder');
                });
            },
            "book": function() {
                var that = this;
                if (this.roomTypes.length > 0 && !this.oi.itemIdSelected) {
                    this.oi.itemIdSelected = this.roomTypes[0]._id;
                }
                var eo = '';
                for (var i = 2; i < this.eaop.length; i++) {
                    if (2 != i) {
                        eo += '|';
                    }
                    eo += this.eaop[i].name + '^' + this.eaop[i].txt;
                }
                $.ajax({
                    type: 'POST',
                    url: '/post/api:18',
                    data: {
                        hotel_id: this.id,
                        pay_item_id: this.oi.itemIdSelected,
                        big_name: this.eaop[0].txt,
                        mobile: this.eaop[1].txt,
                        book_option: eo
                    },
                    success: function(data) {
                        if (0 != data.state) {
                            that.$dispatch('popTip', data.errormessage);
                            return;
                        }
                        //微信支付prepare
                        if (0 != data.pay.orderId) {
                            that.os = 2;
                            that.oi.payOrderId = data.pay.orderId;
                            that.prepareOrder();
                            that.tl = data.pay.timeLeft;
                            setTimeout(function() {
                                that.$dispatch('informCountTime');
                            }, 0);
                            return;
                        }
                        that.paySuccess(data);

                    },
                    error: function() {
                        that.$dispatch('popTip', '服务器出错--提交报名');
                    }
                })
            },
            "reInit": function() {
                this.showRoomType = false;
                this.showPayInfo = false;
            },
            "hideBook": function() {
                this.show = false;
                this.reInit();
            },
            "selectPayItem": function(id) {
                this.oi.itemIdSelected = id;
            },
            "fillBookInfo": function() {
                this.showRoomType = false;
                setTimeout(function() {
                    this.showPayInfo = true;
                }.bind(this), 310);
            },
            "payItemStatus": function(ci) {
                if (0 == ci._remainingCount) {
                    if (0 != ci._payingTicks) {
                        return '名额已满（' + ci._payingTicks + '人正在报名）';
                    } else {
                        return '名额已满';
                    }
                }
                if (0 != ci._payingTicks) {
                    return '剩' + ci._remainingCount + '人 / 共' + ci._allTicks + '人  ' + ci._payingTicks + '人正在报名';
                } else {
                    return '剩' + ci._remainingCount + '人 / 共' + ci._allTicks + '人';
                }
            }
        },
        props: ['os', 'tl', 'oi'],
        data: function() {
            return {
                show: false,
                showRoomType: false,
                showPayInfo: false,
                itemIdSelected: '',
                roomTypes: [],
                hasRoomType: false,
                selectIndex: 0,
                orderId: '',
                name: '',
                mobile: '',
                startTime: '',
                endTime: '',
                eaop: [{
                    name: 'Name',
                    txt: ''
                }, {
                    name: 'Mobile',
                    txt: ''
                }],
                hotelInfo: {

                },
                certInfo: {

                }
            }
        },
        events: {
            "showPay": function() {
                this.prepareOrder();
            },
            "showBook": function() {
                this.show = true;
                if (this.hasRoomType) {
                    setTimeout(function() {
                        this.showRoomType = true;
                    }.bind(this), 30);
                } else {
                    setTimeout(function() {
                        this.showPayInfo = true;
                    }.bind(this), 30)
                }
            }
        },
        created: function() {
            if ("undefined" != typeof _info) {
                for (var i = 0; i < _info._roomTypes.length; i++) {
                    _info._roomTypes[i]._checked = !!(i - 1);
                }
                this.hotelInfo = _info._hotelInfo;
                this.certInfo = _info._certInfo;
                this.id = _info._otherInfo._id;
                this.roomTypes = _info._roomTypes;
                this.hasRoomType = _info._otherInfo._hasRoomType && _info._roomTypes.length > 0;
                if (this.hasRoomType) {
                    this.itemIdSelected = _info._roomTypes[0]._id;
                }

                this.eaop[0].txt = _info._recordInfo._name;
                this.eaop[1].txt = _info._recordInfo._mobile;
                for (i = 0; i < _info._extraGuestOptions.length; i++) {
                    this.eaop.push({
                        name: _info._extraGuestOptions[i],
                        txt: ''
                    });
                }
            }
        }
    });
    Vue.component('cmp-bc', bookProcess);
})();