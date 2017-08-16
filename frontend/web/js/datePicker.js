/**
 * Created by DongpoLiu on 1/12/17.
 */
$('#datePicker').datetimepicker({
    date: new Date(),
    viewMode: 'YMDHM',
    onDateUpdate: function(){
        $('#date-text1-2').text(this.getText());
        $('#date-text-ymd1-2').text(this.getText('yyyy-MM-dd'));
        $('#date-value1-2').text(this.getValue());
    }
});