function fetchSearchedClients(url, query = '', translation) {
    if (query === '' || query === null) {
        $('#clients').hide();
        return;
    }

    $.ajax({
        url: url,
        method: 'GET',
        data: {query: query},
        dataType: 'json',
        success: function (data) {
            if ($.trim(data)) {
                $('.clients-list').text('');
                $('#clients').show();
                if (!data.length) {
                    $('.clients-list').text(translation.notFoundClient);
                }
                let html = '';
                $.each(data, function (index, value) {
                    html += '<a  href = ' + urlClientProfile + '/' + value.client_id + '>';
                    if (value.flag === 'pin') {
                        html += '#' + value.pin + ' | ';
                    } else if (value.flag === 'phone') {
                        html += '+' + value.phone + ' | ';
                    } else if(value.flag === 'email') {
                        html += '@' + value.email + ' | ';
                    }

                    html += value.name;
                    html += '</a>';
                });
                $('#clients').html(html);
            } else {
                hideUserSearch('onKeyup');
            }
        }
    })
}

function hideUserSearch(action) {
    let element = document.getElementById("clients");

    if (element.innerHTML.trim().length != 0) {
        element.style = "display:none";
        element.innerHTML = "";
    }
    if (action === 'onClick') {
        $("#search").val("");
    }
}

$("#search").focusin(function () {
    $(this).parent().animate({width: "300px"});
});
$("#search").focusout(function () {
    $(this).parent().animate({width: "150px"});
});
$("#search").on('keyup', function () {
    let query = $(this).val();
    fetchSearchedClients(url, query, translation);
});
window.onclick = function (event) {
    hideUserSearch('onClick');
}

