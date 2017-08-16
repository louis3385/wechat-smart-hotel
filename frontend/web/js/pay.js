/**
 * Created by ws on 8/17/16.
 */
$(function () {
    function getDomain() {
        return document.domain.match(/\.(\w+\.\w+)$/)[1];
    }

    var memberId = $("#member_id").val();
    $('#qrcode').qrcode({
        width: 180,
        height: 180,
        text: "http://crm-internal." + getDomain() + "/profile?memberId=" + memberId
    });

    function getQrcode() {
        $('#qrcode').empty().qrcode({
            width: 180,
            height: 180,
            text: "http://crm-internal." + getDomain() + "/profile?memberId=" + memberId
        });
        // $("#msg").html("已更新");
        $("#msg").css("display", "block");
    }

    clearInterval(timeTicket);
    var timeTicket = setInterval(function () {
        getQrcode();
    }, 60000);
});
