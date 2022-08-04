loadSimpleDataGrid(routeRefreshLoan, $("#investForm"), $("#table-invests"), false, 0, false);

function investSecondaryMarketSingleForm(elem) {
    let form = elem.parent().find(".invest-form.single-buy-button");
    elem.parent().find('.invest-form').addClass('show');
    elem.parent().find('.invest-form.show').show();
    elem.parent().find('.close-form').show();
    elem.parent().find('.invest-form input').focus();
    elem.hide();
    return false;
}

$(document).on('change', '#maxRows', function () {
    reloadTable();
});

function investSingleFormClose(elem) {
    let form = elem.parent();
    form.removeClass('show');
    form.hide();
    form.parent().children('.invest-button-form').show();
}

function investSecondaryMarketFormSubmit(elem) {

    event.preventDefault();
    $.ajax({
        url: elem.attr('action'),
        type: 'POST',
        data: {
            "_token": csrfToken,
            loan_id: elem.find('.loan_id').val(),
            investment_id: elem.find('.investment_id').val(),
            originator_id: elem.find('.originator_id').val(),
            cart_loan_id: elem.find('.cart_loan_id').val(),
            amount: elem.find('.form-control').val(),
            market_secondary_id: elem.find('.market_secondary_id').val()
        },
        headers: {
            "Accept": "application/json",
        },
        beforeSend: function () {
            $(".ui.teal.button").prop('disabled', true); // disable button
        },
        success: function (data) {

            if (data.success === true) {
                elem.append('<input type="hidden" name="cart_loan_id" value="' + data.data.cart_loan_id + '">');

                if (document.getElementsByClassName("remove-all-from-cart").length === 2) {
                    $('.remove-all-from-cart.remove-buy-cart').show();
                }
                setTimeout(function () {
                    reloadTable();
                    liveWireAddCart();
                    setTimeout(function () {
                        $('.invest-form.single-buy-button').hide();
                        $('.invest-form.single-buy-button.investment-isOnCard').show();
                    }, 100);

                }, 1000);

                setTimeout(function () {
                    $(".ui.teal.button").prop('disabled', false); // enable button
                }, 2000);
            }
            if (data.success === false) {
                elem.append('<div class="tooltip-error-form">' + data.data.message + '</div>');
                closeAlert(3000);
            }
            return false;
        }
    });
    return false;
}

function deleteCartLoan(loanId, type = null) {

    $.get(myInvestmentRemoveUrl + loanId)
        .done(function () {
            if (type == null) {
                liveWireAddCart();
                reloadTable();
                removeSaleAllDelete();
            }
        })
        .fail(function () {
            // fail notification
        });
}

function reloadTable() {
    let data = $('#investForm').serialize() + '&limit=' + $('#maxRows').val() + '&' + $('.sorting.active-sort').find('input').serialize();
    if ($('ul.pagination').find('li.active').find('span').html()) {
        data += 'page=' + $('ul.pagination').find('li.active').find('span').html() + '&';
    }

    if ('/profile/invest/unsuccessful' === window.location.pathname) {
        data += '&secondaryMarketFailed=true';
    }

    $.ajax({
        type: 'get',
        url: routeRefreshLoan,
        data: data,
        success: function (data) {
            $('#table-invests').html(data);
        },
    });
}

function liveWireAddCart() {
    Livewire.emit('loanAdd');
}

function removeSaleAllDelete() {
    let AllInvestmentOnCard = $('#table-invests .hide-sell-finish .investment-isOnCard');
    let sumActive = AllInvestmentOnCard.length;
    let HideSell = $('.hide-sell-finish-all');

    if (sumActive === 1) {
        HideSell.find('.invest-all-form').hide();
        HideSell.find('.invest-all-button').show();
        $('.remove-all-from-cart').hide();
    }
}

function investAllForm() {
    disabledBunchActive();
}

function disabledBunchActive() {
    $.ajax({
        url: investorHasBunchUrl,
        type: 'get',
        success: function (data) {
            if (data.success === false) {
                $('#investAllFormHas').html('<div class="tooltip-error-form">' + data.data.message + '</div>');
                closeAlert(3000);
                return false;
            } else {
                $('.invest-all-form').show();
                $('.invest-all-form .close-form').show();
                $('.invest-all-button').hide();
                $('.invest-all-form input').focus();
            }
        }
    });
}

function investAllFormSubmit(elem) {
    event.preventDefault();
    let items = new Array();
    let investAllFormAmount = parseFloat(elem.find('.invest-all-form-amount').val());

    let AllInvestmentForm = $('#investTable .hide-sell-finish form');

    AllInvestmentForm.each(function (el) {
        let foundAmount = parseFloat($(this).find('.single-amount').val());
        if (foundAmount > investAllFormAmount) {
            $(this).find('.single-amount').val(investAllFormAmount);

        }

        items[el] = $(this).serializeArray();
    });

    let dataArray = [];
    for (const x in items) {
        let obj = {};
        for (const y in items[x]) {
            obj[items[x][y]['name']] = items[x][y]['value'];
        }
        dataArray[x] = obj;
    }

    $.ajax({
        url: elem.attr('action'),
        type: 'POST',
        data: {"_token": csrfToken, cart: dataArray},
        headers: {"Accept": "application/json"},
        beforeSend: function () {
            $(".invest-all-form-submit").prop('disabled', true); // disable button
        },
        success: function (data) {
            if (data.success === true) {
                reloadTable();
                setTimeout(function () {
                    liveWireAddCart();
                }, 20);

                setTimeout(function () {
                    $('.invest-form.single-buy-button').hide();
                    $('.invest-form.single-buy-button.investment-isOnCard').show();
                }, 100);

                setTimeout(function () {
                    $(".invest-all-form-submit").prop('disabled', false); // disable button
                }, 2000);

            }
            return false;
        }
    });
}

function deleteAllCartLoan(elem) {
    let AllInvestmentOnCard = $('#investTable .hide-sell-finish .investment-isOnCard');

    if (AllInvestmentOnCard.length > 0) {
        AllInvestmentOnCard.each(function () {
            deleteCartLoan($(this).find('.cart_loan_id').val(), 'multiple');
        });
    }

    let HideSell = $('.hide-sell-finish-all');
    HideSell.find('.invest-all-form').hide();
    HideSell.find('.invest-all-button').show();

    $('.remove-buy-cart').hide();

    setTimeout(function () {
        reloadTable();
        liveWireAddCart();
    }, 200);
}


$('.single-amount').keyup(function () {
        checkDecimal($(this), '.single-amount');
        checkMaxAmount($(this), '.single-amount');
    }
);

function checkDecimal(el, className) {
    let amount = el.val();
    if (amount.toString().split(".")[1] && amount.toString().split(".")[1].length > 2) {
        el.parent().append('<div class="tooltip-error-form">' + enterValidValue + '</div>');
        $(className).val(Number(amount).toFixed(2));
        closeAlert(3000);
        return false;
    }
}

function checkMaxAmount(el, className) {

    let amount = Number(el.val()).toFixed(2);
    let outstandingAmount = el.closest('.dataRow').children('td').eq(10).children('.mobile-table-content').data('sum');

    if (parseFloat(amount) > parseFloat(outstandingAmount)) {
        el.parent().append('<div class="tooltip-error-form">' + enterValidValue + ' ' + outstandingAmount + ' </div>');
        closeAlert(3000);
        return false;
    }
}

function closeAlert(secTime, elem = null) {
    setTimeout(function () {
        if (elem !== null) {
            elem.find('input[name=amount]').val('');
        }
        $('.tooltip-error-form').remove();
        $('.tooltip-success-form').remove();
    }, secTime);
}

