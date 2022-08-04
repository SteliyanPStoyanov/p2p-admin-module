if (localStorage.accClickCount) {
    accCountClicks = localStorage.accClickCount;
    let parseDate = new Date(localStorage.accClickCountDate);
    let newDate = parseDate.setMinutes(
        parseDate.getMinutes() + 15);

    if (newDate < new Date()) {
        accCountClicks = 0;
    }
}

$(document).on('click', '#exportBtn', function (event) {

    if (accCountClicks >= 5) {
        event.preventDefault();
        $(this).append('<div class="tooltip-error-form">Multiple sheets export error.</div>');
        closeAlert(3000);
        return false;
    }
    accCountClicks += 1;
    localStorage.accClickCount = Number(accCountClicks);
    localStorage.accClickCountDate = new Date();
    exportFile();
});

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

function exportFile() {

    let dataForm = $('#accountStatement').serialize();


    $.ajax({
        type: 'get',
        url: accountStatementUrl,
        data: dataForm,
        xhrFields: {
            responseType: 'blob'
        },
        success: function (response) {
            let d = new Date();
            let dateString = d.getFullYear() + ''
                + ('0' + (d.getMonth() + 1)).slice(-2) + ''
                + ('0' + d.getDate()).slice(-2);
            let blob = new Blob([response]);
            let link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = dateString + "-account-statement-export.xlsx";
            link.click();
        },
    });
}
