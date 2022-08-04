let clientIdCardCity = $('[name="client_idcard[city_id]"]').val();
let clientIdCardPermanentAddress = $('[name="client_idcard[address]"]').val();
let clientIdCardPostCode = $('[name="client_idcard[post_code]"]').val();

let clientAddressCity = $('[name="client_address[city_id]"]').val();
let clientCurrentAddress = $('[name="client_address[address]"]').val();
let clientPostCode = $('[name="client_address[post_code]"]').val();

onToggleHideAddress();

$('#toggle-address').change(function () {
    let toggleState = $(this).prop('checked');
    if (toggleState === true) {
        onToggleHideAddressNullValue();
    } else {
        if (clientCurrentAddress === "") {
            let clientIdCardCity = $('[name="client_idcard[city_id]"]').val();
            let clientIdCardPermanentAddress = $('[name="client_idcard[address]"]').val();
            let clientIdCardPostCode = $('[name="client_idcard[post_code]"]').val();
            $('[name="client_address[city_id]"]').selectpicker('val', clientIdCardCity);
            offToggleShowAddressValue(clientIdCardPermanentAddress, clientIdCardPostCode, clientIdCardCity);
        } else {
             $('[name="client_address[city_id]"]').selectpicker('val', clientAddressCity);
            offToggleShowAddressValue(clientCurrentAddress, clientPostCode, clientAddressCity);
        }

    }
})

if (clientCurrentAddress != clientIdCardPermanentAddress || clientPostCode != clientIdCardPostCode || clientAddressCity != clientIdCardCity) {
    offToggleShowAddress();
}

function onToggleHideAddressNullValue() {
    $('#current-address').hide();
    $('[name="client_address[address]"]').val('').attr("disabled", true);
    $('[name="client_address[post_code]"]').val('').attr("disabled", true);
    $('[name="client_address[city_id]"]').val('').attr("disabled", true);
}

function offToggleShowAddressValue(clientCurrentAddress, clientPostCode, clientAddressCity) {
    $('#current-address').show();
    $('[name="client_address[address]"]').val(clientCurrentAddress).attr("disabled", false);
    $('[name="client_address[post_code]"]').val(clientPostCode).attr("disabled", false);
    $('[name="client_address[city_id]"]').val(clientAddressCity).attr("disabled", false);
}

function onToggleHideAddress() {
    $('#current-address').hide();
    $('#toggle-address').bootstrapToggle('on');
}

function offToggleShowAddress() {
    $('#current-address').show();
    $('#toggle-address').bootstrapToggle('off');
}
