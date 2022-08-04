// init daterangepicker popUp Modal
$('input[name="date"]').daterangepicker({
    "singleDatePicker": true
});

// init daterangepicker
let picker = $('#repayment').daterangepicker({
    maxDate: endLoanDate,
    "parentEl": "#repayment-container",
    "autoApply": true,
    "singleDatePicker": true,
    "startDate": endLoanDate,
    locale: {
        format: 'YYYY-MM-DD'
    }
});

// range update listener
picker.on('apply.daterangepicker', function (ev, picker) {
    let newDate = picker.startDate.format('DD-MM-YYYY');

    recalculateLoan(newDate);
});

// prevent hide after range selection
picker.data('daterangepicker').hide = function () {
};

// show picker on load
picker.data('daterangepicker').show();

function recalculateLoan(date) {
    $.ajax({
        url: urlRecalculate,
        data: {
            "_token": csrfToken,
            "date": date,
            "loan_id": loan_id
        },
        type: 'post',
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
            $('#PrincipalReCalc > span').html(data.amount_approved);
            $('#InterestReCalc > span').html((70 / 100) * data.amount_approved);
            $('#PenaltyReCalc > span').html((30 / 100) * data.amount_approved);
        }
    });
}
