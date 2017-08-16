(function() {
    var tmpl = "\n        <div class=\"book_detail_vue\"\n            v-if=\"show\"\n            @click=\"hideBook\"\n            transition=\"fade\"\n        >\n            <div class=\"container_ec_dv\"\n            transition=\"rise\"\n            @click.stop\n            v-if=\"showChargeItem\"\n            >\n                <div class=\"title_container_ecdv\">-收费项目-</div>\n                <div class=\"box_container_ecdv\">\n                    <div class=\"leaf_box_cedcdv\" \n                    v-for=\"ci in chargeItems\"\n                    >\n                        <label\n                         :for=\"'qwe'+$index\"\n                         @click=\"selectPayItem(ci._id)\"\n                         >\n                        <div class=\"detail_leaf_bcv\">\n                            <div >\n                                <span class=\"name_leaf_lcv\">{{ci._name}}</span>\n                                <span class=\"price_leaf_lcv\">￥{{ci._payItemPrice}}</span>\n                            </div>\n                            <div class=\"check_leaf_lcv\"  >\n                                <input type=\"radio\" name=\"itemSelected\" style=\"display:none\" :id=\"'qwe'+$index\"\n                                :checked='$index==0'\n                                 />\n                                <div class=\"icon_check_llcv\"></div>\n                            </div>\n                            </div>\n                        <div class=\"left_box_bcd\">{{payItemStatus(roomType)}}</div>\n                        </div>\n                        </label>\n                </div>\n                <button class=\"btn_container_ecdv\"\n                    @click=\"fillBookInfo\"\n                    ontouchstart=\"\"\n                >Next</button>\n                <div class=\"deal_container_ecdv\">\n                    <span class=\"icon_deal_cev\"></span>\n                    <span class=\"txt_deal_cev\">I accept the Payment Agreement</span>\n                </div>\n            </div>\n\n            <div class=\"bookInfo_ec_dv\"\n                transition=\"rise\"\n                @click.stop\n                v-if=\"showBookInfo\"\n            >\n                <div class=\"close_enrollInfo_ecdv\"></div>\n                <div class=\"container_bookInfo_ecdv\">\n                    <div class=\"leaf_container_eev\" v-for=\"ea in eaop\">\n                        <div class=\"txt_container_eev\">{{ea.name}}</div>\n                        <input class=\"input_container_eev\" type=\"text\" v-model=ea.txt />\n                    </div>\n                    <button class=\"btn_container_eev\" @click=\"book\" ontouchstart=\"\">Book</button>\n                    <div class=\"support_container_eev\">This is powered by hotel WeChat</div>\n                </div>\n            </div>\n\n        </div>\n    ";
    var book = Vue.extend({
        template: tmpl,
        methods: {
            "bookSuccess": function(data) {
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
                //关闭报名窗口
                that.reInit();
                that.show = false;
                that.$dispatch('popTip', 'Booking Successfully', 'prompt');
                that.$dispatch('informGetJL');
            },
            "checkMoney": function() {
                var that = this;
                $.ajax({
                    type: 'POST',
                    url: '/post/api:18',
                    data: {
                        order_id: this.oi.payOrder_id,
                        hotel_id: this.id
                    },
                    success: function(data) {
                        if (0 != data.state) {
                            that.$dispatch('popTip', data.errormessage);
                            return;
                        }
                        //book successfully
                        that.bookSuccess(data);

                    },
                    error: function() {
                        that.$dispatch('popTip', '服务器出错--payOrderMoney');
                    }
                })
            },
            "prepareOrder": function() {
                var that = this;
                payOrder(that.oi.payOrder_id, function() {
                    that.checkMoney();
                }, function() {
                    that.$dispatch('popTip', '服务器出错--payOrder');
                });
            },
            "book": function() {
                var that = this;
                if (this.chargeItems.length > 0 && !this.oi.itemIdSelected) {
                    this.oi.itemIdSelected = this.chargeItems[0]._id;
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
                            that.oi.payOrder_id = data.pay.orderId;
                            that.prepareOrder();
                            that.tl = data.pay.time_left;
                            setTimeout(function() {
                                that.$dispatch('informCountTime');
                            }, 0);
                            return;
                        }
                        //免费活动成功
                        that.bookSuccess(data);

                    },
                    error: function() {
                        that.$dispatch('popTip', '服务器出错--提交报名');
                    }
                })
            },
            "reInit": function() {
                this.showChargeItem = false;
                this.showBookInfo = false;
            },
            "hideBook": function() {
                this.show = false;
                this.reInit();
            },
            "selectPayItem": function(id) {
                this.oi.itemIdSelected = id;
            },
            "fillBookInfo": function() {
                this.showChargeItem = false;
                setTimeout(function() {
                    this.showBookInfo = true;
                }.bind(this), 310);
            },
            "payItemStatus": function(ci) {
                if ('nolimit' == ci._remainingCount) {
                    return '不限人数';
                } else {
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
            }
        },
        props: ['os', 'tl', 'oi'],
        data: function() {
            return {
                show: false,
                showChargeItem: false,
                showBookInfo: false,
                itemIdSelected: '',
                chargeItems: [],
                isPay: false,
                selectIndex: 0,
                orderId: '',
                name: '',
                mobile: '',
                eaop: [{
                    name: '姓名',
                    txt: ''
                }, {
                    name: '手机号',
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
                if (this.isPay) {
                    setTimeout(function() {
                        this.showChargeItem = true;
                    }.bind(this), 30);
                } else {
                    setTimeout(function() {
                        this.showBookInfo = true;
                    }.bind(this), 30)
                }
            }
        },
        created: function() {
            if ("undefined" != typeof _info) {
                for (var i = 0; i < _info._chargeItems.length; i++) {
                    _info._chargeItems[i]._checked = !!(i - 1);
                }
                this.hotelInfo = _info._hotelInfo;
                this.certInfo = _info._certInfo;
                this.id = _info._otherInfo._id;
                this.chargeItems = _info._chargeItems;
                this.isPay = _info._otherInfo._isPay && _info._chargeItems.length > 0;
                if (this.isPay) {
                    this.itemIdSelected = _info._chargeItems[0]._id;
                }

                this.eaop[0].txt = _info._recordInfo._name;
                this.eaop[1].txt = _info._recordInfo._mobile;
                for (i = 0; i < _info._extraApplicantOptions.length; i++) {
                    this.eaop.push({
                        name: _info._extraApplicantOptions[i],
                        txt: ''
                    });
                }


            }
        }
    });
    Vue.component('cmp-book', book);
})();
