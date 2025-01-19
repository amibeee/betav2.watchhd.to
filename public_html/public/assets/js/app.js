function insertAtCaret(areaId, text) {
		var txtarea = document.getElementById(areaId);
		if (!txtarea) { return; }

		var scrollPos = txtarea.scrollTop;
		var strPos = 0;
		var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
			"ff" : (document.selection ? "ie" : false ) );
		if (br == "ie") {
			txtarea.focus();
			var range = document.selection.createRange();
			range.moveStart ('character', -txtarea.value.length);
			strPos = range.text.length;
		} else if (br == "ff") {
			strPos = txtarea.selectionStart;
		}

		var front = (txtarea.value).substring(0, strPos);
		var back = (txtarea.value).substring(strPos, txtarea.value.length);
		txtarea.value = front + text + back;
		strPos = strPos + text.length;
		if (br == "ie") {
			txtarea.focus();
			var ieRange = document.selection.createRange();
			ieRange.moveStart ('character', -txtarea.value.length);
			ieRange.moveStart ('character', strPos);
			ieRange.moveEnd ('character', 0);
			ieRange.select();
		} else if (br == "ff") {
			txtarea.selectionStart = strPos;
			txtarea.selectionEnd = strPos;
			txtarea.focus();
		}

		txtarea.scrollTop = scrollPos;
	}

var months = 0;
var method = '';
function calculate_price(){

    if(months == 0 || method == '') return;

    $.getJSON(base_url+'ajax/pricecalculator', {'months':months,'method':method}, function(response){

        if(response.success == true)
        {
            $('#price').html(response.data.price.toFixed(2));
        }

    })

}

$('.package').on('change', function(e){
    
    $trigger = $(this);

    var data = {
        active: $trigger.prop('checked')
    }

    $.post(base_url+'ajax/package/'+$trigger.attr('data-package-id')+'/action', data, function(response){
        if(response.success){
            $('#packageOptionSuccessfullyChanged').modal('show');
        }
    })


});
$('#packageOptionSuccessfullyChanged').on('hidden.bs.modal', function (e) {
  
    window.location = window.location;;
  })

$(document).ready(function(){

    $('.paketOption').on('change', function(e){
        
        if($(this).prop('checked')){
            $('#linePrice').text( '€'+ (parseFloat($('#linePrice').text().replace('€', '')) + parseFloat($(this).attr('data-price'))).toFixed(2) );
        }
        else{
            $('#linePrice').text( '€'+ (parseFloat($('#linePrice').text().replace('€', '')) - parseFloat($(this).attr('data-price'))).toFixed(2) );
        }
        
    
    });

    $.material.init();
	
	$('.createUserLine').on('click', function(e){

        if(display_name = window.prompt('Bitte gebe einen Namen für die neue Line an')){


            $.post(base_url+'ajax/line/create', {display_name:display_name}, function(response){
			
                if(response.success)
                {
                    alert('Line wurde erzeugt.');
                }
                else
                {
                    alert('Line konnte nicht erzeugt werden.');
                }
                
            })


        }
        
		
		

		
    });
    
    $('#linePasswordEditorModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var username = button.data('username') // Extract info from data-* attributes
        var password = button.data('password') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('.modal-body input[name="username"]').val(username)
        modal.find('.modal-body input[name="password"]').val(password)
      })
	
    $('.changeVersionBtn').on('click', function(e){

        $('code').html( m3u+'?version='+$(this).attr('data-type') );

    });

    $('[data-toggle="tooltip"]').tooltip();

		$('select[id=channel]').on('change', function(e){
				window.location = $('select[id=channel] option:selected').val();
		});

        $('.declinePaymentButton').on('click', function(e){

            $('#declinePaymentForm').attr('action', $(this).attr('data-url'));
            $('#declinePaymentModal').modal('show');

        });

$("#toggleCheckboxes").on('click', function() {
        var checkBoxes = $("input[class=awesome_checkbox");
        checkBoxes.prop("checked", !checkBoxes.prop("checked"));
    });  

    $('.checkoutInitBtn').on('click', function(e){

        e.preventDefault();

        $.post(base_url+'ajax/signup', $('#signupForm').serialize(), function(response){

            $('#checkoutDialog').modal('show');

            if(response.success)
            {

                $('#checkoutDialogMessage').html('<div class="alert alert-success">Dein Benutzerkonto wurde erstellt.</div>');
                $('.modal-title').html('Checkout');



            }
            else
            {
                $('#checkoutDialogMessage').html('<div class="alert alert-danger">'+response.message+'</div>');
            }

        })

    });

    $('input[name=months]').on('change', function(){

        months = $(this).val();
        calculate_price();
    });

    $('input[name=method]').on('change', function(){

        method = $(this).val();
        calculate_price();
    });

    $('.openPayoutRequestModal').on('click', function(e){
        e.preventDefault();
        $('#wallet').modal('show');
    });

    $(document).on('click', '.submitPayoutRequest', function(e){

       $('#wallet').modal('hide');
       $('#wallet_id').val($('#modal_wallet_id').val());
       $('#submitBtn').trigger('click');

    });

    $('.showpassword').off('click').on('click', function(e){
        $('input[name=password]').attr('type', 'text');
    });

    if($('.channel').length)
    {
        $('.channel').matchHeight();
    }

    var recorder_channel_id = null;
    var recorder_day = null;
    var recorder_step = 'one';

    $('.selectChannelBtn').off('click').on('click', function(){

        recorder_step = 'one';
        $('#channelRecorderDays').html('');

        $('.selectChannelBtn').removeClass('btn-success');
        $('.selectChannelBtn').addClass('btn-default');

        $(this).addClass('btn-success');
        $('.recorderNextStepBtn').removeAttr('disabled');
        recorder_channel_id = $(this).attr('data-channel');

        $('.recorderNextStepBtn').trigger('click');

    });

    $(document).off('click', '.selectDayBtn').on('click', '.selectDayBtn', function(e){


        recorder_step = 'two';
        $('#channelRecorderProgramme').html('');

        $('.selectDayBtn').removeClass('btn-success');
        $('.selectDayBtn').addClass('btn-default');

        $(this).addClass('btn-success');
        recorder_day = $(this).attr('data-day');

        $('.recorderNextStepBtn').trigger('click');

    });

    $(document).off('click', '.selectProgrammBtn').on('click', '.selectProgrammBtn', function(e){

        $(this).find('.panel-body').addClass('active');
        //recorder_day = $(this).attr('data-day');

        //$('.recorderNextStepBtn').trigger('click');

        $('.recorder-step-four').show();
        window.location.hash = '#thanks';


    });

    $('.recorderNextStepBtn').off('click').on('click', function(e){

        switch(recorder_step)
        {

            case 'one':

                if(recorder_channel_id == 0) return alert('Du musst zuerst einen Kanal auswählen.');


                $.getJSON(base_url+'ajax/channel/recorder/retrieveavailabledays', {'channel_id':recorder_channel_id}, function(response){

                   $.each(response.data.days, function(index, day){
                    $('#channelRecorderDays').append('<p><a href="#programme" data-day="'+day+'" class="btn btn-raised btn-default btn-lg btn-block selectDayBtn">'+day+'</a></p>');
                   });

                }).promise().done(function(){
                    recorder_step = 'two';
                });

            break;

            case 'two':

                $.getJSON(base_url+'ajax/channel/recorder/retrieveprogrammeforday', {'channel_id':recorder_channel_id,'day':recorder_day}, function(response){

                   $.each(response.data.programme, function(index, programm){


                    date = new Date(programm.start * 1000),

                    $('#channelRecorderProgramme').append('<div class="col-md-3 selectProgrammBtn channel" data-start="'+programm.start+'" data-stop="'+programm.stop+'"><div class="panel panel-default"><div class="panel-body"><h4><span class="label label-default">'+date.getHours()+':'+date.getMinutes()+'</span> '+programm.title.de+'</h4></div></div></div>');
                    //$('#channelRecorderDays').append('<p><button data-day="'+day+'" class="btn btn-raised btn-default btn-lg btn-block selectDayBtn">'+day+'</button></p>');
                   });

                }).promise().done(function(){
                    recorder_step = 'three';
                });

            break;

        }

    });

    $('.addRecord').off('click').on('click', function(e){

        $btn = $(this);

        $.post(base_url+'ajax/channel/recorder/create', {'channel_id':$btn.attr('data-record-channel-id'),'title':$btn.attr('data-record-title'),'start':$btn.attr('data-record-start'),'end':$btn.attr('data-record-end')}, function(response){

            if(response.success)
            {



            }
            else
            {

            }

        });

    });

    $('.channelSwitch').off('click').on('click', function(e){
        window.location = $("select[name=channel] option:selected").val();
    });

    $('.insertSmilie').off('click').on('click', function(e){
        insertAtCaret('channelMessage', ' '+$(this).attr('data-insert')+' ');
    });

    $('.toggleChannelChatSmilies').off('click').on('click', function(e){

        $('#channelChatSmilies').toggle();

    });

    $('.channel')
    .on('mouseover', function(e){
        //alert('mousOVER');
    })
    .on('mouseout', function(e){
       //alert('mouseout');
    });

    $('.sendChannelMessage').off('click').on('click', function(){

        var channelMessage = $('input[name=channelMessage]');
        var channel_id = $(this).attr('data-channel-id');

        if(channelMessage.val().length == 0)
        {
            alert('Bitte gebe eine Nachricht an');
            return;
        }

        $.post(base_url+'ajax/channel/message/push', {'channel_message':channelMessage.val(),'channel_id':channel_id}, function(response){

            if(response.success)
            {

                channelMessage.val('');
                refreshChannelChat(channel_id);

            }
            else
            {

            }

        });

    });

    $(':checkbox[id=checkAll]').click (function () {
        $(':checkbox[class=check]').prop('checked', this.checked);
    });

    $('.confirm').off('click').on('click', function(e){
        e.preventDefault();

        $('.modal-title').html('Would you like to proceed?');
        $('.modal-body').html('Would you like to proceed?');
        $('.modal-footer').html('<form action="'+$(this).attr('href')+'" method="post"><input type="submit" value="yes, do it!" class="btn btn-danger"><input type="button" value="Please don\'t!" class="btn" id="closeBtn" /></form>');
        $('#modal').modal('show');

    });

    $('#charge').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var uid = button.attr('data-uid') // Extract info from data-* attributes
        var modal = $(this)
        modal.find('input[id=user_id]').val(uid)
    });

    $('.setPremiumTime').off('click').on('click', function(e){

        $('#bonustime').val( $(this).attr('data-premium-bonus-time') );

    });

    $('.setCoupons').off('click').on('click', function(e){

        $('#coupons').val( $(this).attr('data-coupons') );

    });

    $('.creditBonusTime').off('click').on('click', function(e){

        $('#creditbonustimemodalmessage').empty();

        if($('#bonustime').val().length == 0 || $('#user_id').val().length == 0)
        {

            $('#creditbonustimemodalmessage').html('<div class="alert alert-danger">Bitte wähle die Anzahl der Tage aus die dem Benutzer gutgeschrieben werden sollen.</div>');
            return;

        }

        var user_id = $('#user_id').val();
        var bonustime = $('#bonustime').val();

        $.post(base_url+'/ghru4pxsa3/ajax/bonustime', {'user_id':user_id,'bonustime':bonustime}, function(response){

            if(response.success)
            {
                $('#creditbonustimemodalmessage').html('<div class="alert alert-success">Dem Benutzer wurden die ausgewählte Anzahl an Tagen gutgeschrieben.</div>');
            }
            else
            {
                $('#creditbonustimemodalmessage').html('<div class="alert alert-danger">'+response.message+'</div>');
            }

        });

    });

});
