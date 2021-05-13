//vaersion 1.0
var Departments = new Object();

Departments.tree = null;

Departments.idObjDept               = 'idDept';
Departments.idObjDeptParent         = 'idParent';
Departments.idObjDeptName           = 'deptName';
Departments.idObjParentDeptName     = 'parentDeptName';
Departments.idObjLesee              = 'lesee';
Departments.idObjIndicatorContainer = 'indicator';
Departments.srcIndicator            = 'images/indicator.gif';
Departments.srcErrorIcon            = 'images/error16.gif';
Departments.srcSuccessIcon          = 'images/check16.gif';
Departments.idObjStatusBar          = 'statusBar';
Departments.selectedNode = null;


Departments.initialize = function ()
{       
        $('.textField').val('');
        $('#deptName').prop( "disabled", true );
        $('#parentDeptName').prop( "disabled",true );
        $('#parentBt').hide();
        $('#clearparentBt').hide();
        
	Departments.tree = 
	$('.simpleTree').simpleTree({
		animate: false,
		animation:false,
		drag : false,
		afterClick:function(node)
		{
			Departments.selectedNode = $(node).attr('id');
                        $('#' + Departments.idObjIndicatorContainer).empty();
                        $('#' + Departments.idObjStatusBar).empty();
		},
		afterDblClick:function(node)
		{
                        $('#parentBt').hide();
                        $('#clearparentBt').hide();
                         $('#act').val("save");
                        document.getElementById('submitBt').onclick=function () {Departments.save();};
                        $('#submitBt').val("сохранить");
			//запрос на получение данных об отделе
			//1. отображаем индикатор 
			$('#' + Departments.idObjIndicatorContainer).empty();
			$('#' + Departments.idObjIndicatorContainer).append('<img src="'+Departments.srcIndicator+'">');
			$('#' + Departments.idObjStatusBar).empty();
			$('#' + Departments.idObjStatusBar).append('Пожалуйста подождите, идёт загрузка данных');
			//2. формируем запрос
			var idDept = $(node).attr('id');
			var idRequest = 'getDeptName_' + idDept;
			
			var data;
			data = 'act=getData';	
			data += '&idDept=' + idDept;
			data += '&idRequest=' + idRequest;
			//меняем состояние объекта 
			
			//3. Создаем запрос
			
			 var  req = new Core.request
                        (
                            idRequest,
                            'controllers/dept.controller.php',
                            'POST',
                            data,
                            'JSON',
                            Departments.parseDeptData,
                            Departments.requestError
                        );
			Core.addRequestObject (req);
                       
			$('#deptName').prop( "disabled", false );
			
		},
		afterMove:function(destination, source, pos){},
		afterAjax:function(response){},
		animate:true
	})[0]; //забираем ссылку на сам объект дерева а не наобъект jQuery
}
//чистим форму
Departments.clearForm = function ()
{
    //document.getElementById('deptName').value = ''; //js     
    $('#idDept').val(''); //jQuery
    $('#idParent').val('');
    $('.textField').val('');
    $('#' + Departments.idObjLesee).prop( "checked", false );
}
//заполняем форму для добавления
Departments.addDept = function ()
{
    Departments.clearForm();
     $('#deptName').prop( "disabled", false );
     
     $('#parentBt').show();
      $('#clearparentBt').show();
    $('#act').val("add");
    document.getElementById('submitBt').onclick=function () {Departments.save_add();};
    $('#submitBt').val("добавить");
    
    var idParent = Departments.selectedNode;
    Departments.selectedNode===null ?  idParent=0 : idParent = Departments.selectedNode;

    var idRequest = 'NameDept_' + idParent;
    var data ='act=getDeptName';	
        data += '&idParent=' + idParent;
        data += '&idRequest=' + idRequest;

    var  req = new Core.request
   (
       idRequest,
       'controllers/dept.controller.php',
       'POST',
       data,
       'JSON',
       Departments.resName,
       Departments.requestError
   );
   Core.addRequestObject (req);
}
//проверка и отправка данных
Departments.save_add= function ()
{
    var NewDeptName = $('#deptName').val();
    if (NewDeptName=='')
    {
        $('#deptName').attr("placeholder","Введите название отдела");
        return false;
    }
    Departments.check_for_exists('add');  
}
Departments.save = function ()
{
        Departments.check_for_exists('edit');   
}
Departments.clear_parent = function ()
{
    $('#parentDeptName').val('');
    $('#idParent').val(0);
}
Departments.choose_parent = function ()
{
    $('#idParent').val(Departments.selectedNode);
    
    var idParent = Departments.selectedNode;
     
    var idRequest = 'idParent_' + idParent;
    var data ='act=getDeptName';	
        data += '&idParent=' + idParent;
        data += '&idRequest=' + idRequest;

    var  req = new Core.request
   (
       idRequest,
       'controllers/dept.controller.php',
       'POST',
       data,
       'JSON',
       Departments.resName,
       Departments.requestError
   );
   Core.addRequestObject (req);
      
}
//заполнение формы для переноса отдела
Departments.replace = function ()
{
    Departments.clearForm();
    if (Departments.selectedNode == null) Departments.requestError('Выберите отдел')
    else{
        $('#parentBt').show();
        $('#clearparentBt').show();
        $('#act').val("replace");
        document.getElementById('submitBt').onclick=function () {Departments.save_replace();};
        $('#submitBt').val("перенести");
      
        var idDept = Departments.selectedNode
         
          Departments.selectedNode=null;
           $('#'+idDept).hide();
        var idRequest = 'idDept_' + idDept;
        var data ='act=getData';	
            data += '&idDept=' + idDept;
            data += '&idRequest=' + idRequest;

        var  req = new Core.request
       (
           idRequest,
           'controllers/dept.controller.php',
           'POST',
           data,
           'JSON',
           Departments.parseDeptData,
           Departments.requestError
       );
       Core.addRequestObject (req);

        $('#deptName').prop( "disabled", true );
        $('#parentDeptName').val('');
        $('#idParent').val(0);
    }
   
}
Departments.save_replace= function ()
{
    Departments.check_for_exists('repl');
    
}
Departments.check_for_exists= function (obj)
{
    var parentDeptName = $('#parentDeptName').val();
    var deptName       = $('#deptName').val();
    var idParent       = $('#idParent').val();
    var lesee          = $('#lesee').prop('checked')? 1 : 0;
    var idRequest      = 'id_' + 1;
    var data  = 'act=checkData';
        data += '&obj=' + obj;
        data += '&deptName=' + deptName;
        data += '&idParent=' + idParent;
        data += '&parentDeptName=' + parentDeptName;
        data += '&lesee=' + lesee;
        data += '&idRequest=' + idRequest;

    var  req = new Core.request
   (
       idRequest,
       'controllers/dept.controller.php',
       'POST',
       data,
       'JSON',
       Departments.ResultCheck,
       Departments.requestError
   );
   Core.addRequestObject (req);
    
    
}

Departments.remove = function ()
{
    if (Departments.selectedNode == null) Departments.requestError('Выберите отдел')
    else{
        var res = confirm("При удалении отдела, находящиеся в нем сотрудники будут тоже удалены. \n Для переноса сотрудников в другой отдел можете воспользоваться функционалом \'Групповых операций\'\n\n\n Удалить отдел?");
        if (res === true){
            //1. отображаем индикатор 
            $('#' + Departments.idObjIndicatorContainer).empty();
            $('#' + Departments.idObjIndicatorContainer).append('<img src="'+Departments.srcIndicator+'">');
            $('#' + Departments.idObjStatusBar).empty();
            $('#' + Departments.idObjStatusBar).append('Пожалуйста подождите, идёт выполнение команды');

            var idDept = Departments.selectedNode;
            var idRequest = 'removeDept_' + idDept;
            var data;
            data = 'act=remove';	
            data += '&idDept=' + idDept;
            data += '&idRequest=' + idRequest;
            //меняем состояние объекта 

            //3. Создаем запрос

            var  req = new Core.request
            (
                idRequest,
                'controllers/dept.controller.php',
                'POST',
                data,
                'JSON',
                Departments.commandResult,
                Departments.requestError
            );
            Core.addRequestObject (req);
        }
    }
}
//////////////////////////////////////////
Departments.commandResult = function ( result )
{
	//alert ('Command Result	' + result );
	try
	{
		var dataObj = eval( result );
				
		if ( dataObj.result )
		{
			//убираем узел
			$('#' + dataObj.idObject).remove() ;
		}
		else
		{
			Departments.displayError( dataObj.reason );
			return;
		}
		
		//убираем индикатор
		$('#' + Departments.idObjIndicatorContainer).empty();
		$('#' + Departments.idObjIndicatorContainer).append('<img src="'+Departments.srcSuccessIcon+'">');
		$('#' + Departments.idObjStatusBar).empty();
		$('#' + Departments.idObjStatusBar).append('Команда выполнена.');
	}
	catch ( e )
	{
		Departments.displayError('Парсер данных -> Ошибка: ' + e.message);
		
	}	
}

Departments.resName = function ( result )
{
    try
    {
        var dataObj = eval( result );
        $('#parentDeptName').val(dataObj.deptName);
        $('#idParent').val(dataObj.idParent);
    }
    catch ( e )
    {
        Departments.displayError('Парсер данных -> Ошибка: ' + e.message);
    }	
}

//обрабатывает данные пришедшие от сервера
Departments.parseDeptData = function (data)
{
    try
    {
        var dataObj = eval( data );
        if ( dataObj.result )
        {
            //отображаем данные
            $('#' + Departments.idObjDeptName).val(dataObj.deptName);
            $('#' + Departments.idObjParentDeptName).val(dataObj.parentDeptName);
            dataObj.lesee==1 ?  $('#lesee').prop( "checked", true ): $('#' + Departments.idObjLesee).prop( "checked", false );
            
            //id parent
            var idParent = ( dataObj.idParent == null ) ? '' : dataObj.idParent;
            $('#' + Departments.idObjDeptParent).val(idParent); 	
            //id department
            $('#' + Departments.idObjDept).val(dataObj.idDept); 
        }
        else
        {
            Departments.displayError( dataObj.reason);
            return;
        }
        //убираем индикатор
        $('#' + Departments.idObjIndicatorContainer).empty();
        $('#' + Departments.idObjIndicatorContainer).append('<img src="'+Departments.srcSuccessIcon+'">');
        $('#' + Departments.idObjStatusBar).empty();
        $('#' + Departments.idObjStatusBar).append('Данные загружены.');
    }
    catch ( e )
    {
        Departments.displayError('Парсер данных -> Ошибка: ' + e.message);

    }	
}

//обрабатывает проверку на одинаковые отделы
Departments.ResultCheck = function (data)
{
    try
    {
        var dataObj = eval( data );
        if ( dataObj.result )
        {
            //отображаем данные
            if(dataObj.check_result==1){
                document.deptForm.submit();
            }
            else{
                Departments.displayError('Отдел с таким именем уже существует на данном уровне');
                return;
            }
        }
        else
        {
            Departments.displayError( dataObj.reason);
            return;
        }
        //убираем индикатор
        $('#' + Departments.idObjIndicatorContainer).empty();
        $('#' + Departments.idObjIndicatorContainer).append('<img src="'+Departments.srcSuccessIcon+'">');
        $('#' + Departments.idObjStatusBar).empty();
        $('#' + Departments.idObjStatusBar).append('Данные загружены.');
    }
    catch ( e )
    {
        Departments.displayError('Парсер данных -> Ошибка: ' + e.message);
    }	
}

Departments.requestError = function( msg )
{
	Departments.displayError(msg);
}
Departments.displayError  = function ( msg )
{
	$('#' + Departments.idObjIndicatorContainer).empty();
	$('#' + Departments.idObjIndicatorContainer).append('<img src="'+ Departments.srcErrorIcon +'">');
	$('#' + Departments.idObjStatusBar).empty();
	$('#' + Departments.idObjStatusBar).append('<span style="color:red; font-weight:bold;">' + msg + '</span>');
}
/////////////////////////////////////
//инициализируем дерево отделов
$(document).ready 
    (   
	function()
	{
		Departments.initialize();
	}
);

	

	


