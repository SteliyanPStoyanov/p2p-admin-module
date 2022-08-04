$(document).ready(function () {
    $('#offices').selectpicker();
    $('.roleSelector').on("change", function () {
        let hook = this;
        let module = $(this).attr("data-module");
        let checkboxes = $('#' + module).find("input[type=checkbox]");
        $('.classSelector').prop('checked', false);
        $('.permission').prop('checked', false);

        $.each(checkboxes, function () {
            $('#' + module + ' .classSelector').prop('checked', hook.checked);
            $('#' + module + ' .permission').prop('checked', hook.checked);
        });
    });

    $('.selectByController').on("change", function () {
        let hook = this;
        let controller = $(this).attr("data-controller");
        let module = $(this).attr("data-module");
        let controllerCheckboxes = $('#box' + controller).find("input[type=checkbox]");

        let allClassSelector = $('#' + module + ' .classSelector');

        let allClassSelectorChecked = false;

        $.each($(allClassSelector), function (key, value) {
            if (value.checked) {
                allClassSelectorChecked = true;
            }
        });

        $('#select' + module).prop('checked', allClassSelectorChecked);

        $.each(controllerCheckboxes, function () {
            $(this).prop('checked', hook.checked);
        });
    });

    $('.permission').on("change", function () {
        let controller = $(this).attr("data-controller");
        let module = $(this).attr("data-module");
        let allSelector = $('#box' + controller + ' .permission');
        let allClassSelector = $('#' + module + ' .permission');

        let areAllChecked = true;

        $.each($(allSelector), function (key, value) {
            if (!value.checked) {
                areAllChecked = false;
            }
        });

        let allClassSelectorChecked = false;

        $.each($(allClassSelector), function (key, value) {
            if (value.checked) {
                allClassSelectorChecked = true;
            }
        });

        $('#' + controller).prop('checked', areAllChecked);

        $('#select' + module).prop('checked', allClassSelectorChecked);

    });

    $('.toggle-icon-state').on("click", function () {
        let module = $(this).attr("data-module");
        let hideShowBox = $('#box' + module);
        hideShowBox.toggle("slow");
        $(this).toggleClass('toggle-icon-open');
    });

})

function getCheckController() {
    let activeModule = $('.roleSelector');
    $.each($(activeModule), function (key, value) {
        if (value.checked) {
            let allController = $('#' + value.dataset.module + ' .classSelector');
            $.each($(allController), function (key, value) {
                let allPermission = $('#box' + value.dataset.controller + ' .permission');
                $.each($(allPermission), function (key, value) {
                    let allClassState = true;
                    if (!value.checked) {
                        allClassState = false;
                    }
                    if(allClassState == false){
                        $('#' + value.dataset.controller).prop('checked', false);
                        return false;
                    }else{
                        $('#' + value.dataset.controller).prop('checked', true);
                    }
                });
            });
        }
    });
}

