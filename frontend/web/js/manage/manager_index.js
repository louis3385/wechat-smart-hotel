window.indexDisplay = new Vue({
    el: '.vueContent',
    created: function() {

        this.firstShow = true;
        $(window).on('scroll', this.scrollData.bind(this));
        $('body').on('touchmove',this.fingerMove.bind(this));

        var href = window.location.href;
        var matched = href.match(/key=([^&]*)($|&)/);
        if (matched && matched.length > 0) {
            this.searchWord = window.decodeURI(matched[1]);
            var city=href.match(/city=([^&]*)($|&)/);
            if(city&&city.length>0){
                this.currentCity=window.decodeURI(city[1]);
            }
            this.packShareInfo();
            this.searchSome();
            return;
        } else {
            if ("undefined" != typeof(detail2index)) {
                var searchKey = sessionStorage.getItem('searchKey');
                if ("undefined" != typeof(searchKey)) {
                    this.searchWord = window.decodeURI(searchKey);
                    this.searchSome();
                    return;
                }
            }
        }
        this.ajaxData();
    },
    data: function() {
        return {
            firstShow: false,
            hasNextIndex: false,
            actData: [],
            searching: false,
            loading: false,
            currentSearchType: 5,
            searchWord: '',
            searchType: {
                all: 'all',
                onlyPlace: 'city',
                placeNkeyword: 'all'
            },
            searchState: 'normal',
            nextIndex: 1,
            selectingCity: false,
            currentCity: 'China',
            start2Search: false,
            scrolling: false,
            gettingData: false,
        }
    },
    methods: {
        "packShareInfo":function(){
            if ("undefined" != typeof dataForShare) {
                dataForShare.title = 'Hotels '+this.currentCity+'优质酒店精选';
                if(this.searchWord&&this.searchWord.length>0){
                    dataForShare.description = '『'+this.searchWord+'』的酒店';
                }else{
                    dataForShare.description = '';
                 }
                dataForShare.weixin_url = location.href;
                dataForShare.weixin_tl_icon=dataForShare.weixin_icon = location.protocol+'//'+location.host+'/images/detail/rpDefault.png';
            }
        },
        "fingerMove":function(event){
            if(this.start2Search){
                event.preventDefault();
            }
        },
        "go2url": function(url) {
            sessionStorage.setItem('searchKey', this.searchWord);
            location.href = url + "?t=" + (+new Date());
        },
        "clearNstayfocus": function() {
            this.searchWord = '';
            this.$els.search.focus();
            this.$els.search.focus();
        },
        "closeSearchBox": function() {
            this.start2Search = false;
            $('#use4DownKeyBoard').focus();
        },
        "goTo": function(href) {
            window.location.href = href + '?t=' + (+new Date());
        },
        "goToSearchInput": function() {
            this.start2Search = true;
            this.$els.search.focus();
        },
        "redirectToSearchResult":function(){

            location.href='/?key='+window.encodeURIComponent(this.searchWord)+'&city='+window.encodeURIComponent(this.currentCity);
        },
        "searchSome": function() {
            this.reInit();
            this.currentSearchType = this.searchType.all;
            this.ajaxData();
            $('#use4DownKeyBoard').focus();
        },
        "selectCity": function(city) {
            this.currentCity = city;
            location.href='/?key='+window.encodeURIComponent(this.searchWord)+'&city='+window.encodeURIComponent(this.currentCity);
           /* this.reInit();
            this.currentCity = city;
            this.currentSearchType = this.searchType.onlyPlace;
            this.actData = [];
            this.ajaxData();
            this.selectingCity = false;*/
        },
        reInit: function() {
            this.hasNextIndex = false;
            this.searching = false;
            this.loading = false;
            this.nextIndex = 1;
            this.selectingCity = false;
            this.start2Search = false;
            this.gettingData = false;
        },
        "ajaxData": function() {
            this.searching = true;
            this.loading = true;
            this.gettingData = true;
            if (0 == this.searchWord.length) {
                this.currentSearchType = this.searchType.onlyPlace;
            } else {
                this.currentSearchType = this.searchType.all;
            }
            var apiUrl = _api3._search;
            apiUrl += '?page_num=' + this.nextIndex +
                '&type=' + this.currentSearchType +
                '&city=' + encodeURIComponent(this.currentCity) +
                '&key=' + encodeURIComponent(this.searchWord);
            $.ajax({
                type: 'get',
                url: apiUrl,
                success: function(data) {
                    if (0 != data.state) {
                        $('.popToast').html(data.msg).fadeIn();
                        setTimeout(function() {
                            $('.popToast').fadeOut();
                        }, 1500);
                        return;
                    }
                    if (this.nextIndex > 1) {
                        var rData = this.fillData(data.manager_hotel_list);
                        for (var i = 0; i < rData.length; i++) {
                            this.actData.push(rData[i]);
                        }
                    } else {
                        this.actData = this.fillData(data.manager_hotel_list);
                    }
                    this.nextIndex++;
                    this.hasNextIndex = data.next_state;
                }.bind(this),
                error: function() {
                    $('.popToast').html('Network Error, please refresh').fadeIn();
                    setTimeout(function() {
                        $('.popToast').fadeOut();
                    }, 1500);
                },
                complete: function() {
                    this.loading = false;
                    this.searching = false;
                    this.scrolling = false;
                    setTimeout(function() {
                        this.gettingData = false;
                    }.bind(this), 400);
                }.bind(this)
            })
        },
        "scrollData": function(event) {
            if (this.selectingCity) {
                event.stopPropagation();
                event.preventDefault();
                return;
            }
            this.scrolling = true;
            var body = document.body,
                html = document.documentElement;

            var height = body.scrollHeight;
            var $target = $(event.currentTarget);
            if (this.hasNextIndex && !this.loading && $target.height() + $target.scrollTop() > height - 200) {
                this.ajaxData();
            }

        },
        "fillData": function(oData) {
            var rData = [];
            for (var i = 0; i < oData.length; i++) {
                rData.push(oData[i]);
            }
            return rData;
        },
        "getAllAct": function() {

        },
        "searchAct": function() {

        }
    }
})
