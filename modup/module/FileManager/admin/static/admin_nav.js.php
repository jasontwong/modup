$(function(){
    var link = $('<a />').text('File Manager');
    link
        .click(function(){
            var win_params = [
                'height=400',
                'width=800',
                'scrollbars=yes',
                'toolbar=no',
                'location=no',
                'menubar=no',
                'copyhistory=no',
                'directories=no'
            ];
            window.open('/admin/mod/FileManager/browse/','file_browse', win_params.join());
        });
    $('#nav .tools')
        .next('ul')
        .append($('<li />').append(link));
});
