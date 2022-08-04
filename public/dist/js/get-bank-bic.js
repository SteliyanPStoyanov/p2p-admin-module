$('#client-bank').on('change', function () {
    $.ajax({
        url: urlGetBankBic,
        data: {
            "bank_id": this.value
        },
        type: 'GET',
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
            if (data.bic === undefined) {
                $('#bank-account-bic').val('');
            } else {
                $('#bank-account-bic').val(data.bic);
            }
        }
    });
})
$('#bank-account-iban').on('keyup', function () {
    if (this.value.length > 7) {
        $.ajax({
            url: urlGetBankByIban,
            data: {
                "iban": this.value.toUpperCase()
            },
            type: 'GET',
            headers: {
                "Accept": "application/json",
            },
            success: function (data) {
                if (data.bic === undefined) {
                    $('#bank-account-bic').val('');
                    $('#client-bank').selectpicker('val', 0);
                } else {
                    $('#bank-account-bic').val(data.bic);
                    $('#client-bank').selectpicker('val', data.bank_id);
                }
            }
        });
    }
})
