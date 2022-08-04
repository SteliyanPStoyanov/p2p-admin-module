loadSimpleDataGrid(routeRefreshLoan, $("#myInvestForm"), $("#table-myInvests"), false, 0, false);

function cartSell(cartId) {
    let postData = {
        cart: [],
        _token: csrfToken
    };

    $('.dataRow').each(function () {
        let cartLoanId = $(this).data('row');
        let outstandingInvestment = tableChildren($(this), 7);
        let discount = tableChildren($(this), 8);
        let principal = tableChildren($(this), 9);
        let salePrice = tableChildren($(this), 10);

        postData.cart.push({
            cartLoanId: cartLoanId,
            outstandingInvestment: parseFloat(outstandingInvestment),
            discount: parseFloat(discount),
            principal: parseFloat(principal),
            salePrice: parseFloat(salePrice)
        });
    });

    $.ajax({
        url: cartSaveUrl + '/' + cartId,
        type: 'POST',
        data: postData,
        beforeSend: function () {
            $("#sellAll").prop('disabled', true); // disable button
        },
        success: function (data) {
            setTimeout(function () {
                $("#sellAll").prop('disabled', false); // enable button
            }, 2000);
        }
    })
        .done(function () {
            $('.dataRow').each(function () {
                $(this.remove())
            });

            $('.my-investment-total')
                .children('td').eq(1).html('0 loans');
            $('#totalPrincipal').html('€ 0.00');
            $('#totalPrice').html('€ 0.00');
            $('#putOnSecondaryMarket').show();
            liveWireLoanReload();
        }).fail(function (jqXHR, textStatus, errorThrown) {

        let loopIndex = 0;
        $.each(jqXHR.responseJSON, function (key, value) {
            loopIndex++;
            let className = key.replace(/\./g, '-');
            if (loopIndex === 1) {
                let offset = $('.' + className).offset().top;
                setTimeout(function () {
                    window.scrollTo(offset, 0);
                }, 2);
            }

            $('.' + className).append('<div style="bottom: 45px;" class="tooltip-error-form">' + value[0] + '</div>');
            closeAlert(3000);

        });

    });

    return false;
}

function openSellForm(elem) {
    let form = elem.parent().find(".invest-form.single-sell-button");
    let outstandingAmount = elem.parent().parent().children('td').eq(10).children('.mobile-table-content').data('sum');
    form.find('input[name="amount"]').val(outstandingAmount);
    elem.hide();
    form.show();
}

function sellSingleFormClose(elem) {
    let form = elem.parent();
    form.hide();
    form.parent().children('button').show();
}

function sellFormSubmit(elem) {
    let loanId = elem.find('.loan_id').val();
    let amount = elem.find('.form-control').val();
    let investmentId = elem.find('.investment_id').val();
    let originatorId = elem.find('.originator_id').val();
    let origAmount = parseFloat(elem.parent().parent().children('td').eq(10).children('.mobile-table-content').data('sum'));

    let upperLimit = parseFloat(origAmount + (origAmount * 15 / 100));
    let lowerLimit = parseFloat(origAmount - (origAmount * 15 / 100));


    if (amount.toString().split(".")[1] && amount.toString().split(".")[1].length > 2) {
        elem.append('<div class="tooltip-error-form">' + enterValidValue + '</div>');
        closeAlert(3000);

        return false;
    }
    if (amount < lowerLimit || amount > upperLimit) {
        elem.append('<div class="tooltip-error-form">' + minAmountErrorAll + 'upper ' + upperLimit + ' lower ' + lowerLimit + '</div>');
        closeAlert(3000);

        return false;
    }

    $.ajax({
        url: elem.attr('action'),
        type: 'POST',
        data: {"_token": csrfToken, loanId, investmentId, originatorId, amount},
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
            if (data.success === true) {
                liveWireLoanReload();
                elem.append('<div class="tooltip-success-form">' + data.data.message + '</div>');
                closeAlert(3000, elem);
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

function closeAlert(secTime, elem = null) {
    setTimeout(function () {
        if (elem !== null) {
            elem.find('input[name=amount]').val('');
            investSingleFormClose(elem);
        }
        $('.tooltip-error-form').remove();
        $('.tooltip-success-form').remove();
    }, secTime);
}


$('#createdFromDatepicker').calendar(calendarSettings);

$('#createdToDatepicker').calendar(calendarSettings);

$('.btn-filter-clear').click(scrollToTopAnimation);
$('.btn-filter-submit').click(scrollToTopAnimation);

$(document).ready(function () {
    if (window.outerWidth < 768) {
        $('#filters-collapse').addClass('collapse');
    }

    $('input:radio[name="loan[status]"]').change(function () {
        if ($(this).val() == 'repaid') {
            $("#loanFinalStatuses").show();
            $("#loanStatuses").hide();
            $("#loanStatuses input[type='checkbox']").prop("checked", false);
        } else {
            $("#loanFinalStatuses").hide();
            $("#loanStatuses").show();
            $("#loanFinalStatuses input[type='checkbox']").prop("checked", false);
        }
    });
});

function scrollToTopAnimation() {
    if (window.outerWidth < 425) {
        $("body, html").animate({scrollTop: $("#header-container").scrollTop()}, 300);
    }
    $('#filters-collapse').removeClass('show');
    $('.filters-toggle').addClass('collapsed');
}


function cartBuy(cartId) {
    let data = {
        cart: [],
        cartId: cartId,
        _token: csrfToken
    };
    $('.dataRow').each(function () {
        let cartLoanId = $(this).data('row');
        let outstandingInvestment = tableChildren($(this), 9);
        let principal = tableChildren($(this), 11);
        let discount = tableChildren($(this), 10);

        data.cart.push({
            cartLoanId: cartLoanId,
            principal: principal,
            outstandingInvestment: parseFloat(outstandingInvestment),
            discount: parseFloat(discount),
        });
    });

    $.ajax({
        method: "POST",
        url: buyAllUrl + '/' + cartId,
        data: data,
        beforeSend: function () {
            $("#sellAll").prop('disabled', true); // disable button
        },
        success: function (data) {
            setTimeout(function () {
                $("#sellAll").prop('disabled', false); // enable button
            }, 2000);
             liveWireLoanReload();
        }
    }).done(function (data) {
        if (data.url) {
            window.location = data.url;
        }

        $('.dataRow').each(function () {
            $(this.remove())
        });
    }).fail(function (jqXHR, textStatus, errorThrown) {

        let loopIndex = 0;
        $.each(jqXHR.responseJSON, function (key, value) {
            loopIndex++;
            let className = key.replace(/\./g, '-');
            if (loopIndex === 1) {
                let offset = $('.' + className).offset().top;
                setTimeout(function () {
                    window.scrollTo(offset, 0);
                }, 2);
            }

            $('.' + className).append('<div style="bottom: 45px;" class="tooltip-error-form">' + value[0] + '</div>');
            closeAlert(3000);

        });
        return false;
    });
}


$(document).ajaxSuccess(function (event, xhr, settings) {

    if ($('#totalLoansView').length !== 0) {

        $('#totalLoansCountView').fadeOut(100, function () {
            $(this).html($('#totalLoansView').val()).fadeIn(100);
        });
        $('#totalLoansCountOnce').fadeOut(100, function () {
            $(this).html($('#totalLoansCount').val()).fadeIn(100);
        });
    }

    if (settings.type === 'GET') {
        $('.invest-form').hide();
    }
});

function deleteCartLoan(loanId) {
    $.get(cartDeleteUrl + loanId)
        .done(function () {
            reloadTable();
            window.setTimeout(function () {
                $('[data-toggle="tooltip"]').tooltip('dispose');
            }, 80);
        })
        .fail(function () {
            // fail notification
        });
}

function reloadTable() {
    $.ajax({
        type: 'get',
        url: routeRefreshLoan,
        data: $('.sorting.active-sort').find('input').serialize(),
        success: function (data) {
            $('#table-myInvests').html(data);
            liveWireLoanReload();
        },
    });
}

function changeDiscount(e) {
    let discount = parseFloat(e.find('input').val()).toFixed(1);

    if (discount > Math.abs(premiumLimit)) {
        e.append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + premiumLimitError + '</div>');
        closeAlert(3000);
        return false;
    }
    if (discount < -Math.abs(premiumLimit)) {
        e.append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + premiumLimitError + '</div>');
        closeAlert(3000);
        return false;
    }

    $('.form-control.premium').val(discount)

    reCalculateForm(e);

    return false;
}

function reCalculateForm(el) {
    let total = 0;
    let totalPrincipal = 0;

    if (el[0].classList[1] === 'principal') {
        let singlePrincipalVal = parseFloat(el.val());
        let singleOutstandingVal = parseFloat(el.closest('.dataRow').children('td').eq(7).find('input').val());

        if (parseFloat(singleOutstandingVal) < parseFloat(singlePrincipalVal)) {
            el.parent().append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + principalForSaleRangeError + '</div>');
            closeAlert(3000);
            return false;
        }

        if (parseFloat(singlePrincipalVal) < 0.01) {
            el.parent().append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + principalForSaleRangeErrorMin + '</div>');
            closeAlert(3000);
            return false;
        }
        let valLength = el.val().toString().length;
        if (valLength === 0) {
            el.val('0.01');

        }
    }
    $('.form-control.principal').each(function () {
        let principal = parseFloat($(this).val()).toFixed(2);

        let discount = $(this).closest('.dataRow').children('td').eq(8).find('input').val();

        let percent = (parseFloat(100.00) + parseFloat(discount)).toFixed(1);

        let value = parseFloat(percent * principal / 100).toFixed(2);

        total += parseFloat(value);

        totalPrincipal += parseFloat(principal);

        $(this).closest('.dataRow').children('td').eq(10).find('.form-control.price').val(value);

    });

    $('#totalPrice').text('€ ' + parseFloat(total).toFixed(2));

    $('#totalPrincipal').text('€ ' + parseFloat(totalPrincipal).toFixed(2));

    if (el[0].classList[1] === 'principal') {
        if (singlePrincipalVal.toString().split(".")[1]) {
            if (singlePrincipalVal.toString().split(".")[1].length > 2) {
                el.parent().append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + principalForSalePleaseEnter + '</div>');
                closeAlert(3000);
                el.val(singlePrincipalVal.toFixed(2));
            }
        }

        el.val(singlePrincipalVal.toFixed(2));
    }
}

function reCalculateBuyForm() {
    let totalBuy = 0;

    $('.form-control.premium').each(function () {
        let principal = parseFloat($(this).val()).toFixed(2);
        totalBuy += parseFloat(principal);
    });

    $('#totalPriceBuy').text('€ ' + parseFloat(totalBuy).toFixed(2));
}

function tableChildren(el, num) {
    return el.children('td').eq(num).children('.mobile-table-content').find('input').val();

}


$(document).on('keypress', '.interest-rate-field', function (el) {
    let val = parseFloat($(this).val());
    $(this).data("prevValue", val.toFixed(1));
});

$(document).on('change', '.interest-rate-field', function (el) {
    checkPresentige($(this));

});

function checkPresentige(el) {
    let val = parseFloat(el.val());
    let valLength = el.val().toString().length;

    let oldvalue = el.data("prevValue");

    if (val.toString().split(".")[1]) {
        if (val.toString().split(".")[1].length > 1) {
            el.parent().append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + premiumDecimalError + '</div>');
            closeAlert(3000);
            el.val(val.toFixed(1));
        }
    }

    if (val > Math.abs(premiumLimit)) {
        el.parent().append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + premiumLimitError + '</div>');
        closeAlert(3000);
        el.val(oldvalue);
        return false;
    }
    if (val < -Math.abs(premiumLimit)) {
        el.parent().append('<div style="bottom: 45px; left: 40px;" class="tooltip-error-form">' + premiumLimitError + '</div>');
        closeAlert(3000);
        el.val(oldvalue);
        return false;
    }

    el.val(val.toFixed(1));

    if (valLength === 0) {
        el.val('0.1');
        return false;
    }
}

$(document).on('click', '.minus', function () {
    let input = $(this).parent().find('input');

    let count = parseInt(input.val().toFixed(1)) - 0.1;
    console.log(count, input.val().toFixed(1));

    input.val(count + ' %');
    input.change();
    return false;
});
$('.plus').click(function () {
    let input = $(this).parent().find('input');
    console.log(input);
    input.val(parseInt(input.val()) + 0.1 + ' %');
    input.change();
    return false;
});


