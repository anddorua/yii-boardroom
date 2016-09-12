/**
 * Created by andrey on 18.08.16.
 */
function AppChangeController(modalId, formId, saveBtnId, delBtnId, appItemClass, bookViewUrlTemplate, allowedOptionsUrlTemplate, submitUrlTemplate, deleteTemplate) {
    var appFormSubmitted = false;
    var appId = null;
    var GLOBAL_FIELD_NAME = 'global';

    function open (el) {
        appId = el.target.dataset.appId;
        var bookViewUrl = bookViewUrlTemplate.replace('placeholder', appId);
        var allowedOptionsUrl = allowedOptionsUrlTemplate.replace('placeholder', appId);
        $.when($.ajax(bookViewUrl), $.ajax(allowedOptionsUrl))
            .done(function (booking, allowedOptions) {
                fillBooking(booking[0], allowedOptions[0]);
            });
        appFormSubmitted = false;
        $('#' + modalId).modal({});
        return false;
    }

    function getForm() {
        return $('#' + formId);
    }

    function fillBooking (booking, allowedOptions)
    {
        var form = getForm();
        $('#' + modalId).find('#appChangeModalLabel').html('B.B. Details on ' + booking.date);
        form.find('#bookingform-appid').val(booking.appId);
        form.find('#bookingform-timebegin').val(booking.timeBegin);
        form.find('#bookingform-timeend').val(booking.timeEnd);
        form.find('#bookingform-comment').val(booking.comment);
        form.find('#bookingform-applytoall').prop('checked', false);
        form.find('#app-submitted').html(booking.submitted);
        form.find('#bookingform-employeecode').val(booking.employeeCode);
        if (allowedOptions.applyToAll) {
            form.find('#applytoall-wrapper').removeClass('hidden');
        } else {
            form.find('#applytoall-wrapper').addClass('hidden');
        }
        form.find('#bookingform-timebegin, #bookingform-timeend, #bookingform-comment, #bookingform-employeecode, #bookingform-applytoall').prop("disabled", !allowedOptions.editable);
        form.find('#bookingform-employeecode').prop("disabled", !(allowedOptions.editable && allowedOptions.changeEmployee));
        if (allowedOptions.editable) {
            $('#' + saveBtnId).removeClass('hidden');
        } else {
            $('#' + saveBtnId).addClass('hidden');
        }
        form.yiiActiveForm('resetForm');
    }

    function setSummaryErrorMessage(msg) {
        var GLOBAL_FIELD_ID = '-' + GLOBAL_FIELD_NAME;
        var jqForm = getForm();
        if (msg != '') {
            jqForm.yiiActiveForm('add', {
                'id': GLOBAL_FIELD_ID,
                'name': GLOBAL_FIELD_NAME,
                'container': null,
                'input': null,
                'error': null
            });
        }
        var msgObject = {};
        msgObject[GLOBAL_FIELD_ID] = msg == '' ? '' : [msg];
        jqForm.yiiActiveForm('updateMessages', msgObject, true);
        if (msg != '') {
            jqForm.yiiActiveForm('remove', GLOBAL_FIELD_ID);
        }
    }

    /*
     see https://github.com/samdark/yii2-cookbook/blob/master/book/forms-activeform-js.md
     for JS usage of ActiveForm
     */
    function setErrorMessages(msgObject) {
        var GLOBAL_ERROR_ID = '-' + GLOBAL_FIELD_NAME;
        var jqForm = getForm();
        var globalId = $.map(msgObject, function(value, index){ return index; })
            .filter(function(controlId){ return controlId.indexOf(GLOBAL_ERROR_ID) != -1; });
        var hasGlobalError = globalId.length > 0;
        if (hasGlobalError) {
            jqForm.yiiActiveForm('add', {
                'id': globalId[0],
                'name': GLOBAL_FIELD_NAME,
                'container': null,
                'input': null,
                'error': null
            });
        }
        jqForm.yiiActiveForm('updateMessages', msgObject, hasGlobalError);
        if (hasGlobalError) {
            jqForm.yiiActiveForm('remove', globalId[0]);
        }
    }

    function submitAppChange() {
        setSummaryErrorMessage('');
        var jqForm = getForm();
        jqForm.data('yiiActiveForm').submitting = true;
        $.ajax(submitUrlTemplate, {
                type: 'POST',
                data: new FormData(jqForm[0]),
                mimeType:"multipart/form-data",
                contentType: false,
                cache: false,
                processData:false
            })
            .then(function(data){
                var response = JSON.parse(data);
                if ($.type(response) == 'array' && response.length == 0) {
                    //closing form with success
                    appFormSubmitted = true;
                    $('#' + modalId).modal('hide');
                } else {
                    setErrorMessages(response);
                }
            });

        return false;
    }

    function deleteApp() {
        if (!confirm("Are you sure to delete appointment?")) {
            return false;
        }

        var form = getForm();
        form.find('#bookingform-applytoall').prop('checked');

        var url = deleteTemplate
            .replace('placeholder', appId)
            .replace('set-placeholder', form.find('#bookingform-applytoall').prop('checked') ? 'all' : 'one');
        $.ajax(url, { type: 'DELETE'})
            .then(function(data){
                appFormSubmitted = true;
                $('#' + modalId).modal('hide');
                //console.log(data);
            }, function(error){
                setSummaryErrorMessage(error.responseText);
            });
    }

    function onAppChangeClosed (e) {
        if (appFormSubmitted) {
            location.reload();
        }
    }


    $('#' + saveBtnId).click(submitAppChange);
    $('#' + delBtnId).click(deleteApp);
    $('#' + modalId).on("hidden.bs.modal", onAppChangeClosed);
    $('.' + appItemClass).click(open);
}