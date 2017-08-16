/**
 * 我发布的数据
 * ms-controller="manager_party_list_controller"
 * ms-repeat-el="manager_party_list"   我发布的信息数据
 *
 * 详情参考接口文档 api.open.user.manager_pub_info_all
 */
var manager_party_list_model = avalon.define({ //avalon model定义方式
    $id: "manager_party_list_controller",
    page_num: 1,
    next_page: 1,
    //是否包含下一页  0-无  1-有
    manager_party_list: [],
    is_has_data: 2,
    //是否有数据 0-未初始化 1-有 2-无
    pay_count: 0,
    //支付人数
    join_count: 0,
    //报名人数
    display_content:false,



    gotoSignIn:function(name,er){
        var target=er.target;
        var classList=target.classList;
        
        if(classList.contains('n2s')&&!classList.contains('signin')){
           layer.open({
            content: '确认<span style="color:#FF3300">'+name+"</span>已经来了，并帮TA签到吗？",
            btn: ['确认', '取消'],
            style:"70%",
            shadeClose: true,
            yes: function(index){
             $.post("/applicant/check-in",
             {
                applicant_id:target.dataset.ai
            },function(data){
                layer.close(index);
                if(data.result){
                    target.classList.remove("n2s");
                    target.classList.add("signin");
                }
            });
         }, 
         no: function(){
            layer.open({content: '你选择了取消', time: .5});
        }
    });   
       }
   },
    /**
     * 查询下一页数据
     * @param {Object} callback 回调函数
     */
     queryNextPage: function(info_id, info_type, callback) {
        var page_num = manager_party_list_model.page_num + 1; //当前页+1
        manager_party_list_model.queryList(page_num, info_id, info_type, callback);
    },
    /**
     * 循环结束之后的回调函数，
     */
     repeatCallback: function() {

     },
    /**
     * 查询我发布的数据
     * @param {Object} page_num 页码
     * @param {Object} callback 回调函数,回传是否有下一页
     */
     queryList: function(page_num, callback) {

        manager_party_list_model.page_num = page_num;

        require(['avalon', "mmRequest"], function(avalon) { //使用avalon自带的ajax需在使用前加载mmRequest
            avalon.ajax({
                type: "get",
                url: "/post/api:6?page_num=" + page_num,
                dataType: 'json',
                cache: false
            }).done(function(data) {
                //只有第一页加载全部数据
                manager_party_list_model.display_content=true;
                if (page_num == 1) {
                    if (data.manager_party_list != "") {
                        manager_party_list_model.manager_party_list = data.manager_party_list;
                        manager_party_list_model.pay_count = data.pay_count;
                        manager_party_list_model.join_count = data.join_count;
                        manager_party_list_model.is_has_data = 1;

                    } else {
                        manager_info_nalist_model.manager_party_list = [];
                        manager_party_list_model.is_has_data = 2;
                    }

                } else {
                    if (data.manager_party_list != "") {
                        manager_party_list_model.manager_party_list.pushArray(data.manager_party_list);
                    }
                }
                manager_party_list_model.next_page = data.next_state;
                if (callback != undefined) {
                    callback(data.next_state); //执行回调函数
                }

            });
        });
    }
});

// 来自页面的js
$(document).ready(function(){
    
    //定义回调函数
    manager_party_list_model.repeatCallback = function(){
        _btn_control._init();//循环后执行初始化按钮
        // _btn_control._widthControl();//循环后执行初始化宽度
    }

    var h = $(window).height();
    $(".count_different").css({"padding-top":(h-110)/2+"px","padding-bottom":(h-110)/2+"px"});
    $(".hd_No .c").css({"padding-top":(h-133)/2+"px","padding-bottom":(h-133)/2+"px"});
    pullUpPage._init();
    // _btn_control._widthControl();
    manager_party_list_model.queryList(1,pullUpPage._setIsExec);

});
window.onscroll = function(){
    // _btn_control._widthControl();
};
var _btn_control = {
    _widthControl:function(){//初始化用户名、席位名称宽度控制
        var w = $("body").width();
        $(". ul li .t").each(function(){
            $(this).children(".yhName").width(w-85-$(this).children(".joinPay").width());
            $(this).children(".joinXiwei").width(w-90-$(this).children(".joinTime").width());
        });
        $(".join_txx_list").each(function(){
            if($(this).children("span").html()=="："){
                $(this).remove();
            }
        });
        $(".index_re_outSide ul li .b").each(function(){
            if($(this).children(".join_txx").children("div").length==1 && $(this).children(".join_txx").children("div").children("span").html()=="手机："){
                $(this).children(".btn_control").remove();
                $(this).children(".join_txx").show();
            }
        });
    },
    _init:function(){//初始化按钮
        $(".index_re_outSide ul li").each(function(){
            /*
            $(this).attr("id","yhList"+$(this).index());

            if($(this).find("a").attr("class")!="btn_control thisOver"){
                $(this).find("a").prop('outerHTML', '<a href="javascript:void(0)" ontouchstart="" class="btn_control" onclick="_btn_control._open(\'yhList'+$(this).index()+'\')"><span class="down">展开</span></a>');
            }
            */

        });

        _hdbLoad._run(".hd_list_pic");
    },  
}
var pullUpPage= {
    _next_page: 0,   //是否有下一页   1-有   0-无
    _is_exec_trigger: false, //是否执行查询
    _setIsExec: function(next_page){  //设置是否可以执行触发器
        pullUpPage._is_exec_trigger = true;
        pullUpPage._next_page = next_page;
        $(".jz_More").hide();
    },
    _trigger: function() {
        if( pullUpPage._is_exec_trigger == true && pullUpPage._next_page==1){
            var _scH = $(document).scrollTop(),_wiH = $(window).height(),_liH = $(".index_re_outSide ul li").length * 110;
            if(_scH > (_liH - _wiH)){
                $(".jz_More").show();
                pullUpPage._is_exec_trigger = false;
                manager_party_list_model.queryNextPage(pullUpPage._setIsExec);
            }
        }else if(pullUpPage._next_page==0){
            $(".jz_More").show().html("已全部加载");
        }
    },
    _init: function() {
        $(window).bind("scroll", pullUpPage._trigger);
    }
};