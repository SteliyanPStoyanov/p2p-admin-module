loadSimpleDataGrid(routeRefreshLoan, $("#myInvestForm"), $("#table-myInvests"), false, 0, false);


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

    $('[data-toggle="tooltip"]').tooltip();
});


$(document).ajaxSuccess(function (event, xhr, settings) {
    if ($('#loanStatusSell').val() == loanRepaid) {
        $('.hide-sell-finish-all').hide();
    }

    if ($('#loanStatusSell').val() != loanRepaid) {
        $('.hide-sell-finish-all').show();
    }

    if ($('#totalLoansView').length != 0) {
        $('#totalLoansCountView').fadeOut(100, function () {
            $(this).html($('#totalLoansView').val()).fadeIn(100);
        });
        $('#totalLoansCountOnce').fadeOut(100, function () {
            $(this).html($('#totalLoansCount').val()).fadeIn(100);
        });
    }
});
$(document).on('change', '#maxRows', function () {
    reloadTable();
});

$(document).on('click', '#sell_all', function (elem) {
    elem.preventDefault();

    let items = new Array();
    let AllInvestmentForm = $('#table-myInvests .hide-sell-finish form');

    AllInvestmentForm.each(function (el) {
        if($(this).is(':hidden')) {
            items[el] = $(this).serializeArray();
        }
    });

    let dataArray = [];
    let i = 0;
    for (const x in items) {
        let obj = {};
        for (const y in items[x]) {
            obj[items[x][y]['name']] = items[x][y]['value'];
        }
        dataArray[i] = obj;
        i++;
    }

    $.ajax({
        url: $(this).data('saleUrl'),
        type: 'POST',
        data: {"_token": csrfToken, dataArray},
        headers: {"Accept": "application/json"},
        beforeSend: function () {
            $("#sell_all").prop('disabled', true); // disable button
        },
        success: function (data) {
            if (data.success === true) {
                if (document.getElementsByClassName("remove-all-from-cart").length === 0) {
                    $('.hide-sell-finish-all').append('<div class="remove-all-from-cart"  onclick="deleteAllCartLoan(this);"><i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i></div>')
                }
                reloadTable();
                setTimeout(function () {
                    liveWireAddCart();
                }, 20);

                setTimeout(function () {
                    $("#sell_all").prop('disabled', false); // enable button
                }, 2000);
            }
            if (data.success === false) {

            }
            return false;
        }
    });
});

$(document).on('click', '.investment-isOnCard', function () {
    $(this).find('.single-amount').prop("disabled", false);
    $(this).find('.single-amount').focus();
    $(this).find('.button').prop("disabled", false);
});

function closeAlert(secTime, elem = null) {
    setTimeout(function () {
        if (elem !== null) {
            elem.find('input[name=amount]').val('');
        }
        $('.tooltip-error-form').remove();
        $('.tooltip-success-form').remove();
    }, secTime);
}

function investSingleFormClose(elem) {
    elem.parent().parent().find('.invest-form').removeClass('show');
    elem.parent().parent().find('.sell_loan').show();
    elem.hide();
}

function reloadTable() {
    let page = $('.pagination .page-item.active').find('.page-link').text();
    $.ajax({
        type: 'get',
        url: routeRefreshLoan,
        data: 'page=' + page + '&' + $('#myInvestForm').serialize() + '&limit=' + $('#maxRows').val() + '&' + $('.sorting.active-sort').find('input').serialize(),
        success: function (data) {
            $('#table-myInvests').html(data);
        },
    });
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

function deleteMarketLoan(loanId, type = null) {
    $.get(myInvestmentMarketRemoveUrl + loanId)
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


function deleteAllCartLoan(elem) {
    let AllInvestmentOnCard = $('#table-myInvests .hide-sell-finish .investment-isOnCard');
    let AllInvestmentOnMarket = $('#table-myInvests .hide-sell-finish .investment-isOnMarket');

    if (AllInvestmentOnCard.length > 0) {
        AllInvestmentOnCard.each(function () {
            deleteCartLoan($(this).find('.cart_loan_id').val(), 'multiple');
        });
    }

    if (AllInvestmentOnMarket.length > 0) {
        AllInvestmentOnMarket.each(function () {
            deleteMarketLoan($(this).find('.cart_loan_id').val(), 'multiple');
        });
    }
    $(elem).remove();

    setTimeout(function () {
        reloadTable();
        liveWireAddCart();
    }, 400); // TODO: This is wrong way. Need to collect all ids and delete them all at once
}

function scrollToTopAnimation() {
    if (window.outerWidth < 425) {
        $("body, html").animate({scrollTop: $("#header-container").scrollTop()}, 300);
    }
    $('#filters-collapse').removeClass('show');
    $('.filters-toggle').addClass('collapsed');
}

const calendarSettings = {
    type: 'date',
    monthFirst: false,
    formatter: {
        date: function (date, settings) {
            let parsedDate = new Date(date).toLocaleDateString("en-GB");
            return parsedDate.replaceAll('/', '.');
        }
    }
};

$('#createdFromDatepicker').calendar(calendarSettings);
$('#createdToDatepicker').calendar(calendarSettings);
$('.btn-filter-clear').click(scrollToTopAnimation);
$('.btn-filter-submit').click(scrollToTopAnimation);


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
    let cartLoanId = elem.find('.cart_loan_id').val();

    if (checkDecimal(elem.find('.form-control'), '.single-amount') === false) {
        return false;
    }

    if (checkMaxAmount(elem.find('.form-control'), '.single-amount') === false) {
        return false;
    }

    $.ajax({
        url: elem.attr('action'),
        type: 'POST',
        data: {"_token": csrfToken, loanId, investmentId, originatorId, amount, cartLoanId},
        headers: {"Accept": "application/json"},
        success: function (data) {
            if (data.success === true) {
                elem.append('<div class="tooltip-success-form">' + data.data.message + '</div>');
                reloadTable();
                if (document.getElementsByClassName("remove-all-from-cart").length === 0) {
                    $('.hide-sell-finish-all').append('<div class="remove-all-from-cart"  onclick="deleteAllCartLoan(this);"><i class="fa fa-times" aria-hidden="true" style="font-size: 20px;"></i></div>')
                }
                liveWireAddCart();
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

function liveWireAddCart() {
    Livewire.emit('loanAdd');
}

function removeSaleAllDelete() {
    let AllInvestmentOnCard = $('#table-myInvests .hide-sell-finish .investment-isOnCard');
    let AllInvestmentOnMarket = $('#table-myInvests .hide-sell-finish .investment-isOnMarket');
    let sumActive = AllInvestmentOnCard.length + AllInvestmentOnMarket.length;
    if (sumActive === 1) {
        $('.remove-all-from-cart').remove();
    }
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
