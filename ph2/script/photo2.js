	var left_win_gritter = {show:0,top:20,right:20};
        var right_win_gritter = {show:0,top:20,right:20};
        var photo_in_stripe;
        var duble_win;

        var resized4 = 0;
        
        //getCookie
        function getCookie(name) {
                var cookie = " " + document.cookie;
                var search = " " + name + "=";
                var setStr = null;
                var offset = 0;
                var end = 0;
                if (cookie.length > 0) 
                {
                    offset = cookie.indexOf(search);
                    if (offset !== -1) 
                    {
                        offset += search.length;
                        end = cookie.indexOf(";", offset);
                        if (end === -1) {
                            end = cookie.length;
                        }
                        setStr = unescape(cookie.substring(offset, end));
                    }
                }
                return(setStr);
            };
        //resize
        function letsResize()
        {
            //if(getCookie('ch_video_show') === '0')
            //{
                 $('html').css("height","99.9%");
                 $('body').css("height","99.9%");
                if (getCookie('tur_third_win')!='0' &&  getCookie('tur_fours_win')!='0')
                {
                    duble_win=true;
                    var body_width = $('body').width();
                    var half_body_width = body_width/2;
                    var body_height = $('body').height();
                    $('.left_window').width(body_width*0.5);
                    $('.left_window').height(body_height*1);
                    $('.right_window').width(body_width*0.5);
                    $('.right_window').height(body_height*1);
                    //для левого
                    var info_height = $('.left_info_up').height();
                    $('.left_photo_down').height(body_height-info_height-15);
                    var photo_height_l = $('.left_photo_down').height();
                    photo_height_l*0.75 < half_body_width ? $('.left_photo_down').width(photo_height_l*0.75) : $('.left_photo_down').width(half_body_width-10);
                    $('#left_ph').height(body_height-info_height-15);
                    if(photo_height_l*0.75 < half_body_width)
                    {
                        $('#left_ph').width(photo_height_l*0.75); 
                    }
                    else
                    {
                        $('#left_ph').width(half_body_width-10);
                        var new_width=  $('#left_ph').width();
                        $('#left_ph').height(new_width*4/3);
                    }
                    //для правого
                    var info_height = $('.right_info_up').height();
                    $('.right_photo_down').height(body_height-info_height-15);
                    var photo_height_r =  $('.right_photo_down').height();
                    photo_height_r*0.75 < half_body_width ? $('.right_photo_down').width(photo_height_r*0.75) : $('.right_photo_down').width(half_body_width-10);
                    $('#right_ph').height(body_height-info_height-15);
                    if(photo_height_r*0.75 < half_body_width)
                    {    
                        $('#right_ph').width(photo_height_r*0.75);
                    }
                    else
                    {
                         $('#right_ph').width(half_body_width-10);
                         var new_width=  $('#right_ph').width();
                         $('#right_ph').height(new_width*4/3);
                    }
                    
                    if (getCookie('ch_slide_strick') !== '0'){
                        //полоска фоток
                        $('#left_photo_stripe').width(half_body_width-10);
                        $('#right_photo_stripe').width(half_body_width-10);
                        //сколько фоток показываем
                        photo_in_stripe = Math.floor((half_body_width-10)/118); //~118px - ширина фотки в полоске
                        if ($(".stripe_right").length >= photo_in_stripe) {
                            $(".stripe_right").last().remove();
                            $(".stripe_left").last().remove();
                        }
                    }
                    
                }
                if (getCookie('tur_fours_win')==='0')//показывать только левое окно
                {   
                    duble_win=false;
                    $('.left_window').css("border","0");
                    var body_width = $('body').width();
                    var body_height = $('body').height();
                    
                    $('.left_window').css("width","99%").css("height","99%");
                    $('.right_window').hide();
  
                    if (body_width/body_height<3/2)
                    {
                        $('.left_photo_down').css("float","none");
                        $('.left_info_up').css("float","none");
                        $('.left_info_up').css("border-bottom","3px solid #222222");
                        $('.left_info_up').css("width","100%");
                        
                        var info_height = $('.left_info_up').height();
                        $('.left_photo_down').height(body_height-info_height-20);
                        var photo_height_l = $('.left_photo_down').height();
                        photo_height_l*0.75 < body_width ? $('.left_photo_down').width(photo_height_l*0.75) : $('.left_photo_down').width(body_width-10);
                        $('#left_ph').height(body_height-info_height-20);
                        if(photo_height_l*0.75 < body_width)
                        {
                            $('#left_ph').width(photo_height_l*0.75); 
                        }
                        else
                        {
                            $('#left_ph').width(body_width-10);
                            var new_width=  $('#left_ph').width();
                            $('#left_ph').height(new_width*4/3);
                        }
                    }
                    else
                    {
                        $('.left_photo_down').css("float","left");
                        $('.left_info_up').css("float","left");
                                               
                        $('.left_photo_down').height(body_height-15);
                        var photo_height_l = $('.left_photo_down').height();
                        $('.left_photo_down').width(photo_height_l*0.74);
                        
                        $('#left_ph').height(body_height-15);
                        $('#left_ph').width(photo_height_l*0.74); 
                        
                        $('.left_info_up').width(body_width-photo_height_l*0.75-10);
                    }
                    $('.right_info_up').hide();
                    $('.right_photo_down').hide();
                    $('#right_ph').hide();
                    
                    
                    //считаем где будет располагаться gritter (пескоразбрасыватель;) )
                    var left_info_up =$('.left_info_up').height();
                    var left_photo_down = $('.left_photo_down').width();
                    if (body_width-left_photo_down < 310){
                        left_win_gritter.show = 0;
                    }
                    else {
                        left_win_gritter.show = 1;
                        left_win_gritter.top = left_info_up + 20;
                        left_win_gritter.right = (body_width-left_photo_down)/2 - 150;
                    }
                    if($("div").is("#gritter-notice-wrapper")){
                        if(left_win_gritter.show === 1){
                            $('#gritter-notice-wrapper').show();
                            $('#gritter-notice-wrapper').css("right",left_win_gritter.right);
                            $('#gritter-notice-wrapper').css("top",left_win_gritter.top);
                        }
                        else { $('#gritter-notice-wrapper').hide();}
                    }
                }
                if (getCookie('tur_third_win')=='0')//показывать только правое окно
                {
                    duble_win=false;
                    $('.right_window').css("border","0");
                    var body_width = $('body').width();
                    var body_height = $('body').height();
                    $('.right_window').css("width","99%").css("height","99%");
                    $('.left_window').width(body_width-3);
                    $('.left_window').height(body_height-3);
                    $('.left_window').hide();

                    if (body_width/body_height<3/2)
                    {   
                        $('.right_photo_down').css("float","none");
                        $('.right_info_up').css("float","none");
                        $('.right_info_up').css("border-bottom","3px solid #222222");
                        $('.right_info_up').css("width","100%");
                        
                        var info_height = $('.right_info_up').height();
                        $('.right_photo_down').height(body_height-info_height-20);
                        var photo_height_l = $('.right_photo_down').height();
                        photo_height_l*0.75 < body_width ? $('.right_photo_down').width(photo_height_l*0.75) : $('.right_photo_down').width(body_width-10);
                        $('#right_ph').height(body_height-info_height-20);
                        if(photo_height_l*0.75 < body_width)
                        {
                            $('#right_ph').width(photo_height_l*0.75); 
                        }
                        else
                        {
                            $('#right_ph').width(body_width-10);
                            var new_width=  $('#right_ph').width();
                            $('#right_ph').height(new_width*4/3);
                        }
                    }
                    else
                    {
                        $('.right_photo_down').css("float","left");
                        $('.right_info_up').css("float","left");
             
                        $('.right_photo_down').height(body_height-15);
                        var photo_height_r = $('.right_photo_down').height();
                        $('.right_photo_down').width(photo_height_r*0.74);
                        
                        $('#right_ph').height(body_height-15);
                        $('#right_ph').width(photo_height_r*0.74); 
                        
                         $('.right_info_up').width(body_width-photo_height_r*0.75-10)
                    }
                    $('.left_info_up').hide();
                    $('.left_photo_down').hide();
                    $('#left_ph').hide();
                  
                    //считаем где будет располагаться gritter (пескоразбрасыватель;) )
                    var right_info_up =$('.right_info_up').height();
                    var right_photo_down = $('.right_photo_down').width();
                    if (body_width-right_photo_down < 310){
                        right_win_gritter.show = 0;
                    }
                    else {
                        right_win_gritter.show = 1;
                        right_win_gritter.top = right_info_up + 20;
                        right_win_gritter.right = (body_width-right_photo_down)/2 - 150;
                    }
                    if($("div").is("#gritter-notice-wrapper")){
                        if(right_win_gritter.show === 1){
                            $('#gritter-notice-wrapper').show();
                            $('#gritter-notice-wrapper').css("right",right_win_gritter.right);
                            $('#gritter-notice-wrapper').css("top",right_win_gritter.top);
                        }
                        else { $('#gritter-notice-wrapper').hide();}
                    }
                }
                if (getCookie('tur_third_win')=='0' && getCookie('tur_fours_win')=='0')
                {
                     window.close();
                }
            /*}
            
            else
            {
                var body_width = $('body').width();
                var body_height = $('body').height();
                $('.left_window').width(body_width*0.5);
                $('.left_window').height(body_height*1);
                $('.right_window').width(body_width*0.5);
                $('.right_window').height(body_height*1);

                //divs photo
                $('.left_photo').width(body_width*0.3);
                $('.right_photo').width(body_width*0.3);
                var photo_width =  $('.left_photo').width();
                $('.left_photo').height(photo_width*4/3);
                $('.right_photo').height(photo_width*4/3);

                //img
                $('#left_ph').width(body_width*0.3);
                $('#left_ph').height(photo_width*4/3);
                //
                $('#right_ph').width(body_width*0.3);
                $('#right_ph').height(photo_width*4/3);

                //info
                $('.left_info').width(body_width*0.19);
                $('.left_info').height(photo_width*4/3);
                //
                $('.right_info').width(body_width*0.19);
                $('.right_info').height(photo_width*4/3);

                //video
                $('.left_video').width(body_width*0.5);
                $('.left_video').height(body_height-photo_width*4/3-7);
                //
                $('.right_video').width(body_width*0.5);
                $('.right_video').height(body_height-photo_width*4/3-7);
             }*/
        };
        //resize blocks on resize window
        $(window).resize(function() 
        {
            letsResize();

	});

        function cookieConfigSet(){
            //показывать дополнительные поля
            getCookie('ch_dept')     === '0' || getCookie('ch_dept') === 'false' ? $(".dep").hide()  : $("dep").show();
            getCookie('ch_pos')      === '0' || getCookie('ch_pos') === 'false' ? $(".pos").hide()  : $("pos").show();
            getCookie('ch_time_sm')  === '0' || getCookie('ch_time_sm') === 'false' ? $(".sm_t").hide() : $("sm_t").show();
            getCookie('ch_time_din') === '0' || getCookie('ch_time_din') === 'false' ? $(".sm_d").hide() : $("sm_d").show();
            
            if(getCookie('ch_slide_strick') === '0')
            {
                $("left_photo_stripe").hide();
                $("right_photo_stripe").hide();
            }
            else
            {
                $("left_photo_stripe").show();
                $("right_photo_stripe").show();
            }
            
            //установить шрифт, размер и цвет поля
             $('#time_str1').css("font-family",str_replace('_', ' ', getCookie('time_font')));
             $('#time_str2').css("font-family",str_replace('_', ' ', getCookie('time_font')));
             $('#code_descr1').css("font-family",str_replace('_', ' ', getCookie('event_font')));
             $('#code_descr2').css("font-family",str_replace('_', ' ', getCookie('event_font')));
             $('#FIO1').css("font-family",str_replace('_', ' ', getCookie('fio_font')));
             $('#FIO2').css("font-family",str_replace('_', ' ', getCookie('fio_font')));
             $('#dept1').css("font-family",str_replace('_', ' ', getCookie('dept_font')));
             $('#dept2').css("font-family",str_replace('_', ' ', getCookie('dept_font')));
             $('#position1').css("font-family",str_replace('_', ' ', getCookie('pos_font')));
             $('#position2').css("font-family",str_replace('_', ' ', getCookie('pos_font')));
             $('#sm_time1').css("font-family",str_replace('_', ' ', getCookie('smena_font')));
             $('#sm_time2').css("font-family",str_replace('_', ' ', getCookie('smena_font')));
             $('#sm_dinner1').css("font-family",str_replace('_', ' ', getCookie('din_font')));
             $('#sm_dinner2').css("font-family",str_replace('_', ' ', getCookie('din_font')));
             
             $('#time_str1').css("font-size",getCookie('time_font_size'));
             $('#time_str2').css("font-size",getCookie('time_font_size'));
             $('#code_descr1').css("font-size",getCookie('event_font_size'));
             $('#code_descr2').css("font-size",getCookie('event_font_size'));
             $('#FIO1').css("font-size",getCookie('fio_font_size'));
             $('#FIO2').css("font-size",getCookie('fio_font_size'));
             $('#dept1').css("font-size",getCookie('dept_font_size'));
             $('#dept2').css("font-size",getCookie('dept_font_size'));
             $('#position1').css("font-size",getCookie('pos_font_size'));
             $('#position2').css("font-size",getCookie('pos_font_size'));
             $('#sm_time1').css("font-size",getCookie('smena_font_size'));
             $('#sm_time2').css("font-size",getCookie('smena_font_size'));
             $('#sm_dinner1').css("font-size",getCookie('din_font_size'));
             $('#sm_dinner2').css("font-size",getCookie('din_font_size'));
             
             
             $('#time_str1').css("color",getCookie('time_font_color'));
             $('#time_str2').css("color",getCookie('time_font_color'));
             $('#code_descr1').css("color",getCookie('event_font_color'));
             $('#code_descr2').css("color",getCookie('event_font_color'));
             $('#FIO1').css("color",getCookie('fio_font_color'));
             $('#FIO2').css("color",getCookie('fio_font_color'));
             $('#dept1').css("color",getCookie('dept_font_color'));
             $('#dept2').css("color",getCookie('dept_font_color'));
             $('#position1').css("color",getCookie('pos_font_color'));
             $('#position2').css("color",getCookie('pos_font_color'));
             $('#sm_time1').css("color",getCookie('smena_font_color'));
             $('#sm_time2').css("color",getCookie('smena_font_color'));
             $('#sm_dinner1').css("color",getCookie('din_font_color'));
             $('#sm_dinner2').css("color",getCookie('din_font_color'));
             

           $("#left_photo").toggleClass("left_photo_down");
           $("#left_info").toggleClass("left_info_up");

           $('.left_info_up').css("height","auto");
           $('.left_info_up').css("width","100%");

           //right window
           $("#right_photo").toggleClass("right_photo_down");
           $("#right_info").toggleClass("right_info_up");

           $('.right_info_up').css("height","auto");
           $('.right_info_up').css("width","100%");

           letsResize();
           letsResize();//между дивами left_window & right_window при обновлении страницы образовывается зазор, который при ресайсе исчезает. ХЗ откуда он берётся.

            setTimeout(startFK(),1000);

        };
    ;

    function left_win_fk3()
    {
        let win = 1;
        var data = {tur: getCookie('tur_third_win'), size: $('#first_upd_time').val()};
        $.ajax({
            url: "client1.php",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (data)
            {
                let dataObj = eval(data);

                if (dataObj.result !== 'false')
                {
                    let side = win === 1 ? 'left' : 'right';
                    let number = win === 1 ? 'first' : 'second';

                    console.log(dataObj.script_time);
                    $('#code_descr'+win).html(dataObj.code_descr);
                    $('#time_str'+win).html(dataObj.time_str);
                    $('#FIO'+win).html(dataObj.FIO);
                    $('#dept'+win).html(dataObj.dept);
                    $('#position'+win).html(dataObj.position);
                    $('#sm_time'+win).html(dataObj.sm_time);
                    $('#sm_dinner'+win).html(dataObj.sm_dinner);
                    $('#photo_name'+win).html(dataObj.photo_name);
                    dataObj.photo_name==="" ? $('#'+side+'_ph').attr('src', 'img/no.jpg') : $('#'+side+'_ph').attr('src', '../foto/'+dataObj.photo_name);
                    $('#'+number+'_upd_time').val(dataObj.update_size);
                    dataObj.red_code === 1 ? $('#'+side+'_photo').css('border','3px solid red'): $('#'+side+'_photo').css('border','1px solid green');

                    //gritter_go(dataObj);

                    if (resized2 === 0)
                    {
                        letsResize();
                        resized2 = 1;
                    }
                    side = null;
                    number = null;
                }
                dataObj = null;

            },
            error: function (xhr, ajaxOptions, thrownError)
            {
                console.log(xhr);
                console.log(ajaxOptions);
                console.log(thrownError);
            }
        });
    }
    function right_win_fk3()
    {
        let win = 2;
        var data = {tur: getCookie('tur_fours_win') , size: $('#second_upd_time').val()};
        $.ajax({
            url: "client1.php",
            type: "POST",
            data: data,
            dataType: "json",
            success: function (data)
            {
                let dataObj = eval(data);

                if (dataObj.result !== 'false')
                {
                    let side = win === 1 ? 'left' : 'right';
                    let number = win === 1 ? 'first' : 'second';

                    console.log(dataObj.script_time);
                    $('#code_descr'+win).html(dataObj.code_descr);
                    $('#time_str'+win).html(dataObj.time_str);
                    $('#FIO'+win).html(dataObj.FIO);
                    $('#dept'+win).html(dataObj.dept);
                    $('#position'+win).html(dataObj.position);
                    $('#sm_time'+win).html(dataObj.sm_time);
                    $('#sm_dinner'+win).html(dataObj.sm_dinner);
                    $('#photo_name'+win).html(dataObj.photo_name);
                    dataObj.photo_name==="" ? $('#'+side+'_ph').attr('src', 'img/no.jpg') : $('#'+side+'_ph').attr('src', '../foto/'+dataObj.photo_name);
                    $('#'+number+'_upd_time').val(dataObj.update_size);
                    dataObj.red_code === 1 ? $('#'+side+'_photo').css('border','3px solid red'): $('#'+side+'_photo').css('border','1px solid green');

                    //gritter_go(dataObj);

                    if (resized2 === 0)
                    {
                        letsResize();
                        resized2 = 1;
                    }
                    side = null;
                    number = null;
                }
                dataObj = null;

            },
            error: function (xhr, ajaxOptions, thrownError)
            {
                console.log(xhr);
                console.log(ajaxOptions);
                console.log(thrownError);
            }
        });
    }




        function startFK()
        {
            var data1 = {act:"wait_ajax"};
            $.ajax({
             url: "photo.php", 
             type: "POST",
             data: data1,
             dataType: "text",
             success:function()
             {
                     if (getCookie('tur_third_win') > 0) {
                         setInterval(function() {
                         left_win_fk3();
                             console.log($('#first_upd_time').val());
                         }, 300);
                     }
                     if (getCookie('tur_fours_win') > 0) {
                         setInterval(function() {
                         right_win_fk3();
                             console.log($('#second_upd_time').val());
                        }, 300);
                     }
                     console.log($('#first_upd_time').val());
                     console.log($('#second_upd_time').val());
             },
             error: function (xhr, ajaxOptions, thrownError)
             {
                 console.log(xhr);
                 console.log(ajaxOptions);
                 console.log(thrownError);
             }
             });
        }
        //обновление инфы о проходах на странице
        $(document).ready(function() {
            //есть ли хоть какая-нибудь кука? 
            var testcoockie = getCookie('tur_third_win');
            if (testcoockie === null)
            {
                window.close();
            }
            else cookieConfigSet();
            
         });

         //универсальный реплэйсик      
         function str_replace ( search, replace, subject )
             { // Replace all occurrences of the search string with the replacement string
             if(!(replace instanceof Array)){
                 replace=new Array(replace);
                 if(search instanceof Array){//If search is an array and replace is a string, then this replacement string is used for every value of search
                     while(search.length>replace.length){
                         replace[replace.length]=replace[0];
                     }
                 }
             }
             if(!(search instanceof Array))search=new Array(search);
             while(search.length>replace.length){//If replace has fewer values than search , then an empty string is used for the rest of replacement values
                 replace[replace.length]='';
             }
             if(subject instanceof Array){//If subject is an array, then the search and replace is performed with every entry of subject , and the return value is an array as well.
                 for(k in subject){
                     subject[k]=str_replace(search,replace,subject[k]);
                 }
                 return subject;
             }
             for(var k=0; k<search.length; k++){
                 var i = subject.indexOf(search[k]);
                 while(i>-1){
                     subject = subject.replace(search[k], replace[k]);
                     i = subject.indexOf(search[k],i);
                 }
             }
             return subject;
         }
