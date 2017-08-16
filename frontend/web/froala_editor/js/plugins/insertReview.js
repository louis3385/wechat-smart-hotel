 (function() {
     var tmpl = "<div class=\"popInfo_vue_insertActivity\" :class=\"{'show':show }\" >\n\n        <div>\n            <div class=\"toolBar_insertAct\" :class=\"{'all': 'all'==currentTab}\">\n                <span class=\"return_tbar_iact\" @click=\"hideAct()\"></span>\n                <span @click=\"switchTo('my')\" class=\"my_tab_tbia\">我的活动</span>\n                <span class=\"all_tab_tbia\" @click=\"switchTo('all')\">全部活动</span>\n                <span class=\"affirm_tbar_iact\" :class=\"{ 'everSelected':!!(selectActs.length)}\" @click=\"insertAction\"></span>\n            </div>\n            <div class=\"search_tool_insertAct\" v-if=\"'all'==currentTab\">\n                <input type=\"text\" class=\"input_st_iac\" v-model=\"searchWord\" @keyup.enter=\"searchTitle\" placeholder=\"搜索活动标题\" v-el:search />\n                <span class=\"icon_cancelSearch\" v-if=\"searchWord.length>0\" @click=\"clearNstayfocus\"></span>\n                <span class=\"icon_st_ia\" @click=\"searchTitle\"></span>\n            </div>\n            <div class=\"collection_leaves_act\" @scroll=\"scrollData($event)\" :class=\"{'my': 'my'==currentTab}\">\n                <div class=\"box_leaf_act\" v-for=\"act in actData\" :data-href=act.href>\n                    <div class=\"leaf_act\" @click=\"act.selected=!act.selected\">\n                        <div class=\"content_leaf_act\">\n                            <div class=\"title_leaf_act\" v-text=act.title></div>\n                            <div class=\"period_leaf_act\" v-text=act.date||act.over_date></div>\n                        </div>\n                        <div class=\"act_leaf_act\">\n                            <input type=\"checkbox\" :id=act.rid class=\"hideInput_selector_act\" :checked=act.selected />\n                            <span class=\"selector_act_leaf\">\n                                    \n                                </span>\n                        </div>\n                    </div>\n                </div>\n                <div class=\"spinner\" v-if=\"gettingData\" v-show=\"gettingData\" style=\"display:none\">\n                    <div class=\"bounce1\"></div>\n                    <div class=\"bounce2\"></div>\n                    <div class=\"bounce3\"></div>\n                </div>\n                <div class=\"noData_vue\" v-if=\"!gettingData&&0==actData.length\">\n                    <div class=\"t\">\n                        <p><img class=\"hdPic_noData_vue\" src=\"/images/other/xiao.png\" /></p>\n                        <p>没有找到相关的活动~</p>\n                    </div>\n                </div>\n                <div style=\"text-align:center;color: rgb(143,151,179);margin:10px 0\" v-if=\"!hasNextIndex&&actData.length>=6\">没有更多了~</div>\n            </div>\n        </div>\n    </div> \n   ";
     $.FroalaEditor.DefineIcon('insertReview', { NAME: 'sign-language' });
     $.FroalaEditor.RegisterCommand('insertReview', {
         title: '插入活动',
         focus: false,
         undo: true,
         refreshAfterCallback: false,
         callback: function() {
             var that = this;

             var getIndex = 1;
             window.wsh = $(window).scrollTop();
             that.selection.save();
             $('#use4DownKeyBoard').focus();
             var insertAct = vuePost.$refs.ia;
             insertAct.bindEditor(that);
             if (insertAct) {
                 setTimeout(function() {
                     insertAct.showAct();
                     insertAct.switchTo('my');
                 }, 300);
                 return;
             }


         }
     });
     var insertAct = Vue.extend({
         template: tmpl,
         created: function() {
         },
         data: function() {
             return {
                 show: false,
                 currentTab: 'my',
                 actData: [],
                 index: 1,
                 loading: false,
                 searchWord: '',
                 hasNextIndex: false,
                 displayData: 'my',
                 gettingData: false,
                 that: null
             }
         },
         computed: {
             "selectActs": function() {
                 var sa = [];
                 for (var i = 0; i < this.actData.length; i++) {
                     if (this.actData[i].selected) {
                         sa.push(this.actData[i]);
                     }
                 }
                 return sa;
             }
         },
         methods: {
             "bindEditor": function(that) {
                 this.that = that;
             },
             "clearNstayfocus": function() {
                 this.searchWord = '';
                 this.$els.search.focus();
             },
             "insertAction": function() {
                 var sacts = this.selectActs;
                 if (0 == this.selectActs.length) {
                     return;
                 }
                 var tmpA = "";
                 for (var i = 0; i < sacts.length; i++) {
                     tmpA = tmpA + ('<p><a href="' + sacts[i].href + '">' + sacts[i].title + '</a></p>');
                 }
                 this.hideAct();
                 setTimeout(function() {
                     insertEditor(this.that, tmpA);
                 }.bind(this), 0);

             },
             "packData": function(data) {
                 var rData = [];
                 var acts = data.manager_party_list || data.activity_list;
                 for (var i = 0; i < acts.length; i++) {
                     rData.push(acts[i]);
                     rData[i].rid = +(new Date()) + i;
                     rData[i].aid = acts[i].activity_id || acts[i].id;
                     rData[i].selected = false;
                     if (acts[i].type) {
                         if ('review' == acts[i].type) {
                             rData[i].href = '/party/review/' + rData[i].aid;
                         } else {
                             rData[i].href = '/party/' + rData[i].aid;
                         }
                     } else {
                         rData[i].href = '/party/' + rData[i].aid;
                     }
                 }
                 return rData;
             },
             searchTitle: function() {
                 if (0 == this.searchWord.length) {
                     this.switchTo('all');
                     return;
                 }
                 $('#use4DownKeyBoard').focus();
                 this.reInit();
                 this.displayData = 'search';
                 this.gettingData = true;
                 $.ajax({
                     url: _api3._search,
                     methods: 'get',
                     data: {
                         type: 'title',
                         key: encodeURIComponent(this.searchWord),
                         page_num: 1,
                         actType: 1
                     },
                     timeout: 10000,
                     error: function(jqXHR, textStatus) {
                         var et = '服务器错误 请重新尝试';
                         if (textStatus === 'timeout') {
                             et = '请求超时 请重新尝试';
                         }
                         $('.popToast').html(et).fadeIn();
                         setTimeout(function() {
                             $('.popToast').fadeOut();
                         }, 2500);
                     },
                     success: function(data) {
                         if (1 == data.state) {
                             $('.popToast').html(data.msg).fadeIn();
                             setTimeout(function() {
                                 $('.popToast').fadeOut();
                             }, 2500);
                             return;
                         }

                         this.hasNextIndex = data.next_state;
                         this.actData = this.packData(data);
                         this.showAct();
                     }.bind(this),
                     complete: function() {
                         setTimeout(function() {
                             this.gettingData = false;
                         }.bind(this), 400);

                     }.bind(this)
                 })
             },
             'reInit': function() {
                 this.show = true;
                 this.actData = [];
                 this.index = 1;
                 this.loading = false;
                 this.hasNextIndex = false;
                 this.gettingData = false;
             },
             "switchTo": function(tab) {
                 this.searchWord = '';
                 this.reInit();

                 var aid = '';
                 this.currentTab = tab;
                 this.displayData = tab;
                 if ('all' == tab) {
                     aid = '';
                 }
                 this.gettingData = true;
                 $.ajax({
                     method: 'post',
                     url: _api3._activity_review,
                     data: {
                         type: this.currentTab,
                         page_num: 1,
                         activity_id: aid
                     },
                     timeout: 10000,
                     error: function(jqXHR, textStatus) {
                         var et = '服务器错误 请重新尝试';
                         if (textStatus === 'timeout') {
                             et = '请求超时 请重新尝试';
                         }
                         $('.popToast').html(et).fadeIn();
                         setTimeout(function() {
                             $('.popToast').fadeOut();
                         }, 2500);
                     },
                     success: function(data) {
                         if (1 == data.state) {
                             $('.popToast').html(data.msg).fadeIn();
                             setTimeout(function() {
                                 $('.popToast').fadeOut();
                             }, 2000);
                             return;
                         }
                         var acts = data.activity_list;
                         this.hasNextIndex = data.next_state;
                         this.actData = this.packData(data);
                         if (acts.length > 0) {
                             this.showAct();
                         }
                     }.bind(this),
                     complete: function() {
                         setTimeout(function() {
                             this.gettingData = false;
                         }.bind(this), 400);
                     }.bind(this)
                 });

             },
             "scrollData": function(event) {
                 if (!this.hasNextIndex) {
                     return;
                 }
                 var $target = $(event.currentTarget);
                 if (!this.loading && $target.height() + $target.scrollTop() > $target.get(0).scrollHeight - 20) {
                     this.loading = true;
                     this.index++;

                     var _url = _api3._activity_review;
                     var _method = 'post';
                     var _data = {
                         type: this.currentTab,
                         page_num: this.index,
                         activity_id: 'my' == this.currentTab ? _post._id : 'all'
                     }

                     if ('search' == this.displayData) {
                         var _url = _api3._search;
                         var _method = 'get';
                         var _data = {
                             type: 3,
                             key: this.searchWord,
                             page_num: this.index
                         }
                     }
                     this.gettingData = true;
                     $.ajax({
                         url: _url,
                         method: _method,
                         data: _data,
                         success: function(data) {
                             if (1 == data.state) {
                                 $('.popToast').html(data.msg).fadeIn();
                                 setTimeout(function() {
                                     $('.popToast').fadeOut();
                                 }, 2500);
                                 return;
                             }
                             this.hasNextIndex = data.next_state;
                             var addedData = this.packData(data);
                             for (var i = 0; i < addedData.length; i++) {
                                 this.actData.push(addedData[i]);
                             }
                         }.bind(this),
                         timeout: 10000,
                         error: function(jqXHR, textStatus) {
                             this.index--;
                             var et = '服务器错误 请重新尝试';
                             if (textStatus === 'timeout') {
                                 et = '请求超时 请重新尝试';
                             }
                             $('.popToast').html(et).fadeIn();
                             setTimeout(function() {
                                 $('.popToast').fadeOut();
                             }, 2500);
                         }.bind(this),
                         complete: function() {
                             this.loading = false;
                             setTimeout(function() {
                                 this.gettingData = false;
                             }.bind(this), 400);
                         }.bind(this)
                     })
                 }
             },
             "showAct": function() {
                 this.show = true;
                 $('html').addClass('lock');
                 $('body').addClass('lock');
             },
             "hideAct": function() {
                 $('html').removeClass('lock');
                 $('body').removeClass('lock');
                 this.show = false;
                 $(window).scrollTop(wsh);
             }
         }
     });
     Vue.component('cmp-ia', insertAct);

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
     var stopProp = function(e) {
         if ($('.insertReview').length > 0) {
             if (e.target.classList.contains('insertReview')) {
                 $(e.target).remove();

                 $('body').off('touchstart touchmove touchend', stopProp);
             }
         }

     }
 })();
