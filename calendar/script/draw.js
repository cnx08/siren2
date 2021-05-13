
// Скрипт календарика с возможностью выбора даты 
calendar = {}; 
  
// Названия месяцев 
calendar.monthName=[ 
  'Январь', 'Февраль', 'Март', 'Апрель', 
  'Май', 'Июнь', 'Июль', 'Август', 
  'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь' 
]; 
  
// Названия дней недели 
calendar.dayName=[ 
  'ПН', 'ВТ', 'СР', 'ЧТ', 'ПТ', 'СБ', 'ВС' 
]; 
  
// Выбранная дата 
calendar.selectedDate = { 
  'Day' : null, 
  'Month' : null, 
  'Year' : null 
}; 
  
// ID элемента для размещения календарика 
calendar.element_id=null; 
  
// Выбор даты 
calendar.selectDate = function(elem) {
    var id = elem.id;
    $('#'+id).attr("class") === '' ? $('#'+id).addClass('holiday'): $('#'+id).removeClass('holiday');
}; 
calendar.moverDate = function(elem) {
    var id = elem.id;
    $('#'+id).attr("bgcolor","#DCEBFF");
};  
calendar.mleaveDate = function(elem) {
    var id = elem.id;
    $('#'+id).attr("bgcolor","");
}; 
// Отрисовка календарика на выбранный месяц и год 
calendar.drawCalendar = function(month,year) { 
  var tmp=''; 
  tmp+='<table class="calendar" cellspacing="0" cellpadding="0">'; 
  
  // Месяц и навигация 
  tmp+='<tr>'; 
  tmp+='    <td colspan="7" class="navigation">'+calendar.monthName[(month-1)]+'<\/td>'; 
  tmp+='<\/tr>'; 
  
  // Шапка таблицы с днями недели 
  tmp+='<tr>'; 
  tmp+='<th>'+calendar.dayName[0]+'<\/th>'; 
  tmp+='<th>'+calendar.dayName[1]+'<\/th>'; 
  tmp+='<th>'+calendar.dayName[2]+'<\/th>'; 
  tmp+='<th>'+calendar.dayName[3]+'<\/th>'; 
  tmp+='<th>'+calendar.dayName[4]+'<\/th>'; 
  tmp+='<th class="th_holiday">'+calendar.dayName[5]+'<\/th>'; 
  tmp+='<th class="th_holiday">'+calendar.dayName[6]+'<\/th>'; 
  tmp+='<\/tr>'; 
  
  // Количество дней в месяце 
  var total_days = 32 - new Date(year, (month-1), 32).getDate(); 
  // Начальный день месяца 
  var start_day = new Date(year, (month-1), 1).getDay(); 
  if (start_day==0) { start_day=7; } 
  start_day--; 
  // Количество ячеек в таблице 
  var final_index=Math.ceil((total_days+start_day)/7)*7; 
  
  var day=1; 
  var index=0; 
  do { 
    // Начало строки таблицы 
    if (index%7==0) { 
      tmp+='<tr>'; 
    } 
    // Пустые ячейки до начала месяца или после окончания 
    if ((index<start_day) || (index>=(total_days+start_day))) { 
      tmp+='<td class="grayed">&nbsp;<\/td>'; 
    } 
    else { 
      var class_name=''; 
      if (index%7==6 || index%7==5) { 
        class_name='holiday'; 
      } 
      // Ячейка с датой 
      tmp+='<td id="'+day+'_'+month+'_'+year+'" class="'+class_name+'" '+ 
           'onclick ="calendar.selectDate(this);" onmouseover ="calendar.moverDate(this);"  onmouseleave ="calendar.mleaveDate(this);">'+day+'<\/td>'; 
      day++; 
    } 
    // Конец строки таблицы 
    if (index%7==6) { 
      tmp+='<\/tr>'; 
    } 
    index++; 
  } 
  while (index<final_index); 
  
  tmp+='<\/table>'; 
  
  // Вставить таблицу календарика на страницу 
  var el=document.getElementById(calendar.element_id); 
  if (el) { 
    el.innerHTML=tmp; 
  } 
};



$(document).ready(function() {

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
          'Year' : new Date().getFullYear() 
        }; 
        $('#year').html(calendar.selectedDate.Year);
        // Нарисовать календарик 
        calendar.drawCalendar( 
            calendar.selectedDate.Month, 
            calendar.selectedDate.Year 
        );
    });
    GetHolidays(calendar.selectedDate.Year)

}); 