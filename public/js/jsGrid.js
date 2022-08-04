function loadSimpleDataGrid(
    urlController,
    formId,
    tableId,
    toRefresh = false,
    refreshInterval = 10000,
    loadDataRange = true ,
    investAll = null,
    reloadWithPagination = null
) {
    if (loadDataRange == true) {
        loadDateRangePicker($("#createdAt, #updatedAt"));
    }
    if (investAll == true) {
        investAllReloadDataTable();
    }
     if (reloadWithPagination == true) {
        reloadDataTablePagination();
    }

    $(document).ready(function () {
        $(document).on('click', '.pagination a', function (e) {
            e.preventDefault();
            getTable($(this).attr('href').split('page=')[1], true, null, true);
        });
        $(document).on('click', 'button[type=reset]', function (e) {
            e.preventDefault();
            $('#' + formId.attr('id') + ' select').not(".noClear").val('');
            $('#' + formId.attr('id') + ' input[type=text]').val('');
            $('#' + formId.attr('id') + ' input[type=number]').val('');

            if($('#' + formId.attr('id') + ' input[type=radio]').hasClass('noClear') == false){
                $('#' + formId.attr('id') + ' input[type=radio]').prop('checked', false);
            }

            $('#' + formId.attr('id') + ' input[type=checkbox]').not('noClear').prop('checked', false);
            getTable($('ul.pagination').find('li.active').find('span').html(), true, null, false);
        });
        $(document).on('click', 'input[type=submit]', function (e) {
            e.preventDefault();
            getTable($('ul.pagination').find('li.active').find('span').html(), true, null, false);
        });
    });
    if (toRefresh) {
        setInterval(function () {
            if ($('.check-modal').is(':visible')) {
                return;
            }
            getTable($('ul.pagination').find('li.active').find('span').html());
        }, refreshInterval);
    }

    function getTable(page, withFilters = true, sorting = null, pagination = null) {
        let data = [];
        if (true === withFilters) {
            let formData = formId.serialize();
            data = formData;
            if ($("#maxRows").val() > 0) {
                data += '&limit=' + $("#maxRows").val();
            }

        }

        if (sorting != null) {
            data += '&' + sorting
        } else {
            data += '&' + $('.sorting.active-sort').find('input').serialize()
        }
        let url = urlController;
        if (true === pagination) {
            page = parseInt(page);
            if (page > 0) {
                url = url + '?page=' + page;
            }
        }

        if ('/profile/invest/unsuccessful' == window.location.pathname) {
            data += '&secondaryMarketFailed=true';
        }

        $.ajax({
            url: url,
            type: 'GET',
            data: data,
            success: function (response) {
                tableId.html('').append(response);
                $('td.readOnly').css({"pointer-events": "none", "cursor": "not-allowed", "opacity": "0.60"});
            },
            error: function (jqXHR) {

                let messages;
                try {
                    messages = JSON.parse(jqXHR.responseJSON.message);
                } catch(err) {
                    messages = jqXHR.responseJSON.message;
                }

                let errorHandler = $("#errorHandlerAjax");
                let errors = '<div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show" role="alert">\n';

                if ($.isArray(messages) || (typeof messages === 'object' && messages !== null)) {
                    $.each(messages, function (key, value) {
                        errors += value;
                    });
                } else {
                    errors += messages;
                }

                errors += '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                    '    <span aria-hidden="true">&times;</span>\n' +
                    '  </button>\n' +
                    '</div>';
                errorHandler.html(errors);
            }
        });
    }
     function investAllReloadDataTable(){
        getTable($('ul.pagination').find('li.active').find('span').html(), true) ;
     }

    function reloadDataTablePagination(){
        getTable($('ul.pagination').find('li.active').find('span').html(), true , true, true) ;
     }

    $('.clearGroupFilter').click(function (e) {
        e.preventDefault();
        $(this).parent().find('input[type=text]').val('');
        $(this).parent().find('input[type=number]').val('');
        $(this).parent().find('select').val('');
        $(this).parent().find('input[type=radio]').prop('checked', false);
        $(this).parent().find('input[type=checkbox]').prop('checked', false);
        getTable($('ul.pagination').find('li.active').find('span').html(), true);
    });
    $(document).on('click', '.sorting', function () {
        let valuve = $(this).find('input').val();
        let iconField = $('.sorting').find('i');
        $('.sorting').removeClass("active-sort");
        iconField.removeClass('fa-sort-asc');
        iconField.removeClass('fa-sort-desc');
        $(this).addClass('active-sort');
        if (valuve == 'asc') {
            $(this).find('input').val('desc');
            $(this).find('i').addClass('fa-sort-desc');
        } else {
            $(this).find('input').val('asc');
            $(this).find('i').addClass('fa-sort-asc');
        }
        getTable($('ul.pagination').find('li.active').find('span').html(), true, $(this).find('input').serialize());
    });
}
