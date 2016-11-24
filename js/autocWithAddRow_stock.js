/**
 * Site : http:www.smarttutorials.net
 * @author muni
 */

$(".delete").on('click', function () {
    $('.case:checkbox:checked').parents("tr").remove();
    $('.check_all').prop("checked", false);
    check();
});
var i = $('table tr').length;

$(".addmore").on('click', function () {
    count = $('table tr').length;

    var data = "<tr><td><input type='checkbox' class='case'/></td><td><span id='snum" + i + "'>" + count + ".</span></td>";
    data += "<td><input class='form-control' type='number' id='varKID_" + i + "' name='varKID[]' required readonly/></td>\n\
<td><input class='form-control' type='text' id='varZDIR_" + i + "' name='varZDIR[]' required/></td>\n\
<td><input class='form-control' type='text' id='varDetail_" + i + "' name='varDetail[]' maxlength=\"100\" required readonly/></td> \n\
<td><input class='form-control' type='text' id='varSlipSuffix_" + i + "' name='varSlipSuffix[]' required/></td> \n\
<td><input class='form-control' type='number' id='varQty_" + i + "' name='varQty[]' required/></td> \n\
<td bgcolor='#ffffe6'><input class='form-control' type='text' id='varLastSuffix_" + i + "' name='varLastSuffix[]' required readonly/></td>\n\
<td bgcolor='#ffffe6'><input class='form-control' type='number' id='varLastQty_" + i + "' name='varLastQty[]' required/></td></tr>";
    $('table').append(data);
    row = i;
    $('#varZDIR_' + i).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: 'add_autoc_ajax_stock.php',
                dataType: "json",
                method: 'post',
                data: {
                    name_startsWith: request.term,
                    type: 'item_table',
                    row_num: row
                },
                success: function (data) {
                    response($.map(data, function (item) {
                        var code = item.split("|");
                        return {
                            label: code[0],
                            value: code[0],
                            data: item
                        };
                    }));
                }
            });
        },
        autoFocus: true,
        minLength: 0,
        select: function (event, ui) {
            var names = ui.item.data.split("|");
            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            $('#varDetail_' + id[1]).val(names[1]);
            $('#varSlipSuffix_' + id[1]).val(names[2]);
            $('#varLastSuffix_' + id[1]).val(names[3]);
            $('#varLastQty_' + id[1]).val(names[4]);
            $('#varKID_' + id[1]).val(names[5]);
        }
    });
    i++;
});

function select_all() {
    $('input[class=case]:checkbox').each(function () {
        if ($('input[class=check_all]:checkbox:checked').length == 0) {
            $(this).prop("checked", false);
        } else {
            $(this).prop("checked", true);
        }
    });
}

function check() {
    obj = $('table tr').find('span');
    $.each(obj, function (key, value) {
        id = value.id;
        $('#' + id).html(key + 1);
    });
}

$('#varZDIR_1').autocomplete({
    source: function (request, response) {
        $.ajax({
            url: 'add_autoc_ajax_stock.php',
            dataType: "json",
            method: 'post',
            data: {
                name_startsWith: request.term,
                type: 'item_table',
                row_num: 1
            },
            success: function (data) {
                response($.map(data, function (item) {
                    var code = item.split("|");
                    return {
                        label: code[0],
                        value: code[0],
                        data: item
                    }
                }));
            }
        });
    },
    autoFocus: true,
    minLength: 0,
    select: function (event, ui) {
        var names = ui.item.data.split("|");
        $('#varDetail_1').val(names[1]);
        $('#varSlipSuffix_1').val(names[2]);
        $('#varLastSuffix_1').val(names[3]);
        $('#varLastQty_1').val(names[4]);
        $('#varKID_1').val(names[5]);
    }
});
