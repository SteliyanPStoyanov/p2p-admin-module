$(document).ready(function () {

    $('#selectAllControllers').on("change", function () {
        let hook = this;
        let checkboxes = $('.allCheckBoxCont').find("input[type=checkbox]");

        $.each(checkboxes, function () {
            $('.classSelector').prop('checked', hook.checked);
            $('.permission').prop('checked', hook.checked);
            $('.roleSelector').prop('checked', hook.checked);
        });
    });

    $('.roleSelector').on("change", function () {
        let hook = this;
        let module = $(this).attr("data-module");
        let checkboxes = $('#' + module).find("input[type=checkbox]");

        $.each(checkboxes, function () {
            $('#' + module + ' .classSelector').prop('checked', hook.checked);
            $('#' + module + ' .permission').prop('checked', hook.checked);
            $('#selectAllControllers').prop('checked', hook.checked);
        });
    });

    $('.selectByController').on("change", function () {
        let hook = this;
        let controller = $(this).attr("data-controller");
        let module = $(this).attr("data-module");
        let controllerCheckboxes = $('#box' + controller).find("input[type=checkbox]");

        let allClassSelector = $('#' + module + ' .classSelector');

        let allClassSelectorChecked = true;

        $.each($(allClassSelector), function (key, value) {
            if (!value.checked) {
                allClassSelectorChecked = false;
            }
        });

        $('#select' + module).prop('checked', allClassSelectorChecked);

        $('#selectAllControllers').prop('checked', allClassSelectorChecked);


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

        let allClassSelectorChecked = true;

        $.each($(allClassSelector), function (key, value) {
            if (!value.checked) {
                allClassSelectorChecked = false;
            }
        });

        $('#' + controller).prop('checked', areAllChecked);

        $('#select' + module).prop('checked', allClassSelectorChecked);


        if ($('.allCheckBoxCont :checkbox:not(:checked)').length == 0) {
            $('#selectAllControllers').prop('checked', true);
        } else if ($('.allCheckBoxCont :checkbox:not(:checked)').length > 0) {
            $('#selectAllControllers').prop('checked', false);
        }
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
    let ContState = {};
    $.each($(activeModule), function (key, value) {
        if (value) {
            let allController = $('#' + value.dataset.module + ' .classSelector');

            $.each($(allController), function (key, value) {
                let allPermission = $('#box' + value.dataset.controller + ' .permission');

                $.each($(allPermission), function (key, value) {
                    let allClassState = true;
                    if (!value.checked) {
                        allClassState = false;
                    }
                    if (allClassState == false) {
                        $('#' + value.dataset.controller).prop('checked', false);
                        return false;
                    } else {
                        $('#' + value.dataset.controller).prop('checked', true);
                    }
                });

            });

            if ($('#' + value.dataset.module + ' :checkbox:not(:checked)').length == 0) {
                $('#select' + value.dataset.module).prop('checked', true);
            } else if ($('#' + value.dataset.module + ':checkbox:checked').length == 0) {
                $('#select' + value.dataset.module).prop('checked', false);
            }
        }

        let allModulesState = true;
        if (!value.checked) {
            allModulesState = false;
        }


        $('#selectAllControllers').prop('checked', allModulesState);

    });
}

