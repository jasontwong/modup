(function($){
    $(function(){
        $('.fields_table .field input.table')
            .each(function(){
                var el = $(this),
                    value = el.val(),
                    values = value == '' ? [] : JSON.parse(value),
                    table = $('<table />'),
                    table_tr = $('<tr />'),
                    table_td = $('<td />'),
                    table_th = $('<th />'),
                    input = $('<input type="text" />'),
                    add_row = $('<a />'),
                    add_col = $('<a />'),
                    del_row = $('<img src="/file/module/Inventory/img/option-close.png" />'),
                    del_col = $('<img src="/file/module/Inventory/img/option-close.png" />'),
                    tr, td, th;
                table_td.css('padding', '3px');
                table_th.css('text-align', 'center');
                add_row
                    .text('Add Row')
                    .click(function(){
                        var row = $('tr:last', table).clone(true);
                        $('input', row).val('');
                        table
                            .append(row)
                            .trigger('update');
                    });
                add_col
                    .text('Add Column')
                    .click(function(){
                        var rows = $('tr', table);
                        rows.each(function(){
                            var row = $(this),
                                col = $('> *:last', row).clone(true);
                            $('input', col).val('');
                            row.append(col);
                        });
                        table.trigger('update');
                    });
                del_row
                    .css('cursor', 'pointer')
                    .click(function(){
                        $(this).closest('tr').remove();
                        table.trigger('update');
                    });
                del_col
                    .css('cursor', 'pointer')
                    .click(function(){
                        var ele = $(this).closest('th'),
                            cols = $('th', ele.closest('tr'));
                        cols.each(function(i){
                            if (ele.is(this))
                            {
                                $('tr', table)
                                    .each(function(){
                                        $('td', this).eq(i).remove();
                                    });
                                ele.remove();
                                table.trigger('update');
                                return false;
                            }
                        });
                    });
                table
                    .bind('update', function(){
                        var rows = $('tr:not(:first)', $(this));
                        values = [];
                        rows.each(function(x){
                            var cols = $('td:not(:first)', $(this));
                            cols.each(function(y){
                                $('input', $(this))
                                    .data('x', x)
                                    .data('y', y)
                                    .trigger('keyup');
                            });
                        });
                    });
                input
                    .css({
                        'height' : '22px',
                        'width' : '89px'
                    })
                    .data('x', 0)
                    .data('y', 0)
                    .keyup(function(){
                        var ele = $(this),
                            x = ele.data('x'),
                            y = ele.data('y');
                        if (values[x] == undefined)
                        {
                            values[x] = [];
                        }
                        values[x][y] = ele.val();
                        el.val(admin.JSON.make(values));
                    });
                if (values.length)
                {
                    for (x in values)
                    {
                        tr  = table_tr.clone();
                        if (x == 0)
                        {
                            th = table_th.clone();
                            tr.append(th);
                            th = '';
                            for (i=0;i<values[x].length;i++)
                            {
                                th = table_th.clone();
                                th.append(del_col.clone(true));
                                tr.append(th);
                                th = '';
                            }
                            table.append(tr);
                            tr = '';
                            tr  = table_tr.clone();
                        }
                        td = table_td.clone();
                        td.append(
                            del_row.clone(true)
                        );
                        tr.append(td);
                        td = '';
                        for (y in values[x])
                        {
                            td = table_td.clone();
                            td.append(
                                input
                                    .clone(true)
                                    .data('x', x)
                                    .data('y', y)
                                    .val(values[x][y])
                            );
                            tr.append(td);
                            td = '';
                        }
                        table.append(tr);
                        tr = '';
                    }
                }
                else
                {
                    tr  = table_tr.clone();
                    th = table_th.clone();
                    tr.append(th);
                    th = table_th.clone();
                    th.append(del_col.clone(true));
                    tr.append(th);
                    th = '';
                    table.append(tr);
                    tr = '';
                    tr  = table_tr.clone();
                    td = table_td.clone();
                    td.append(
                        del_row.clone(true)
                    );
                    tr.append(td);
                    td = table_td.clone();
                    td.append(
                        input.clone(true)
                    );
                    tr.append(td);
                    table.append(tr);
                    td = '';
                    tr = '';
                }
                el.after(table).after(add_col).after(add_row);
            });
    });
})(jQuery);
