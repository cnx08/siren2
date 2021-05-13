	var stop_animation = false;
        var cnt = 0;
        function get_px_code(p) {
            var arr_event = p.innerHTML.split(';');
            if (arr_event[6].length == 16)
            {
                $("#px_code").val(arr_event[6]);
                $("#px_code").focus().select();
            }
        }

	function run_stop_file(com){
            var data1 = {act:"Listen", command:com};
            $.ajax({
             url: "client.php", 
             type: "POST",
             data: data1,
             dataType: "text",
             error: function (xhr, ajaxOptions, thrownError) {
                 alert("run_stop_file");
		alert(xhr);alert(ajaxOptions);alert(thrownError);
             }
             });
        };
	function startMon(upd_size,cnt){
            //setInterval(function() {
                 var data2 = {size: upd_size,counter: cnt};
                 $.ajax({
                  url: "client1.php", 
                  type: "POST",
                  data: data2,
                  dataType: "json",
                  success: function (data) {
                        if (data.result == 'restart') startMon(0,cnt);
                        else{
                            //$("#events2").append('<p >cnt = '+data.counter+' ev = '+data.result+'</p>');
                            var arr_events = data.result.split('^');
                            arr_events.forEach(function(item, arr_events) {
                                if (item != ''){
                                    var arr_event = item.split(';');
                                    var css_class = '';
                                    if (arr_event[1] == $("#code").val())
                                    {
                                        css_class = 'code';
                                    }
                                    if (arr_event[2] == $("#turn").val())
                                    {
                                        css_class += ' turn ';
                                    }
                                    if ((arr_event[2] == $("#unit").val() && (arr_event[1] == 'E' || arr_event[1] == 'T')) || (arr_event[1] != 'E' && (arr_event[2]>($("#unit").val()-1)*32 && arr_event[2] < $("#unit").val()*32)))
                                    {
                                        css_class += ' unit ';
                                    }
                                    var e = $("#events");
                                    //if (!stop_animation){
                                    if ($("#anistop").val()=='0'){
                                        var height = e[0].scrollHeight;
                                        e.animate({ scrollTop: height}, 10);
                                    }
                                    e.append('<p class="'+css_class+'" onclick="get_px_code(this)">'+item+'</p>');
                                    
                                    if ($("#events > p").length > 150 && $("#anistop").val()=='0')
                                    {
                                        $('#events').find('p:lt(100)').remove();
                                    }
                                }
                            });
                            $('#first_upd_time').val(data.update_size);
                            $('#counter').val(data.counter);
                            startMon(data.update_size,data.counter);
                        }
                 },
                 error:function (xhr, ajaxOptions, thrownError) {
                     alert("mon");
                    alert(xhr);alert(ajaxOptions);alert(thrownError);
                     //document.location.href = '../index.php';
                 }
              });
            //}, 300);
        };
       
        //обновление инфы о проходах на странице
        $(document).ready(function() {
            $("#events").mouseenter(function () {
                stop_animation = true;
                 $("#anistop").val('1');
            })
            $("#events").mouseleave(function () {
                stop_animation = false;
                $("#anistop").val('0');
            })
            run_stop_file("run");
            
                   
            startMon(0,0);
         });

         
        