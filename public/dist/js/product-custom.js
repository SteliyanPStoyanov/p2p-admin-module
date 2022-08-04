$('#product_group_id').on('change', function () {
    let card = $("#productSettings").parent().parent();
    card.show();
    $.ajax({
        url: urlGetSettingByProductGroup,
        data: {
            "product_group_id": this.value
        },
        type: 'GET',
        headers: {
            "Accept": "application/json",
        },
        success: function (data) {
            let productSettingsDiv = $('#productSettings');
            if (!$.isArray(data)) {
                productSettingsDiv.html('' +
                    '<div class="form-group">' +
                    '<p class="text-danger">' +
                    data +
                    '</p>' +
                    '</div>'
                );

                return;
            }

            let html = ''
            data.forEach(function (index) {
                console.log(index);
                html += '<div class="form-group">';
                html += '<label for="value_' + index.key + '">' + index.name + '</label>';
                html += '<input type="text" class="form-control" name="product_settings[' + index.name + ']" id="value_' + index.key + '" value="' + index.value + '">'
                html += '</div>';
            });
            productSettingsDiv.html(html);
        }
    });
})
