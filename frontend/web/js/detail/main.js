var vueDetail = new Vue({
    el: 'body',
    created: function() {
        var that = this;

        if ("undefined" != typeof(_info)) {
            for (var index in _info) {
                if (_info.hasOwnProperty(index)) {
                    this[String(index).slice(1)] = _info[index];
                }
            }
            this.show = true;

            setTimeout(function() {
                that.packShareInfo();
                that.hitOneTime();
            }, 0);
        }
        this.getBookInfo();
    },
    events: {
        "showQr": function() {
            this.$broadcast('childShowQr');
        },
        'popTip': function(msg, type) {
            this.$broadcast('popMsg', msg, type);
        },
        "informShowBook": function() {
            this.$broadcast('showBook');
        },
        "informShowPay": function() {
            this.$broadcast('showPay');
        },
        "informGetJL": function() {
            this.$broadcast('getJL');
        },
        "informCountTime": function() {
            this.$broadcast('timeCount');
        }
    },
    methods: {
        decodeTxt: function(txt) {
            return decodeURIComponent(txt);
        },
        "goTo": function(url) {
            location.href = url + '?t=' + (+new Date());
        },
        "getBookInfo": function() {
            var that = this;
            $.ajax({
                method: 'POST',
                url: '/post/api:97',
                data: {
                    hotel_id: this.otherInfo._id
                },
                success: function(data) {
                    if (0 != data.state) {
                        that.$broadCast('popMsg', '获取订单状态出错');
                        return;
                    }
                    that.orderInfo = data;
                    that.orderState = data.orderState;
                    if (data.timeLeft) {
                        that.timeLeft = data.timeLeft;
                        setTimeout(function() {
                            that.$broadcast('timeCount');
                        }, 0);
                    }
                },
                error: function() {
                    that.$broadCast('popMsg', '网络错误--获取订单');
                }
            });
        },
        'openMap': function(longitude, latitude) {
            if (longitude && latitude) {
                wx.openLocation({ longitude: longitude, latitude: latitude });
            }
        },
        'getHeadIcon': function(url) {
            if (url) {
                return url;
            }
            return '/images/other/face_default_200.png';
        },
        'getRoomStatus': function(lbs) {
            if (0 == lbs._leftTicks) {
                if (0 == lbs._payingTicks) {
                    return 'No Rooms';
                } else {
                    return 'No Rooms' + '（' + lbs._payingTicks + ' guest are paying）';
                }
            }
            if (0 == lbs._payingTicks) {
                return 'Remaining ' + lbs._leftTicks + ' rooms and ' + lbs._allTicks + ' guest';
            } else {
                return 'Remaining ' + lbs._leftTicks + ' rooms and ' + lbs._allTicks + ' guest（' + lbs._payingTicks + ' guests are paying）';
            }
        }
    },
    data: function() {
        return {
            show: false,
            orderState: false,
            timeLeft: 0,
            orderInfo: {

            },
            bannerInfo: {

            },
            roomTypes: {

            },
            extraGuestOptions: [],
            hotelDescriptions: '',
            lbsInfo: {

            },
            otherInfo: {

            },
            recordInfo: {

            },
            tmplInfo: {

            },
            enabled: false,
            showRelatedHotel: false
        }
    }
});
