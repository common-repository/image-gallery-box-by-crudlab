jQuery(document).ready(function ($) {
    jQuery("[data-toggle='switch']").bootstrapSwitch();

    if ($('[data-toggle="select"]').length) {
        $('[data-toggle="select"]').select2();
    }

    $('[data-toggle="checkbox"]').radiocheck();
    $('[data-toggle="radio"]').radiocheck();

    jQuery('[name="status"]').on('switchChange.bootstrapSwitch', function (event, state) {
        $.post(ajaxurl, {action: "clgbactive", status: state}, function (data) {
            if (data.status == 1) {
                jQuery('#clgp_circ').css("background", "#0f0");
            } else {
                jQuery('#clgp_circ').css("background", "#f00");
            }
        }, "json");
    });

    jQuery('[name="box_style"]').on("change", function (e) {
        $.post(ajaxurl, {action: "clgbphtml", style: e.val}, function (data) {
            $('#boxpophtml').html(data);
        }, "html");
    });



    $('.btn-preview').click(function () {
        $('.c-popup-wrap .vlightbox-wrap').remove();
        $('.c-popup-wrap').append($('#boxpophtml').html());
        $('.c-popup-wrap').show();
        var incls = $('[name="animation"] option:selected').data('aniin');
        $('.c-popup-wrap .vlightbox-wrap').addClass('animated ' + incls);
        if($('[name="show_title"]:checked').val() == "0"){
            $('.c-popup-wrap .imagedata .title').hide();
        }   
    });

    $('.c-popup-wrap').on('click', '.crdclose', function () {
        var incls = $('[name="animation"] option:selected').data('aniout');
        $('.c-popup-wrap .vlightbox-wrap').addClass('animated ' + incls);
        if (incls !== 'none') {
            $('.c-popup-wrap .vlightbox-wrap').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                $('.c-popup-wrap .vlightbox-wrap').remove();
                $('.c-popup-wrap').hide();
            });
        } else {
            $('.c-popup-wrap .vlightbox-wrap').remove();
            $('.c-popup-wrap').hide();
        }
    });   
});