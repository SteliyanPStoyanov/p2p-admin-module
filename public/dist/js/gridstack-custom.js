function saveDragGrid() {
    let serializedData = [];
    grid.engine.nodes.forEach(function (node) {
        serializedData.push({
            x: node.x,
            y: node.y,
            width: node.width,
            height: node.height,
            id: node.id
        });
    });
    $.ajax({
        url: urlSave,
        data: {
            "_token": csrfToken,
            "data": JSON.stringify(serializedData)
        },
        type: 'post',
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
        }
    });
}

function loadDragGrid() {
    $.ajax({
        url: urlLoad,
        data: {
            "_token": csrfToken,
        },
        type: 'post',
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
            if (jQuery.isEmptyObject(data) == false) {
                let serializedDataN = JSON.parse(data.layout);
                let itemsNew = GridStack.Utils.sort(serializedDataN);

                grid.batchUpdate();

                itemsNew.forEach(function (item) {
                    $("#" + item.id).attr("data-gs-x", item.x);
                    $("#" + item.id).attr("data-gs-y", item.y);
                });

                grid.commit();
            }
        }
    });

};

const $salaryPeriod = $('#salaryPeriod');
const $salaryPeriodText = $('.salaryPeriodText');
const $salarySum = $('#salarySum');
const $salarySumText = $('.salarySumText');
const $paymentsPeriod = $('#paymentsPeriod');
const $paymentsPeriodText = $('.paymentsPeriodText');
const $paymentsSum = $('#paymentsSum');
const $paymentsSumText = $('.paymentsSumText');

$salaryPeriodText.html($salaryPeriod.val());
$salarySumText.html($salarySum.val());
$paymentsPeriodText.html($paymentsPeriod.val());
$paymentsSumText.html($paymentsSum.val());

$salarySum.on('input change', () => {
    claculateSomeLoan('Salary');
});
$salaryPeriod.on('input change', () => {
    claculateSomeLoan('Salary');
});
$paymentsSum.on('input change', () => {
    claculateSomeLoan('Payments');
});
$paymentsPeriod.on('input change', () => {
    claculateSomeLoan('Payments');
});

function claculateSomeLoan(module) {
    let sum
    let sumVal
    let periodVal
    if (module === 'Salary') {
        $salarySumText.html($salarySum.val());
        $salaryPeriodText.html($salaryPeriod.val());
        sum = $salarySum.val() / $salaryPeriod.val();
        sumVal = $salarySum.val();
        periodVal = $salaryPeriod.val();

    } else {
        $paymentsSumText.html($paymentsSum.val());
        $paymentsPeriodText.html($paymentsPeriod.val());
        sum = $paymentsSum.val() / $paymentsPeriod.val();
        sumVal = $paymentsSum.val();
        periodVal = $paymentsPeriod.val();
    }

    $('#to' + module + 'Sum > span').html(sumVal);
    //todo FIX LATER !!!!!!
    $('#loan_sum').val(sumVal);
    $('#loan_period').val(periodVal);


    $('#to' + module + 'Time > span').html(periodVal);
    $('#to' + module + 'Payment > span').html(sum);
    $('#to' + module + 'Interest > span').html((70 / 100) * sum);
    $('#to' + module + 'Penalty > span').html((30 / 100) * sum);
}

// $("#opened-tasks," +
//     "#drag")
//     .mouseenter(function (e) {
//         $("#icon-opened-tasks," +
//             "#icon-drag"
//         ).addClass("grid-stack-item-content");
//     });
// $("#opened-tasks," +
//     "#drag")
//     .mouseleave(function (e) {
//         $("#icon-opened-tasks," +
//             "#icon-drag"
//         ).removeClass("grid-stack-item-content");
//     });
