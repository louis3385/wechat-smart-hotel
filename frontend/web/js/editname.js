/**
 * Created by ws on 8/17/16.
 */
$(function () {
    $("#btn_save").click(function(){
        var type=$(this).attr("btn_type");
        var memberId = $("#member_id").val();
        if(type==1){
            var data = $('input[name="realname"]').val();
            if(!data){
                $("#namemsg2").css("display","block");
                return;
            }else if(data.length>6){
                $("#namemsg1").css("display","block");
                return;
            }else{
                $("#namemsg1").css("display","none");
                $("#namemsg2").css("display","none");
            }
        }else if(type==2){
            var data = $('input[name="mobile"]').val();
            if(!(/^1[3|4|5|7|8]\d{9}$/.test(data))){
                $("#mobilemsg").css("display","block");
                return;
            }else{
                $("#mobilemsg").css("display","none");
            }
        }else if(type==3){
            var data = $('input[name="birth"]').val();
        }else if(type==4){
            var data = $('input[name="address"]').val();
            if(data.length>20){
                $("#addressmsg").css("display","block");
                return;
            }else{
                $("#addressmsg").css("display","none");
            }
        }
        var url ="/card/editname";
        $.ajax({
            type: "post",
            url: url,
            // timeout:2000,
            data: {data: data,type:type,memberId:memberId},
            cache: false,
            dataType: "json",
            success: function (data) {
                if (data != null) {
                    var _state = data.state;
                    //请求正常
                    if (_state == '0') {
                        window.history.back();
                    }
                }
            },
            error: function () {
                $("#errormsg").css("display","block");
            }
        })

    });
});
