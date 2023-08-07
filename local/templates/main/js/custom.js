$(function () {
    var $filter = $('.js-sm-filter');
    if ($filter.length) {
        $('body').addClass('filter');
        userLoad();
    }
    if(!getCookie('BITRIX_SM_cookie_use'))
    {
        $('.cookie-bar').show();
    }
});


$(document).on('submit', '.js-form', function (e) {
    //console.log('submit');
    e.preventDefault();
    var $form = $(this),
        $closest = $form.parent(),
        data = $form.serialize(),
        $files_inp = $form.find('.js-file');
    //console.log($files_inp.length);
    //console.log(data); return;
    $.ajax({
        url:'#',
        data:data,
        type:'POST',
        dataType:'json',
        cache: false,
        success: function(json)
        {
            if(json.SEND)
            {
                $closest.append('<div class="text-center">'+ json.SEND + '</div>');
                $form.fadeOut(500, function () {
                    $form.remove();
                });
                //$closest.html($('#tnx').html());
                /*if(!$('.js-tnx').length)
                {
                    $('body').append('<a href="#tnx" class="js-tnx" data-modal>');
                }
                $('.js-tnx').trigger('click');
                $form.find('input[type=text], input[type=email], textarea').val('');*/
            }
            if(json.ERROR)
            {
                if (!$form.find('.cs-err').length)
                {
                    $form.append('<div class="cs-err text-center">'+ json.ERROR + '</div>');
                }
                else
                {
                    $form.find('.cs-err').text(json.ERROR);
                }
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText + '|\n' + status + '|\n' +error);
        },
    });
});


// показать ещё
$(document).on('click', '.show_more_js', function (e) {
    e.preventDefault();
    var targetContainer = $('.js-prlist.custom'),          //  Контейнер, в котором хранятся элементы
        url = $(this).attr('href');    //  URL, из которого будем брать элементы
    if (url !== undefined) {
        $.ajax({
            type: 'GET',
            url: url,
            dataType: 'html',
            success: function (data) {
                //  Удаляем старую навигацию
                $('.pagination').replaceWith($(data).find('.pagination'));
                var elements = $(data).find('.js-prlist.custom > li'),  //  Ищем элементы
                    pagination = $(data).find('.pagination');//  Ищем навигацию
                targetContainer.append(elements);   //  Добавляем посты в конец контейнера
                //app.prZIndex();
                var pr_z_index = $('.js-prlist>li:last-child').css('z-index');
                if (pr_z_index == 'auto') {
                    pr_z_index = 0;
                }
                $('.pr-list>li').each(function () {
                    $(this).css('z-index', pr_z_index);
                    pr_z_index--;
                });
            }
        })
    }
});

// фильтрация по году
$(document).on('click', '.js-sel_year button', function (e) {
    var targetContainer = $('.js-prlist.custom'),          //  Контейнер, в котором хранятся элементы
        year = $(this).data('val');    //  URL, из которого будем брать элементы
    if (year !== undefined) {
        $.ajax({
            type: 'GET',
            url: '',
            data: {
                year:year
            },
            dataType: 'html',
            success: function (data) {
                //  Удаляем старую навигацию
                $('.pagination').replaceWith($(data).find('.pagination'));
                var elements = $(data).find('.js-prlist.custom > li'),  //  Ищем элементы
                    pagination = $(data).find('.pagination');//  Ищем навигацию
                targetContainer.html(elements);   //  Добавляем посты в конец контейнера
                //app.prZIndex();
                var pr_z_index = $('.js-prlist>li:last-child').css('z-index');
                if (pr_z_index == 'auto') {
                    pr_z_index = 0;
                }
                $('.pr-list>li').each(function () {
                    $(this).css('z-index', pr_z_index);
                    pr_z_index--;
                });
            }
        })
    }
});



function eventHandler(toAjax,
                      href,
                      dataSend,
                      dataType,
                      hasForm,
                      bModalWindow,
                      onSuccess) {
    if (!!toAjax) {
        if (hasForm) {
            dataSend.append('AJAX', toAjax);
        }
        else {
            dataSend.AJAX = toAjax;
        }
    }
    //console.log(dataSend);

    var data = {
        url: !href ? window.location.href : href,
        data: dataSend,
        type: 'post',
        dataType: dataType,
        success: function (result) {
            if (onSuccess) // SEND CALLBACK
            {
                return onSuccess(result);
            }

            if (bModalWindow) {
                $.fancybox.close();
                $.fancybox.open(result, oPage.fancyOBJ);
            }
        },
        error: function(xhr, status, error) {
            console.log(xhr.responseText + '|\n' + status + '|\n' +error);
        }
    };

    if (hasForm) {
        data.processData = false;
        data.contentType = false;
    }

    $.ajax(data);

    return false;
}


function number_format(str) {
    return String(str).replace(/(\s)+/g,'').replace(/(\d{1,3})(?=(?:\d{3})+$)/g,'$1 ');
}


function setCookie(name, value, time) {
    var valueEscaped = encodeURI(value);
    var expiresDate = new Date();
    if(!time)
    {
        time = 365 * 24 * 60 * 60 * 1000;
    }
    expiresDate.setTime(expiresDate.getTime() + time); // год
    var expires = expiresDate.toGMTString();
    var newCookie = name + "=" + valueEscaped + "; path=/; expires=" + expires;
    if (valueEscaped.length <= 4000) document.cookie = newCookie + ";";
}

function getCookie(name) {
    var prefix = name + "=";
    var cookieStartIndex = document.cookie.indexOf(prefix);
    if (cookieStartIndex == -1) return null;
    var cookieEndIndex = document.cookie.indexOf(";", cookieStartIndex + prefix.length);
    if (cookieEndIndex == -1) cookieEndIndex = document.cookie.length;
    return decodeURI(document.cookie.substring(cookieStartIndex + prefix.length, cookieEndIndex));
}

function DelCookie(sName) {document.cookie = sName + "=; expires=Fri, 31 Dec 1999 23:59:59 GMT; path=/;";}