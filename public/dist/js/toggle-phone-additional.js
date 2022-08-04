$(document).ready(function () {
    let phoneAdditionalButton = $('#toggle-phone-additional');
    let phoneAdditionalContainer = $('#toggle-phone-additional-input-container');
    let phoneCounter = 0;

    phoneAdditionalButton.click(function () {
        if (phoneCounter === 4) {
            phoneAdditionalButton.prop('disabled', true);
            phoneAdditionalButton.html('<i class=\'fas fa-times font-10\'></i>');
            phoneAdditionalContainer.append('<div id="phoneAdditional' + phoneCounter + '" class="phoneAdditionalInputContainer"><input name="client[phone' + phoneCounter + ']" class="form-control w-75 mt-3" minlength="7" /> <button class="btn btn-secondary mt-3 w-15 phoneAdditionalDelete" type="button"><i class="fas fa-trash-alt font-10"></i></button></div>');
            phoneAdditionalContainer.append('<p class="phoneAdditionalWarning font-10 text-danger">Достигнат максимум от телефонни номера</p>');
            phoneCounter++;
        } else {
            phoneAdditionalContainer.append('<div id="phoneAdditional' + phoneCounter + '" class="phoneAdditionalInputContainer"><input name="client[phone' + phoneCounter + ']" class="form-control w-75 mt-3" minlength="7" /> <button class="btn btn-secondary mt-3 w-15 phoneAdditionalDelete" type="button"><i class="fas fa-trash-alt font-10"></i></button></div>');
            phoneCounter++;
            phoneAdditionalButton.removeAttr('disabled');
        }
    })

    $(phoneAdditionalContainer).on("click", ".phoneAdditionalDelete", function (e) {
        e.preventDefault();
        $(this).parent('div').remove();
        $('.phoneAdditionalWarning').remove();
        phoneCounter--;
        phoneAdditionalButton.removeAttr('disabled');
        phoneAdditionalButton.html('<i class=\'fas fa-plus font-10\'></i>');
    })

});
