$(document).ready(function() {
    $(".dropdown-menu li a").click(function () {
        var selText = $(this).text();
        var selId = $(this).data('select-id');
        var imgSource = $(this).find('img').attr('src');
        var img = '<img src="' + imgSource + '"/>';
        $('.label-position-input').val(selId);
        $(this).parents('.btn-group').find('.dropdown-toggle').html(img + ' ' + selText + ' <span class="caret"></span>');
    });
});
