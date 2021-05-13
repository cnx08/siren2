
function ToBase()
{
    
    idishniki = new Array();
    var selectedElements = $("td.holiday");//берём все праздники
    for (var i = 0; i < selectedElements.length; i++){
        idishniki.push(selectedElements[i].id);//и в массивчик их ид
    }
    var data1 = {act:'ToBase',ids:idishniki};
    $.ajax({
     url: "./asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
    success: function (data) {
         //alert(data);
        },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error ToBase');
     }
    });
}
function GetHolidays(year)
{
    var data1 = {act:'GetHolidays',year:year};
    $.ajax({
     url: "./asinc.php", 
     type: "POST",
     data: data1,
     dataType: "text",
    success: function (data) {
         if(data!=='0'){
            $(".holiday").removeClass('holiday');
            //parse data
            var arr = data.substring(2).split(',');
            //set class holiday per data
            for (var i = 0; i < arr.length; i++) {
                var day = arr[i].split('.');
                var id = parseInt(day[0])+'_'+parseInt(day[1])+'_'+day[2];
                $('#'+id).addClass('holiday');
            }
         }
        },
    error: function (xhr, ajaxOptions, thrownError) {
        alert('error GetHolidays');
     }
    });
}
function AllowEdit()
{
    $("#mask").show();
    $("#mask").css('z-index','-1');
}
function ChangeYear(dir)
{
    var year = parseInt($("#year").text()) + dir;
    $("#year").html(year);

    var data = {"jan":"1","feb":"2","mar":"3","apr":"4","may":"5","jun":"6","jul":"7","aug":"8","sep":"9","oct":"10","nov":"11","dec":"12"};

    $.each(data,function(key, value) {        
        calendar.element_id = key; 

        var day = '';
        if(value == (parseInt(new Date().getMonth())+1))//нестрогое равенство
        {
            day = new Date().getDate();
        }
        // По умолчанию используется текущая дата 
        calendar.selectedDate={ 
          'Day' : day, 
          'Month' : value, 
          'Year' : year 
        }; 
        $('#year').html(year);
        // Нарисовать календарик 
        calendar.drawCalendar( 
            calendar.selectedDate.Month, 
            calendar.selectedDate.Year 
        );
    });
    GetHolidays(year);
}