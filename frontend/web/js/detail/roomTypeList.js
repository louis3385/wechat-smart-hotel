/**
 * Created by DongpoLiu on 12/29/16.
 */
(function() {
    var tmpl = "<div class=\"room_detail_v\" v-if=\"roomTypes.length>0\">\n\t\t\t\t<div class=\"banner_room_dv\">\n\t\t\t\t\t<span class=\"icon_bc_dv\"></span>\n\t\t\t\t\t<span class=\"txt_bc_dv\">Room Type</span>\n\t\t\t\t</div>\n\t\t\t\t<div class='container_room_dv'>\n\t\t\t\t\t<div class=\"leaf_room_dv\" v-for=\"roomType in roomTypes\">\n                    <div class=\"banner_leaf_cdv\">\n                    \t<span class=\"dot_banner_lcdv\"></span>\n                        <span class=\"title_banner_lcdv\">{{roomType._name}}</span>\n                        <span class=\"price_banner_lcdv\">{{0==roomType._roomTypePrice?'Free':'￥'+roomType._roomTypePrice}}</span>\n                    </div>\n                    <div class=\"info_leaf_cdv\">\n                        <div>\n                        \t{{payItemStatus(roomType)}}\n                        </div>\n                    </div>\n                \t</div>\n\t\t\t\t</div>\n            </div>";
    var rt = Vue.extend({
        template: tmpl,
        created: function() {
            if ("undefined" != typeof(_info)) {
                this.roomTypes = _info._roomTypes;
            }
        },
        methods: {
            decodeTxt: function(txt) {
                return decodeURIComponent(txt);
            },
            "payItemStatus": function(roomType) {
                if (0 == roomType._remainingCount) {
                    if (0 != roomType._payingTicks) {
                        return 'No Rooms Now（'+ roomType._payingTicks + 'guests are paying）';
                    } else {
                        return 'No Rooms Now';
                    }
                }
                if (0 != roomType._payingTicks) {
                    return 'Remaining11 ' + roomType._remainingCount + ' rooms ' + roomType._allTicks + ' guest ' + roomType._payingTicks + ' guests are paying';
                } else {
                    return 'Remaining222 ' + roomType._remainingCount + ' rooms ' + roomType._allTicks + ' guest';
                }
            }
        },
        data: function() {
            return {
                roomTypes: []
            }
        }
    });
    Vue.component('cmp-rt', rt);
})();
