(function ($) {
    var settings, imgList, cutI;
    jQuery.fn.crudgallery = function (options) {
        var defaults = {
            amiIn: "none",
            amiOut: "none",
            showtitle: 1,
        };
        settings = $.extend({}, defaults, options);
        imgList = this;
        $(imgList).click(iclick);
        $('[data-caction="next"]').click(nextimage);
        $('[data-caction="prev"]').click(previmage);
        $('.c-popup-wrap').on('click', '.crdclose', close);
        $('.c-popup-wrap .vlightbox-wrap').append('<div class="crd-ajax-loader"></div>');
        $('.c-popup-wrap .imagecontainer img').attr('src', '');
        $(window).unload(function () {
            return "Bye now!";
        });
    };

    var iclick = function () {
        cutI = $(imgList).index(this);
        $('.c-popup-wrap .vlightbox-wrap').css('top', jQuery(window).scrollTop() + 50);
        $('.c-popup-wrap').show();
        $('.c-popup-wrap .vlightbox-wrap').addClass('animated ' + settings.amiIn);
        if (settings.showtitle) {
            $('.c-popup-wrap .imagedata .title').show();
        } else {
            $('.c-popup-wrap .imagedata .title').hide();
        }

        nextprev();
        loadimage($(this));
        return false;
    };

    var showLoader = function () {
        $('.crd-ajax-loader').show();
    }
    var hideLoader = function () {
        $('.crd-ajax-loader').hide();
    }

    var nextprev = function () {
        var tt = $(imgList).length;
        $('.imagecount .inline-block:first').html(cutI + 1 + "/" + tt);
        if (cutI + 1 == tt) {
            $('[data-caction="next"]').hide();
        } else {
            $('[data-caction="next"]').show();
        }

        if (cutI == 0) {
            $('[data-caction="prev"]').hide();
        } else {
            $('[data-caction="prev"]').show();
        }
    };

    var nextimage = function () {
        var elem = ($(imgList).get(cutI + 1));
        cutI = cutI + 1;
        nextprev();
        loadimage(elem);
        return false;
    }

    var previmage = function () {
        var elem = ($(imgList).get(cutI - 1));
        cutI = cutI - 1;
        nextprev();
        loadimage(elem);
        return false;
    };

    var setTitle = function (elem) {
        var text = '';

        if (typeof $(elem).attr('title') !== "undefined") {
            text = $(elem).attr('title');
        } else if (typeof $(elem).attr('data-title') !== "undefined") {
            text = $(elem).attr('data-title');
        } else {
            text = $(elem).find('img[alt]').attr('alt');
        }

        $('.c-popup-wrap .imagedata .title').text(text);
    };

    var loadimage = function (elem) {
        var href = $(elem).attr('href')
        var photo = new Image();

        $(photo)
                .bind('error', function () {
                    debug('as');
                    hideLoader();
                })
                .one('load', function () {


                    // A small pause because some browsers will occassionaly report a 
                    // img.width and img.height of zero immediately after the img.onload fires
                    setTimeout(function () {
                        var percent;

//                        if (settings.get('scalePhotos')) {
//                            setResize = function () {
//                                photo.height -= photo.height * percent;
//                                photo.width -= photo.width * percent;
//                            };
//                            if (settings.mw && photo.width > settings.mw) {
//                                percent = (photo.width - settings.mw) / photo.width;
//                                setResize();
//                            }
//                            if (settings.mh && photo.height > settings.mh) {
//                                percent = (photo.height - settings.mh) / photo.height;
//                                setResize();
//                            }
//                        }
//                        debug('Window height ' + getScreenHeight());
//                        debug("Photo Height " + photo.height + " width " + photo.width);

                        if (photo.height > getScreenHeight()) {
//                            debug("New height "+ getNewheight());
                            var h = getNewheight();
                            photo.width = Math.round(photo.width / photo.height * h);
                            photo.height = h;
//                            debug("New photo Height " + photo.height + " width " + photo.width);
                        }

                        photo.style.width = photo.width + 'px';
                        photo.style.height = photo.height + 'px';

//                        setTimeout(function () {
                        $('.crd-ajax-loader').hide(0, function () {
                            prep(photo);
                            setTitle(elem);
                        });

//                        hideLoader();

//                        }, 1000)


                    }, 1);


                });
        $('.c-popup-wrap .imagecontainer img').attr('src', '');
        if (!cached(href)) {
            showLoader();
        }
        photo.src = href;
    };

    function cached(url) {
        var test = document.createElement("img");
        test.src = url;
        return test.complete || test.width + test.height > 0;
    }

    function debug(obj) {
        if (window.console && window.console.log) {
            window.console.log(obj);
        }
    }

    var getScreenHeight = function () {
        return getNewheight();
        return jQuery(window).height() - 100;
    }

    var getScreenWidth = function () {
        return jQuery(window).width() - 100;
    }

    var getNewheight = function () {
        var h = jQuery(window).height();
        return Math.round(h - (h / 100) * 30);
    }

    var getNewwidth = function () {
        var w = jQuery(window).width();
        return Math.round(w - (w / 100) * 30);
    }


    var prep = function (object) {
        $('.c-popup-wrap .imagecontainer img').attr('src', object.src);
        $('.c-popup-wrap .vlightbox-wrap').css('max-width', '85%');
        $('.c-popup-wrap .imagecontainer img').animate({
            width: object.width,
            height: object.height
        }, 500);

        $('.c-popup-wrap .vlightbox-wrap').animate({
            width: object.width,
        }, 500);

    };

    var close = function () {
        $('.c-popup-wrap .vlightbox-wrap').addClass('animated ' + settings.amiOut);
        if (settings.amiOut !== 'none') {
            $('.c-popup-wrap .vlightbox-wrap').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function () {
                $('.c-popup-wrap .vlightbox-wrap').removeClass(settings.amiOut + ' animated ' + settings.amiIn);
                $('.c-popup-wrap').hide();
            });
        } else {
            $('.c-popup-wrap').hide();
            $('.c-popup-wrap .vlightbox-wrap').removeClass(settings.amiOut + ' animated ' + settings.amiIn);
        }
    };
}(jQuery));