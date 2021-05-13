
$(document).ready(function() {
    //статистика сервера
    var data1 = {act:"schema_serv"};
        $.ajax({
         url: "../techreport/asinc.php", 
         type: "POST",
         data: data1,
         dataType: "text",
        success: function (data) {
            var dataObj = JSON.parse(data);
            
            if (dataObj.res === '0'){alert('Ошибка получения данных для сервера'); return;}
            if (dataObj.res === '1'){
                dataObj.tbl_log > 0 ? $('#t_log').css("background-color","#FF6C6D") : $('#t_log').css("background-color","#71E08A");
                dataObj.left_ev > 0 ? $('#left_ev').css("background-color","#FF6C6D") : $('#left_ev').css("background-color","#71E08A");
                
                $('#t_log').attr("title", dataObj.tbl_log);
                if (dataObj.tbl_log != '0')
                {
                    $('#t_log').click(function(){
                        window.open("http://"+location.host+"/techreports.php?schema_request=1&rtype=1", '_blank');
                    });
                }
                $('#left_ev').attr("title", dataObj.left_ev);
                if (dataObj.left_ev != '0')
                {
                    $('#left_ev').click(function(){
                        window.open("http://"+location.host+"/techreports.php?full_check=on&rtype=6", '_blank');
                    });
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr);
            alert(ajaxOptions);
            alert(thrownError);
            alert('error get info for proc errors and left events');
        }
        });
    
    
    
    
    
    //статистика турникетов
    $(".div_content_turn").each(function(){
	var id = $(this).attr('id');
        var turn = id.split("_")[1];
       
        var data1 = {act:"schema_turn", turn:turn};
        $.ajax({
         url: "../techreport/asinc.php", 
         type: "POST",
         data: data1,
         dataType: "text",
        success: function (data) {
            var dataObj = JSON.parse(data);
            
            if (dataObj.res === '0'){alert('Ошибка получения данных для устройства '+turn); return;}
            if (dataObj.res === '1'){
                var test = 0;
                var t_interval = dataObj.over_interval.split("_");
                if(t_interval[0].length < 10) test = t_interval[0];
                if ( dataObj.count !== '0'){
                    dataObj.c > 0 ? $('#rs_'+turn).css("background-color","#FF6C6D") : $('#rs_'+turn).css("background-color","#71E08A");
                    dataObj.doublek > 0 ? $('#line_'+turn).css("background-color","#FF6C6D") : $('#line_'+turn).css("background-color","#71E08A");
                    dataObj.err_reader > 0 ? $('#reader_'+turn).css("background-color","#FF6C6D") : $('#reader_'+turn).css("background-color","#71E08A");
                    dataObj.uncomplete_p > 10 ? $('#io_'+turn).css("background-color","#FF6C6D") : $('#io_'+turn).css("background-color","#71E08A");
                    test > 10 ? $('#test_'+turn).css("background-color","#FF6C6D") : $('#test_'+turn).css("background-color","#71E08A");
                    dataObj.fire > 0 ? $('#fire_'+turn).css("background-color","#FF6C6D") : $('#fire_'+turn).css("background-color","#71E08A");
                    dataObj.w_p > 10 ? $('#reg_'+turn).css("background-color","#FF6C6D") : $('#reg_'+turn).css("background-color","#71E08A");
                    dataObj.apb_p > 10 ? $('#zas_'+turn).css("background-color","#FF6C6D") : $('#zas_'+turn).css("background-color","#71E08A");
                    dataObj.d_p > 0 ? $('#dop_'+turn).css("background-color","#FF6C6D") : $('#dop_'+turn).css("background-color","#71E08A");
                    dataObj.x_p > 0 ? $('#graph_'+turn).css("background-color","#FF6C6D") : $('#graph_'+turn).css("background-color","#71E08A");
                }
                else{
                    $('#rs_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#line_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#reader_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#io_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#test_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#fire_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#reg_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#zas_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#dop_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#graph_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                    $('#graph_'+turn).css({"background-color":"#CDCDCD","color":"gray"});
                }
                $('#rs_'+turn).attr("title", dataObj.c + ' ('+dataObj.c_p+'%)');
                if (dataObj.c != '0')
                {
                    $('#rs_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=1", '_blank');
                    });
                }
                $('#line_'+turn).attr("title", dataObj.doublek);
                if (dataObj.doublek != '0')
                {
                    $('#line_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=9", '_blank');
                    });
                }
                
                $('#reader_'+turn).attr("title", dataObj.err_reader);
                if (dataObj.err_reader != '0')
                {
                    $('#reader_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=8", '_blank');
                    });
                }
                
                $('#io_'+turn).attr("title", dataObj.uncomplete + ' ('+dataObj.uncomplete_p+'%)');
                if (dataObj.uncomplete != '0')
                {
                    $('#io_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=5", '_blank');
                    });
                }
                
                if(t_interval[1] != 1000){
                    $('#test_'+turn).attr("title", t_interval[0] + ' (интервал '+t_interval[1]+')')
                }
                else {$('#test_'+turn).attr("title", t_interval[0]);}
                if (t_interval[0] != '0')
                {
                    $('#test_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=10&interval="+t_interval[1], '_blank');
                    });
                }
                
                $('#fire_'+turn).attr("title", dataObj.fire);
                if (dataObj.fire != '0')
                {
                    $('#fire_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=7", '_blank');
                    });
                }
                
                $('#reg_'+turn).attr("title", dataObj.w + ' ('+dataObj.w_p+'%)');
                if (dataObj.w != '0')
                {
                    $('#reg_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=4", '_blank');
                    });
                }
                
                $('#zas_'+turn).attr("title", dataObj.apb + ' ('+dataObj.apb_p+'%)');
                if (dataObj.apb != '0')
                {
                    $('#zas_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=6", '_blank');
                    });
                }
                
                $('#dop_'+turn).attr("title", dataObj.d + ' ('+dataObj.d_p+'%)');
                if (dataObj.d != '0')
                {
                    $('#dop_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=3", '_blank');
                    });
                }
                
                $('#graph_'+turn).attr("title", dataObj.x + ' ('+dataObj.x_p+'%)');
                if (dataObj.x != '0')
                {
                    $('#graph_'+turn).click(function(){
                        window.open("http://"+location.host+"/techreports.php?turn="+turn+"&rtype=101&type=2", '_blank');
                    });
                }
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert('error get info for '+turn);
            alert(xhr);
            alert(ajaxOptions);
            alert(thrownError);
        }
        });
    });
    
    
 });
