
$('#edit').froalaEditor({
  toolbarButtonsXS: ['bold', 'color', 'fontSize', 'align', 'insertImage2', 'insertLink', 'insertReview','insertVideo'],
  language: 'zh_cn',
  imageButtons: ["floatImageLeft", "floatImageNone", "floatImageRight", "linkImage", "replaceImage", "removeImage"]
});
var startTime;
var bTime = new Date();
var datepickerNormalStart=false;
var cTime = new Date(bTime.getFullYear() + '-' + bTime.getMonth() + '-' + bTime.getDate() + " " + bTime.getHours() + ":00");
var $timePicker = $('.timepicker').pickatime({
  /*min:cTime,*/
  format: 'HH:i',
  onOpen:function(){
    if(!datepickerNormalStart){
        $timePicker[0].value="";
        return;
    }
  },
  onClose: function() {
    datepickerNormalStart=false;
    if (!$timePicker[0].value) {
      return;
    }
    var date=$datePicker[0].value;
    if(date.split(' ').length>1){
      return;
    }
    startTime = $datePicker[0].value += " " + $timePicker[0].value;

        //reInitTime();
      }
    });
if($timePicker.length>0){
  $timePicker.pickatime('picker').set('disable', [
    { from: [0, 0], to: [5, 30] }
    ]);
}
var $datePicker = $('.datepicker').pickadate({
  format: 'yyyy-mm-dd',
  min: new Date(),
  formatSubmit: 'yyyy-mm-dd',
    // min: [2015, 7, 14],
    container: '#container',
    // editable: true,
    closeOnSelect: true,
    closeOnClear: false,
    onOpen: function() {
      datepickerNormalStart=true;
      $datePicker[0].value = "";
    },
    onClose: function() {
      if (!$datePicker[0].value) {
        return;
      }
      setTimeout(function(){
        $timePicker.click();
      },0);
    }
  });

var $timePicker2 = $('.timepicker2').pickatime({
  format: 'HH:i',
  onOpen:function(){
    if(!datepickerNormalStart){
      $timePicker2[0].value="";
        return;
    }
  },
  onClose: function() {
    datepickerNormalStart=false;
    if (!$timePicker2[0].value) {
      return;
    }
    var date=$datePicker2[0].value;
    if(date.split(' ').length>1){
      return;
    }
    $datePicker2[0].value += " " + $timePicker2[0].value
  }
});
if($timePicker2.length>0){
  $timePicker2.pickatime('picker').set('disable', [
    { from: [0, 0], to: [5, 30] }
    ])
};
var $datePicker2 = $('.datepicker2').pickadate({
  format: 'yyyy-mm-dd',
  min: new Date(),
  formatSubmit: 'yyyy-mm-dd',
    // min: [2015, 7, 14],
    container: '#container2',
    // editable: true,
    closeOnSelect: true,
    closeOnClear: false,
    onOpen: function() {
       datepickerNormalStart=true;
      $datePicker2[0].value = "";
    },
    onClose: function() {
      if (!$datePicker2[0].value) {
        return;
      }
      setTimeout(function(){
        $timePicker2.click();
      },0);
    }
  });

setTimeout(function() {
    /*if(location.search){
      $('.fr-view').html(localStorage.post_party_content_101_m);
    }*/
    $('.fr-view').attr('tabIndex', 0).blur(function() { 
      _postTemp._set('post_party_content_101', $('.fr-view').html());
    });

    $('.fr-placeholder').remove();
  }, 0);

function open(elem) {
  if (document.createEvent) {
    var e = document.createEvent("MouseEvents");
    e.initMouseEvent("mousedown", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
    elem[0].dispatchEvent(e);
  } else if (element.fireEvent) {
    elem[0].fireEvent("onmousedown");
  }
}


$('.picker').on('touchmove', function(e) {

  var target = e.target.classList;
  if (!target.contains('picker__list-item')) {
    console.log(target);
    e.preventDefault();
  }

});
