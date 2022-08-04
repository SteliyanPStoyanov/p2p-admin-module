loadSimpleDataGrid(routeRefreshLoan, $("#investForm"), $("#table-invests"), false, 0, false);

calendarSettings = {
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

$('.invest-form').hide();

$('.btn-filter-clear').click(scrollToTopAnimation);
$('.btn-filter-submit').click(scrollToTopAnimation);

$(document).ready(function () {
    if (window.outerWidth < 768) {
        $('#filters-collapse').addClass('collapse');
    }
});

function scrollToTopAnimation() {
    if (window.outerWidth < 425) {
        $("body, html").animate({scrollTop: $("#header-container").scrollTop()}, 300);
    }
    $('#filters-collapse').removeClass('show');
    $('.filters-toggle').addClass('collapsed');
}

$(document).ajaxSuccess(function (event, xhr, settings) {

        if ($('#totalLoansCount').length != 0) {
            if ($('#totalLoansCountView').text() !== $('#totalLoansCount').val()) {
                $('#totalLoansCountView').fadeOut(100, function () {
                    $(this).html($('#totalLoansCount').val()).fadeIn(100);
                });
            } else {
                $('#totalLoansCountView').html($('#totalLoansCount').val());
            }
        }

        if (settings.type == 'GET') {
            $('.invest-form').hide();
        }
    }
);

function investSingleForm(elem) {
    disabledSingleBunchActive(elem);
}

function investSingleFormClose(elem) {
    elem.parent().parent().find('.invest-form').removeClass('show');
    elem.parent().parent().find('.invest-button-form').show();
    elem.hide();
}

function investAllForm() {
    disabledBunchActive();
}

function investAllShowForm() {
    $('.invest-all-form').hide();
    $('.invest-all-form .close-form').hide();
    $('.invest-all-button').show();
    $('.invest-all-form input').focus();
}


function investAllFormSubmit(elem) {
    let amount = elem.parent().find('.invest-all-form-amount').val();
    if (amount.toString().split(".")[1] && amount.toString().split(".")[1].length > 2) {
        elem.append('<div class="tooltip-error-form">' + enterValidValue + '</div>');
        closeAlert(3000);
        return false;
    }

    if (amount < minAmount) {
        elem.append('<div class="tooltip-error-form">' + minAmountErrorAll + '</div>');
        closeAlert(3000);
        return false;
    }

    if (amount > uninvestedWallet) {
        elem.append('<div class="tooltip-error-form">' + uninvestedAmountIsLower + '</div>');
        closeAlert(3000);
        return false;
    }

    let loanId = elem.find('.loan_id').val();

    event.preventDefault();
    $.ajax({
        url: investAllUrl,
        type: 'POST',
        data: {"_token": csrfToken, loanId, amount},
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
            if (data.success === true) {
                elem.append('<div class="tooltip-success-form">' + data.data.message + '</div>');
                liveWire();
                closeAlert(3000, elem);
            }

            if (data.success === false) {
                elem.append('<div class="tooltip-error-form">' + data.data.message + '</div>');
                closeAlert(3000);
                return false;
            }
            investAllShowForm();
            return false;
        }
    });
}

function investAllClose(elem) {
    elem.parent().parent().find('.invest-all-form').hide();
    elem.parent().parent().find('.invest-all-button').show();
    elem.hide();
}

function investFormSubmit(elem) {
    let loanId = elem.find('.loan_id').val();
    let amount = elem.find('.form-control').val();

    if (amount.toString().split(".")[1] && amount.toString().split(".")[1].length > 2) {
        elem.append('<div class="tooltip-error-form">' + enterValidValue + '</div>');
        closeAlert(3000);
        return false;
    }
    if (amount < minAmount) {
        elem.append('<div class="tooltip-error-form">' + minAmountErrorAll + '</div>');
        closeAlert(3000);
        return false;
    }

    $.ajax({
        url: elem.attr('action'),
        type: 'POST',
        data: {"_token": csrfToken, loanId, amount},
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
            if (data.success === true) {
                liveWire();
                elem.append('<div class="tooltip-success-form">' + data.data.message + '</div>');
                closeAlert(3000, elem);
                 reloadTableTimeOut(3001);
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

$(document).on('change', '#maxRows', function () {
    reloadTable();
});

$('.invest-all-form-amount').keyup(function () {
        checkDecimal($(this), '.invest-all-form-amount');
    }
);

$('.single-amount').keyup(function () {
        checkDecimal($(this), '.single-amount');
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

function disabledSingleBunchActive(elem) {
    $.ajax({
        url: investorHasBunchUrl,
        type: 'get',
        success: function (data) {
            if (data.success === false) {
                elem.append('<div class="tooltip-error-form">' + data.data.message + '</div>');
                closeAlert(3000);
                return false;
            } else {
                elem.parent().find('.invest-form').addClass('show');
                elem.parent().find('.close-form').show();
                elem.parent().find('.invest-form input').focus();
                elem.hide();
            }

        }
    });
}

function reloadTable() {
    $.ajax({
        type: 'get',
        url: routeRefreshLoan,
        data: $('#investForm').serialize() + '&limit=' + $('#maxRows').val() + '&' + $('.sorting.active-sort').find('input').serialize(),

        success: function (data) {
            $('#table-invests').html(data);
        },
    });
}

function reloadTableTimeOut(secTime) {
    setTimeout(function () {
        reloadTable();
    }, secTime);
}
