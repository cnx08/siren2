<?php
require_once('include/_login.php');

$IDMODUL=3;

include("include/input.php");
require("include/common.php");
require("include/head.php");
echo PrintHead('Отделы','Справочник Отделов');

if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
{
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
}
require_once ('classes/ext_pages.class.php');
require_once ('classes/base/controls.class.php');
require_once ('classes/ext_deptForm.class.php');

//код представления страницы
$view   = '';
//массив для хранения ошибок
$errors = array();
// строка js скрипта, который должен выполнятся после загрузки страницы
$onLoad = '';

//создание страницы


$action = null;
$Page = new CBasePage('СКУД "Сирень" - Подразделения');
$Page->addCSSInclude('gstyles/common.css');
$Page->addCSSInclude('gstyles/menu.css');
$Page->addCSSInclude('gstyles/forms.css');
$Page->addCSSInclude('gstyles/tree.css');



$Page->addJSInclude('gscripts/jquery/lib/jquery-2.1.1.js');

$Page->addJSInclude('gscripts/jquery/plugins/jquery.simple.tree.js');
$Page->addJSInclude('gscripts/core.js');
$Page->addJSInclude('gscripts/departments.js');


$form = new CDepartmentForm('deptForm');


$Page->start();
$Page->startHead();
//выводим меню
	require_once('include/menu.php');
$Page->endHead();

$Page->startBody();

///buttons panel

$btPanel = new CButtonPanel ( 'buttonPanel' );
//new button
$newButton = new CImageLinkBt ('images/addBt16.gif','newDeptBt','добавить отдел');
$newButton->linkButton->addProperty('class','linkBt');
$newButton->linkButton->setEventListener('onclick','Departments.addDept()');
//replace button 
$replaceButton = new CImageLinkBt ('images/replace.gif','newEmplBt','перенести отдел');
$replaceButton->linkButton->addProperty('class','linkBt');
$replaceButton->linkButton->setEventListener('onclick','Departments.replace()');	
//remove button 
$removeButton = new CImageLinkBt ('images/removeBt16.gif','newEmplBt','удалить отдел');
$removeButton->linkButton->addProperty('class','linkBt');
$removeButton->linkButton->setEventListener('onclick','Departments.remove()');	

$btPanel->addButton($newButton);
$btPanel->addButton($replaceButton);
$btPanel->addButton($removeButton);

$view .= $btPanel->render();

//создаём справочник подразделений
$view .= '<table border="0" class="treeReference" cellpadding="0" cellspacing="0" >';

$view .= '<tr>';
$view .= '<th width="32%" >Отделы</th>';
$view .= '<th width="68%" >Редактирование</th>';
$view .= '</tr>';
$view .= '<tr>';
$view .= '<td valign="top">';

try
{
    $q1 = 'select value from base_const where name = \'BASE_MANTH\'';
                //echo $q1;
    $company = $dbConnection->createResultSet($q1);
    $companyname = $company->current();
    //прибиваем запрос
    $company->close();

    //строим дерево
    $view .= '<div class="treeScrollContainer">';
    $view .= '<ul id="browser" class="simpleTree">';
    $view .= '<li class="root" id="deptRoot"><span>'.$companyname[value].'</span>';
    $view .= '<ul>';

    $q = 'select * from pr_get_departments(0,'.$_SESSION['iduser'].')';

    $dataSet = $dbConnection->createResultSet($q);
    //print_r($dataSet);

    while ( $r = $dataSet->current() )
    {	
        $view .= '<li  id="'.$r['id'].'">';
        $view .=  '<span>'.$r['name'].'</span>';

        //проверяем, существует ли потомки
        $q1 = 'select * from pr_department_child_exists('.$r['id'].')';
        $chExist = $dbConnection->createResultSet($q1);
        $childExists = $chExist->current();
        //прибиваем запрос
        $chExist->close();

        if ( $childExists['bool'] == 'true')
        {
            $view  .=   '<ul class="ajax">';
            $view  .=   '<li >{url:controllers/dept.controller.php?act=getNode&idNode='.$r['id'].'}</li>';
            $view  .= 	'</ul>';
        }
        $view .= '</li>';

        $dataSet->next();
    }

    $view .= '</ul>';
    $view .= '</li>';
    $view .= '</ul>';
    $view .= '</div>';

    $dataSet->close();
}
catch ( CDBException $e)
{				
    echo $e;
    exit();
}

$view .= '</td>';
$view .= '<td width="68%" valign="top">'.$form->render().'</td>';
$view .= '</tr>';
$view .= '<tr>';
$view .= '<td id="indicator" align="center">&nbsp;</td>';
$view .= '<td id="statusBar">&nbsp;</td>';
$view .= '</tr>';
$view .= '</table>';

//вывод представления стрнаницы
echo $view;

$Page->endBody();

$Page->end();

?>