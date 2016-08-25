/**
 * Created by andrey on 18.08.16.
 */
function AppChangeController(modalId, formId, saveBtnId, appItemClass, bookViewUrlTemplate, allowedOptionsUrlTemplate, submitUrlTemplate) {
    var appFormSubmitted = false;

    function open (el) {
        var bookViewUrl = bookViewUrlTemplate.replace('placeholder', el.target.dataset.appId);
        var allowedOptionsUrl = allowedOptionsUrlTemplate.replace('placeholder', el.target.dataset.appId);
        $.when($.ajax(bookViewUrl), $.ajax(allowedOptionsUrl))
            .done(function (booking, allowedOptions) {
                fillBooking(booking[0], allowedOptions[0]);
            });
        appFormSubmitted = false;
        $('#' + modalId).modal({});
        return false;
    }

    function fillBooking (booking, allowedOptions)
    {
        var form = $('#' + formId);
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

    function submitAppChange() {
        var jqForm = $('#' + formId);
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
                    /*
                     see https://github.com/samdark/yii2-cookbook/blob/master/book/forms-activeform-js.md
                     for JS usage of ActiveForm
                     */
                    var globalId = $.map(response, function(value, index){ return index; })
                        .filter(function(controlId){ return controlId.indexOf('-global') != -1; });
                    var hasGlobalError = globalId.length > 0;
                    if (hasGlobalError) {
                        jqForm.yiiActiveForm('add', {
                            'id': globalId[0],
                            'name': 'global',
                            'container': null,
                            'input': null,
                            'error': null
                        });
                    }
                    jqForm.yiiActiveForm('updateMessages', response, hasGlobalError);
                    if (hasGlobalError) {
                        jqForm.yiiActiveForm('remove', globalId[0]);
                    }
                }
            });

        return false;
    }

    function onAppChangeClosed (e) {
        if (appFormSubmitted) {
            location.reload();
        }
    }


    $('#' + saveBtnId).click(submitAppChange);
    $('#' + modalId).on("hidden.bs.modal", onAppChangeClosed);
    $('.' + appItemClass).click(open);
}