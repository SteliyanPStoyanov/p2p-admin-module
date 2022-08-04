const PAYMENT_METHOD_BANK = paymentMethodBankId;
const CASH_METHOD_ID = cashMethodId;
const OFFICE_WEB_ID = officeWebId;
const EASY_PAY_METHOD_ID = easyPayMethodId;

let countOffice = $('#countOffices').val();
if (countOffice == 1) {
    $('#offices option[value="' + 1 + '"]').prop('selected', true);
    $('#offices').attr("disabled", "disabled");
    $('#offices').addClass('hideArrowFromSelect');
}

let paymentMethod = $("#payment-method").val();
if (paymentMethod != PAYMENT_METHOD_BANK) {
    hideIban();
}

$("#payment-method").change(function () {
    hideIban();
});

function hideIban() {
    let paymentMethod = $("#payment-method").val();
    if (paymentMethod != PAYMENT_METHOD_BANK) {
        $("#iban").hide();
    } else {
        $("#iban").show();
    }
}

function paymentMethods() {
    let officeVal = $("#offices").val();

    if (officeVal == OFFICE_WEB_ID) {
        $('select[name*="loan[payment_method]"] option[value="3"]').hide();
        $('#payment-method').val('')
        $('#payment-method').val(EASY_PAY_METHOD_ID);
        $('#payment-method').change();
    } else {
        $('select[name*="loan[payment_method]"] option[value="3"]').show();
        $('#payment-method').val('')
        $('#payment-method').val(EASY_PAY_METHOD_ID);
        $('#payment-method').change();
    }

    if (officeVal != OFFICE_WEB_ID) {
        $('select[name*="loan[payment_method]"] option[value="2"]').hide();
        $('#payment-method').val('')
        $("#payment-method").val(CASH_METHOD_ID);
        $("#payment-method").change();
    } else {
        $('select[name*="loan[payment_method]"] option[value="2"]').show();
        $('#payment-method').val('')
        $('#payment-method').val(EASY_PAY_METHOD_ID);
        $('#payment-method').change();
    }
}

$("#offices").change(function () {
    let officeVal = $("#offices").val();
    let productByOffice = $("#productsByOffice");

    $('#nav-tabContent').show('slow');

    paymentMethods();
    officeAjax(urlOffice, officeVal, productByOffice);
});


function reDrawProductSelect(products, productId) {
    let productByOffice = $("#nav-tab");
    productByOffice.empty();

    if (products === undefined || products.length === 0) {
        $('#nav-tabContent').hide();
        $('#nav-tabContent').hide();
        $("#loan-products-warning").show('slow');
        $("#loan-products-warning").html('<div id="loan-products-warning" ' +
            'class="alert alert-warning alert-dismissible bg-warning text-white border-0 fade show" ' +
            ' role="alert"> <button type="button" class="close" data-dismiss="alert" ' +
            'aria-label="Close"> <span aria-hidden="true">×</span>' +
            ' </button><strong>Няма създадени продукти за този офис</strong></div>')
    } else {
        $('#nav-tabContent').show();
        $('#loan-products-warning').hide();
    }

    $.each(products, function (index, value) {
        let selected = (
            value.product_id === productId
                ? true : false
        );

        if (index === 0) {
            productByOffice.prepend($("<a/>", {
                text: value.name,
                'class': 'nav-item nav-link active loan',
                'data-toggle': 'tab',
                'role': 'tab',
                'id': 'productsByOffice',
                'name': 'loan[product_id]',
                product_id: value.product_id
            }));
        } else {
            productByOffice.append($("<a/>", {
                text: value.name,
                'class': 'nav-item nav-link loan',
                'data-toggle': 'tab',
                'role': 'tab',
                'id': 'productsByOffice',
                'name': 'loan[product_id]',
                product_id: value.product_id,
            }));
        }
        $("#loan_product_id").val(value.product_id);
    });

    if (products.length > 2) {
        let calcWidthTabs = ($('#nav-tab').width() / 2) - 2;
        $('.nav-link.loan').innerWidth((calcWidthTabs / (products.length - 1)) - 2);
        $('.nav-link.loan').first().innerWidth(calcWidthTabs);
        let windowWidth = $(window).width();
        if (windowWidth < 1600) {
            $('.nav-link.loan').innerWidth(calcWidthTabs);
        }
    } else {
        let calcWidthTabs = ($('#nav-tab').width() / products.length) - 2;
        $('.nav-link.loan').innerWidth(calcWidthTabs);
    }

    $('.nav-item.nav-link.loan').click(function (e) {
        if (products.length > 2) {
            let calcWidthTabs = ($('#nav-tab').width() / 2) - 2;
            $('.nav-link.loan').innerWidth((calcWidthTabs / (products.length - 1)) - 2);
            $(this).innerWidth(calcWidthTabs);
        }
    });
    productByOffice.show();
    $(".loan").click(function () {
        let productId = $(this).attr('product_id');
        let pin = $("#pin").val();

        $.ajax({
            url: productSettingsUrl,
            method: 'GET',
            data: {productId: productId},
            dataType: 'json',
            success: function (data) {

                let labelSumMin = data.sumMin;
                let labelSumMax = data.sumMax;
                let labelPeriodMin = data.periodMin;
                let labelPeriodMax = data.periodMax;
                let labelPeriod = data.periodLabel;
                let step = data.step;
                let defaultAmount = data.default_amount;
                let defaultPeriod = data.default_period;

                let paymentsSum = $("#paymentsSum");
                let paymentsPeriod = $("#loanPeriod");
                let loanSum = $("#loan_sum");
                let periodSum = $("#loan_period");

                if (pin === undefined) {
                    $("#payment_sum").html(defaultAmount);
                    $("#loanPeriod").html(defaultPeriod);
                    loanSum.attr('min', defaultAmount);
                    loanSum.attr('max', defaultAmount);
                    loanSum.val(defaultAmount);
                    periodSum.attr('min', defaultAmount);
                    periodSum.attr('max', defaultAmount);
                    periodSum.val(defaultPeriod);
                } else {
                    $(".salaryPeriodText").html(labelPeriodMax);
                    paymentsSum.attr('min', labelSumMin);
                    paymentsSum.attr('max', labelSumMax);
                    paymentsSum.val(labelSumMax);
                    paymentsSum.attr('step', step);
                    paymentsPeriod.attr('min', labelPeriodMin);
                    paymentsPeriod.attr('max', labelPeriodMax);
                    paymentsPeriod.val(labelPeriodMax);

                    $("#payment_sum").html(labelSumMax);
                    $("#loanPeriod").html(labelPeriodMax);
                    $("#loan_period").html(labelPeriodMax);
                    $("#paymentsPeriod").html(labelPeriodMax);
                    $("#paymentsPeriod").attr('max', labelPeriodMax);
                    $("#paymentsPeriod").val(labelPeriodMax);

                    loanSum.attr('min', labelSumMin);
                    loanSum.attr('max', labelSumMax);
                    loanSum.val(labelSumMax);

                    periodSum.attr('min', labelPeriodMin);
                    periodSum.attr('max', labelPeriodMax);
                    periodSum.val(labelPeriodMax);


                    if ($("#min-sum").length) {
                        $("#min-sum").remove();
                    }
                    if ($("#min-period").length) {
                        $("#min-period").remove();
                    }

                    $("#loan_product_id").val(productId);
                    if (labelPeriod == "days") {
                        $("#periodLabel").text(loanSettingDay);
                    } else {
                        $("#periodLabel").text(loanSettingMonth);
                    }
                }
            }
        })

    });
}

function officeAjax(urlOffice, officeVal, productByOffice) {
    $.ajax({
        url: urlOffice,
        method: 'GET',
        data: {officeId: officeVal, clientId: $("#clientId").val()},
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                $("#office-danger-alert").html(data.error);
                return;
            }

            productByOffice.empty();
            if (data.length != 0) {

                reDrawProductSelect(
                    data['products'],
                    data['selectedProductId']
                );
            } else {
                productByOffice.hide();
            }
        }
    })
}

$(".loan").click(function () {
    let productId = $("#productsByOffice").val();

    $.ajax({
        url: productSettingsUrl,
        method: 'GET',
        data: {productId: productId},
        dataType: 'json',
        success: function (data) {
            let labelSumMin = data.sumMin;
            let labelSumMax = data.sumMax;
            let labelPeriodMin = data.periodMin;
            let labelPeriodMax = data.periodMax;
            let labelPeriod = data.periodLabel;
            let step = data.step;
            let paymentsSum = $("#paymentsSum");
            let paymentsPeriod = $("#paymentsPeriod");
            let loanSum = $("#loan_sum");

            paymentsSum.attr('min', labelSumMin);
            paymentsSum.attr('max', labelSumMax);
            paymentsSum.val(labelSumMax);
            paymentsSum.attr('step', step);

            paymentsPeriod.attr('min', labelPeriodMin);
            paymentsPeriod.attr('max', labelPeriodMax);
            paymentsPeriod.val(labelPeriodMax);

            $("#payment_sum").html(labelSumMax);
            $("#loanPeriod").html(labelPeriodMax);

            $("#loan_period").attr('max', labelPeriodMax);
            $("#loan_period").val(labelPeriodMax);

            loanSum.attr('min', labelSumMin);
            loanSum.attr('max', labelSumMax);
            loanSum.val(labelSumMax);

            if ($("#min-sum").length) {
                $("#min-sum").remove();
            }
            if ($("#min-period").length) {
                $("#min-period").remove();
            }

            if (labelPeriod == "days") {
                $("#periodLabel").text(loanSettingDay);
            } else {
                $("#periodLabel").text(loanSettingMonth);
            }
        }
    })

});

$("#loan_sum").keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        return false;
    }
    setTimeout(function () {
        let loanSum = $("#loan_sum");
        let paymentsSum = $("#paymentsSum");
        let paymentsSumLabel = $("#payment_sum");
        let value = parseInt(loanSum.val());
        let maxValue = parseInt(loanSum.attr('max'));
        let minValue = parseInt(paymentsSum.attr('min'));
        if ($("#min-sum").length) {
            $("#min-sum").remove();
        }

        if (value > maxValue) {
            loanSum.val(maxValue);
            paymentsSum.val(maxValue);
            paymentsSumLabel.html(maxValue);
            return false;
        }

        if (value < minValue || isNaN(value)) {
            loanSum.after('<div id="min-sum" class="text-danger font-10">' + loanMinSum + ' : ' + minValue + '</div>');
            return false;
        } else {
            $("#min-sum").remove();
        }

        paymentsSum.val(value);
        paymentsSumLabel.html(value);

    }, 1);
})

$("#loan_period").keydown(function (event) {
    if (event.keyCode == 13) {
        event.preventDefault();
        return false;
    }

    setTimeout(function () {
        let loanPeriod = $("#loan_period");
        let paymentsPeriod = $("#paymentsPeriod");
        let loanPeriodLabel = $("#loanPeriod");
        let value = parseInt(loanPeriod.val());
        let maxValue = parseInt(loanPeriod.attr('max'));
        let minValue = parseInt(paymentsPeriod.attr('min'));

        if ($("#min-period").length) {
            $("#min-period").remove();
        }

        if (value > maxValue) {
            loanPeriod.val(maxValue);
            paymentsPeriod.val(maxValue);
            loanPeriodLabel.html(maxValue);
            return false;
        }

        if (value < minValue || isNaN(value)) {
            loanPeriod.after('<div id="min-period" class="text-danger font-10">' + loanMinPeriod + ' : ' + minValue + '</div>');
            if (isNaN(value)) {
                loanPeriod.val(minValue);
                paymentsPeriod.val(minValue);
                loanPeriodLabel.html(minValue);
            }
            return false;
        } else {
            $("#min-period").remove();
        }

        paymentsPeriod.val(value);
        loanPeriodLabel.html(value);

    }, 1);
})


$(".pin-btn").click(function (e) {
    let clientPin = $("#pin").val();
    ajaxFeilds(ajaxUrl, clientPin);
});

function ajaxFeilds(url, clientPin) {
    paymentMethods();
    $.ajax({
        url: url,
        method: 'GET',
        data: {pin: clientPin, loanId: $('#loan_id').val()},
        dataType: 'json',
        success: function (data) {
            if (data.error) {
                $("#cstm-danger-alert").html(data.error);
                return;
            }

            let client = data.client;
            let clientIdCard = data.idCard;
            let loan = data.loan;
            let loanSum = data.loan.amount_approved;
            let loanPeriod = data.loan.period_approved;
            let loanOfficeId = data.loan.office_id;
            let productGroupVal = data.isInstallmentGroup;

            if (client) {
                $("#clientId").val(client.client_id);
                $("#first_name").val(client.first_name);
                $("#middle_name").val(client.middle_name);
                $("#last_name").val(client.last_name);
                $("#id_card_number").val(client.idcard_number);
                $("#phone").val(client.phone);
                let additionalPhone = client.additional_client_phone;
                $("#phone_additional").val(additionalPhone != null ? additionalPhone.number : '');

                let phoneAdditionalDiv = $("#div-phone-additional");
                let phoneAdditionalCheck = $('#toggle-phone-additional');
                additionalPhone != null ? phoneAdditionalDiv.show() : phoneAdditionalDiv.hide();
                additionalPhone != null ? phoneAdditionalCheck.prop('checked', false) : phoneAdditionalCheck.prop('checked', true);

                $("#email").val(client.email);
                $("#client_id").val(client.client_id);
            }

            if (data.clientNames) {
                $("#first_name").val(data.clientNames.first_name);
                $("#middle_name").val(data.clientNames.middle_name);
                $("#last_name").val(data.clientNames.last_name);
            }

            if (data.clientPhone) {
                $("#phone").val(data.clientPhone.number);
            }

            if (data.clientEmail) {
                $("#email").val(data.clientEmail.email);
            }

            if (clientIdCard) {
                $('[name="client_idcard[city_id]"]').val(clientIdCard.city_id);
                $('[name="client_idcard[address]"]').val(clientIdCard.address);
                $('[name="client_idcard[post_code]"]').val(clientIdCard.post_code);
                $('[name="client_idcard[issue_date]"]').val(clientIdCard.issue_date);
                $('[name="client_idcard[valid_date]"]').val(clientIdCard.valid_date);
                $('[name="client_idcard[sex]"]').val(clientIdCard.sex);
                $('[name="client_idcard[issued_by]"]').val(clientIdCard.issued_by);
            }

            if (true == productGroupVal) {
                $("#periodLabel").text(loanSettingMonth);
            }

            let address = data.address;
            let contact = data.contact;
            let guarant = data.guarant;
            let products = data.products;
            let employer = data.employer;
            let countOffice = $('#countOffices').val();

            if (address) {
                $('[name="client_address[city_id]"]').val(address.city_id);
                $('[name="client_address[address]"]').val(address.address);
                $('[name="client_address[post_code]"]').val(address.post_code);
            }

            if (employer) {
                $('[name="client_employer[name]"]').val(employer.name);
                $('[name="client_employer[bulstat]"]').val(employer.bulstat);
                $('[name="client_employer[city_id]"]').val(employer.city_id);
                $('[name="client_employer[address]"]').val(employer.address);
                $('[name="client_employer[position]"]').val(employer.position);
                $('[name="client_employer[experience]"]').val(employer.experience);
                $('[name="client_employer[salary]"]').val(employer.salary);
                $('[name="client_employer[details]"]').val(employer.details);
            }

            if (guarant) {
                $('#guarant_type option[value="' + guarant.guarant_type_id + '"]').prop('selected', true);
                $("#guarant_pin").val(guarant.pin);
                $("#guarant_first_name").val(guarant.first_name);
                $("#guarant_middle_name").val(guarant.middle_name);
                $("#guarant_last_name").val(guarant.last_name);
                $("#guarant_phone").val(guarant.phone);
                $("#guarant_id_card_number").val(guarant.idcard_number);
                $("#guarant_address").val(guarant.address);
                $("#guarant_id_card_issue_date").val(guarant.idcard_issue_date);
                $("#guarant_id_card_valid_date").val(guarant.idcard_valid_date);
            }

            if (contact) {
                $('#contact_type option[value="' + contact.contact_type_id + '"]').prop('selected', true);
                $("#contact_first_name").val(contact.first_name);
                $("#contact_last_name").val(contact.last_name);
                $("#contact_middle_name").val(contact.middle_name);
                $("#contact_phone").val(contact.phone);
            }

            $("#loan_sum").val(loanSum);
            $("#payment_sum").html(loanSum);

            $("#loan_period").val(loanPeriod);
            $("#loanPeriod").html(loanPeriod);
            $('#offices').val(loanOfficeId);

            $('#payment-method').val(loan.payment_method_id);
            if (loan.payment_method_id == PAYMENT_METHOD_BANK) {
                $("#iban").val(data.bank.iban);
                $("#iban").show();
            } else {
                $("#iban").hide();
            }

            if (products && loan.product_id) {
                reDrawProductSelect(
                    products,
                    loan.product_id
                );
            }

            if (clientIdCard.city_id != address.city_id || clientIdCard.address != address.address || clientIdCard.post_code != address.post_code) {
                $('#toggle-address').bootstrapToggle('off');
                offToggleShowAddressValue(address.address, address.post_code, address.city_id);
            } else {
                onToggleHideAddress();
            }

            $('[name="client_address[city_id]"]').selectpicker('val', address.city_id);
            $('[name="client_idcard[city_id]"]').selectpicker('val', clientIdCard.city_id);


            if (countOffice == 1) {
                $('#offices option[value="' + 1 + '"]').prop('selected', true);
                $('#offices').attr("disabled", "disabled");
                $('#offices').addClass('hideArrowFromSelect');
            }
            $('#nav-tabContent').show('slow');

        }
    })
}

