<?php

include("include/input.php");
require_once('classes/ext_pages.class.php');

$object = ( isset( $_REQUEST['object'] ) && !empty( $_REQUEST['object']) ) ? $_REQUEST['object'] : null;
$action = ( isset( $_REQUEST['act'] )    && !empty( $_REQUEST['object']) ) ? $_REQUEST['act']	 : null;

if ( $object == 'departments' )
{

	require_once('classes/ext_deptForm.class.php');	
	
	$Page = new CEmptyPage('проводник по отделам');
	$Page->addCSSInclude('gstyles/common.css');
	$Page->addCSSInclude('gstyles/tree.css');
	$Page->addCSSInclude('gstyles/forms.css');

        $Page->addJSInclude('gscripts/jquery/lib/jquery-2.1.1.js');
        
	$Page->addJSInclude('gscripts/jquery/plugins/jquery.simple.tree.js');
	$Page->addJSInclude('gscripts/deptTree.js');
	
	$Page->start();
		
		if ( 
			!isset( $_REQUEST['elIdDept'] ) 	|| 
			 empty( $_REQUEST['elIdDept'] ) 	||  
			!isset( $_REQUEST['elDeptName'] )	|| 
			 empty( $_REQUEST['elDeptName'] ) 	
			)
		{
			echo 'Ошибка: Не удаётся инициализировать проводник,<br> так как не указаны объекты-получатели выбора';
			exit();
		}		
		
		$tree = '';
	    $form = new CBrowseDeptForm('msgForm');
		
		$form->elements['getBt']->setEventListener('onclick','DeptViewer.returnSelectTo(\''.$_REQUEST['elIdDept'].'\',\''.$_REQUEST['elDeptName'].'\')');  
		$form->elements['cancelBt']->setEventListener('onclick','DeptViewer.closeWnd()');
		$tree .= '<ul id="browser" class="simpleTree">';
		$tree .= '<li class="root" id="deptRoot"><span>Предприятие</span>';
		$tree .= '<ul>';
		
			
                        $q = 'select * from pr_get_departments(0,'.$_SESSION['iduser'].')';
                    	$res = pg_query($q);
			

			while ( $r = pg_fetch_array($res) )
			{	
				$tree .= '<li  id="'.$r['id'].'">';
				$tree .=  '<span>'.$r['name'].'</span>';
			
				//проверяем, существует ли потомки
				$q1 = 'select * from pr_department_child_exists('.$r['id'].')';
				//echo $q1;
				$chExist = pg_query($q1);
				$childExists = pg_fetch_array($chExist);
				//прибиваем запрос
			
			
				if ( $childExists['bool'] == 'true')
				{
					
					$tree  .=   '<ul class="ajax">';
					$tree  .=   '<li >{url:controllers/dept.controller.php?act=getNode&idNode='.$r['id'].'}</li>';
					$tree  .= 	'</ul>';
				}
				$tree .= '</li>';
		
				
			}
	
			$tree .= '</ul>';
			$tree .= '</li>';
			$tree .= '</ul>';
			$tree .= '</div>';
	
			
		
		$form ->Tree = $tree;
		
		echo $form->render();

		$Page->end();
}




if ( $object == 'departments_st' )
{
	require_once('classes/ext_deptForm.class.php');	
	
	$Page = new CEmptyPage('проводник по отделам');
	$Page->addCSSInclude('gstyles/common.css');
	$Page->addCSSInclude('gstyles/static_tree.css');
	$Page->addCSSInclude('gstyles/forms.css');
	
	$Page->addJSInclude('gscripts/static_tree.js');
	
	$Page->start();
		
		if ( 
			!isset( $_REQUEST['elIdDept'] ) 	|| 
			 empty( $_REQUEST['elIdDept'] ) 	||  
			!isset( $_REQUEST['elDeptName'] )	|| 
			 empty( $_REQUEST['elDeptName'] ) 	
			)
		{
			echo 'Ошибка: Не удаётся инициализировать проводник,<br> так как не указаны объекты-получатели выбора';
			exit();
		}		
		
		$tree = '';
	    $form = new CBrowseDeptForm('deptTree');
		
		$form->elements['getBt']->setEventListener('onclick','DeptTree.returnCheckedNodesTo(\''.$_REQUEST['elDeptName'].'\',\''.$_REQUEST['elIdDept'].'\')');  
		$form->elements['cancelBt']->setEventListener('onclick','self.close()');
		
				//функция для рекурсивного построения дерева отделов
				function treeRecurse( $idParent )
				{
						$res = '';
						$nodeCount = 0;
		
						$q = 'select * from pr_get_departments('.$idParent.','.$_SESSION['iduser'].')';
		
						$res .= '<ul class="Tree-Container">';
		
						$qRes = pg_query($q);
		
						$totalRows = pg_num_rows($qRes);
		
						while ($r = pg_fetch_array($qRes))
						{
							$nodeClass = 'Tree-Node';
							$nodeCount ++;
							$childrenNodes = '';
			
							//смотрим является ли узел последним
							if ( $nodeCount == $totalRows )
								$nodeClass .= ' Tree-isLast';
				
							//узнаём есть ли детишки 	
			
							$qChildren = 'select * from pr_department_child_exists('.$r['id'].')';
							$childrenRes = pg_query( $qChildren );
							$children = pg_fetch_array( $childrenRes );
			
							if ( $children['bool'] == 'true' )
							{
								$nodeClass .= ' Tree-ExpandClosed';
								pg_free_result($childrenRes);
								$childrenNodes .= '<ul class="Tree-Container">';
								$childrenNodes .= treeRecurse( $r['id'] );
								$childrenNodes .= '</ul>';
							}
							else
							{
								$nodeClass .= ' Tree-ExpandLeaf ';
							}
			
			
							$res .= '<li id="li-Item1" class="'.$nodeClass.'">';
							$res .= '<div class="Tree-Expand"></div>';
							$res .= '<input id="'.$r['id'].'" name="nodeCheck_'.$r['id'].'" type="checkbox"/>';
							$res .= '<div  class="Tree-Content" ><span id="'.$r['id'].'">'.$r['name'].'</span></div>';
							$res .= $childrenNodes;
							$res .= '</li>';
						}  
		
						$res .= '</ul>';
						return $res;
				}
	
			$tree .= '<div id="treeContainer" >';
			$tree .= '<ul id="tree" class="Tree-Container">';
			$tree .= '<li id="li-top1" class="Tree-Node Tree-IsRoot Tree-ExpandOpen Tree-IsLast">';
			$tree .= '<div class="Tree-Expand"></div>';
			//$tree .= '<input type="checkbox"/>';
			$tree .= '<div  class="Tree-Content "><span><b>Предприятие</b></span></div>'; 
			
			$tree .= treeRecurse(0);
			
		
			$tree .= '</li>';
			$tree .= '</ul>';
			$tree .= '</div>';
		
	
			
		
		$form ->Tree = $tree;
		
		echo $form->render();
		$Page->onLoad =  ' DeptTree = new Tree("tree","treeContainer",true); ';
		
		$Page->end();
	
}
//////////////////////////////////////////////////////////////////////////////////////////////

if ( $object == 'turnlist' )
{
	require_once('classes/ext_turnForm.class.php');	
	
	$Page = new CEmptyPage('проводник по точкам прохода');
	$Page->addCSSInclude('gstyles/common.css');
	$Page->addCSSInclude('gstyles/static_tree.css');
	$Page->addCSSInclude('gstyles/forms.css');
	
	$Page->addJSInclude('gscripts/static_tree.js');
	
	$Page->start();
		
		if ( 
			!isset( $_REQUEST['elIdTurn'] ) 	|| 
			 empty( $_REQUEST['elIdTurn'] ) 	||  
			!isset( $_REQUEST['elTurnName'] )	|| 
			 empty( $_REQUEST['elTurnName'] ) 	
			)
		{
			echo 'Ошибка: Не удаётся инициализировать проводник,<br> так как не указаны объекты-получатели выбора';
			exit();
		}		
		
		$tree = '';
	    $form = new CBrowseTurnForm('turnTree');
		
		$form->elements['getBt']->setEventListener('onclick','TurnTree.returnCheckedNodesTo(\''.$_REQUEST['elTurnName'].'\',\''.$_REQUEST['elIdTurn'].'\')');  
		$form->elements['cancelBt']->setEventListener('onclick','self.close()');
		
				//функция для рекурсивного построения дерева отделов
				function treeRecurse( $idParent )
				{
						$res = '';
						$nodeCount = 0;
		
						$q = 'select * from pr_get_turn_list(null,'.$_SESSION['iduser'].')';
		
						$res .= '<ul class="Tree-Container">';
		
						$qRes = pg_query( $q );
		
						$totalRows = pg_num_rows( $qRes );
		
						while ( $r = pg_fetch_array( $qRes ) )
						{
							$nodeClass = 'Tree-Node';
							$nodeCount ++;
							$childrenNodes = '';
			
							//смотрим является ли узел последним
							if ( $nodeCount == $totalRows )
								$nodeClass .= ' Tree-isLast';
				

							$nodeClass .= ' Tree-ExpandLeaf ';

							$res .= '<li id="li-Item1" class="'.$nodeClass.'">';
							$res .= '<div class="Tree-Expand"></div>';
							$res .= '<input id="'.$r['id'].'" name="nodeCheck_'.$r['id'].'" type="checkbox"/>';
							$res .= '<div  class="Tree-Content" ><span id="'.$r['id'].'">'.$r['name'].'</span></div>';
							$res .= $childrenNodes;
							$res .= '</li>';
						}  
		
						$res .= '</ul>';
						return $res;
				}
	
			$tree .= '<div id="treeContainer" >';
			$tree .= '<ul id="tree" class="Tree-Container">';
			$tree .= '<li id="li-top1" class="Tree-Node Tree-IsRoot Tree-ExpandOpen Tree-IsLast">';
			$tree .= '<div class="Tree-Expand"></div>';
			$tree .= '<div  class="Tree-Content "><span><b>Все турникеты</b></span></div>'; 
			
			$tree .= treeRecurse(0);
			
		
			$tree .= '</li>';
			$tree .= '</ul>';
			$tree .= '</div>';
		
	
			
		
		$form ->Tree = $tree;
		
		echo $form->render();
		$Page->onLoad =  ' TurnTree = new Tree("tree","treeContainer",true); ';
		
		$Page->end();
	
}

if ( $object == 'turnnumlist' )
{
    require_once('classes/ext_turnForm.class.php');	

    $Page = new CEmptyPage('проводник по точкам прохода');
    $Page->addCSSInclude('gstyles/common.css');
    $Page->addCSSInclude('gstyles/static_tree.css');
    $Page->addCSSInclude('gstyles/forms.css');

    $Page->addJSInclude('gscripts/static_tree.js');

    $Page->start();

    if ( !isset( $_REQUEST['elIdTurn'] ) || empty( $_REQUEST['elIdTurn'] ) 	||  
         !isset( $_REQUEST['elTurnName'] ) || empty( $_REQUEST['elTurnName'] ) 	
       )
    {
        echo 'Ошибка: Не удаётся инициализировать проводник,<br> так как не указаны объекты-получатели выбора';
        exit();
    }		

    $tree = '';
    $form = new CBrowseTurnForm('turnTree');

    $form->elements['getBt']->setEventListener('onclick','TurnTree.returnCheckedNodesTo(\''.$_REQUEST['elTurnName'].'\',\''.$_REQUEST['elIdTurn'].'\')');  
    $form->elements['cancelBt']->setEventListener('onclick','self.close()');

    function treeRecurse()
    {
        $res = '';
        $nodeCount = 0;
        $q = 'select * from pr_get_turn_list(null,'.$_SESSION['iduser'].')';
        $res .= '<ul class="Tree-Container">';
        $qRes = pg_query( $q );
        $totalRows = pg_num_rows( $qRes );

        while ( $r = pg_fetch_array( $qRes ) )
        {
                $nodeClass = 'Tree-Node';
                $nodeCount ++;

                //смотрим является ли узел последним
                if ( $nodeCount == $totalRows )
                        $nodeClass .= ' Tree-isLast';

                $res .= '<li id="li-Item1" class="'.$nodeClass.'">';
                $res .= '<div class="Tree-Expand"></div>';
                $res .= '<input id="'.$r['num'].'" name="nodeCheck_'.$r['num'].'" type="checkbox"/>';
                $res .= '<div  class="Tree-Content" ><span id="'.$r['num'].'">'.$r['name'].'  (#'.$r['num'].') </span></div>';
                $res .= '</li>';
        }  

        $res .= '</ul>';
        return $res;
    }

    $tree .= '<div id="treeContainer" >';
    $tree .= '<ul id="tree" class="Tree-Container">';
    $tree .= '<li id="li-top1" class="Tree-Node Tree-IsRoot Tree-ExpandOpen Tree-IsLast">';
    $tree .= '<div class="Tree-Expand"></div>';
    $tree .= '<div  class="Tree-Content "><span><b>Все турникеты</b></span></div>'; 

    $tree .= treeRecurse(0);


    $tree .= '</li>';
    $tree .= '</ul>';
    $tree .= '</div>';

    $form ->Tree = $tree;
    echo $form->render();
    $Page->onLoad =  ' TurnTree = new Tree("tree","treeContainer",true); ';
    $Page->end();
}
if ( $object == 'eventstype' )
{
    require_once('classes/ext_evtypeForm.class.php');	

    $Page = new CEmptyPage('Типы событий');
    $Page->addCSSInclude('gstyles/common.css');
    $Page->addCSSInclude('gstyles/static_tree.css');
    $Page->addCSSInclude('gstyles/forms.css');

    $Page->addJSInclude('gscripts/static_tree.js');

    $Page->start();

    if ( !isset( $_REQUEST['elIdEv'] ) || empty( $_REQUEST['elIdEv'] ) 	||  
         !isset( $_REQUEST['elEvName'] ) || empty( $_REQUEST['elEvName'] ) 	
       )
    {
        echo 'Ошибка: Не удаётся инициализировать проводник,<br> так как не указаны объекты-получатели выбора';
        exit();
    }		

    $tree = '';
    $form = new CBrowseEvForm('turnTree');

    $form->elements['getBt']->setEventListener('onclick','TurnTree.returnCheckedNodesTo(\''.$_REQUEST['elEvName'].'\',\''.$_REQUEST['elIdEv'].'\')'); 
    $form->elements['cancelBt']->setEventListener('onclick','self.close()');

    function treeRecurse()
    {
        $res = '';
        $nodeCount = 0;
        $q = 'select * from BASE_EVENTS_TYPE';
        $res .= '<ul class="Tree-Container">';
        $qRes = pg_query( $q );
        $totalRows = pg_num_rows( $qRes );

        while ( $r = pg_fetch_array( $qRes ) )
        {
                $nodeClass = 'Tree-Node';
                $nodeCount ++;

                //смотрим является ли узел последним
                if ( $nodeCount == $totalRows )
                        $nodeClass .= ' Tree-isLast';

                $res .= '<li id="li-Item1" class="'.$nodeClass.'">';
                $res .= '<div class="Tree-Expand"></div>';
                $res .= '<input id="'.$r['id'].'" name="nodeCheck_'.$r['description'].'" type="checkbox"/>';
                $res .= '<div  class="Tree-Content" ><span id="'.$r['id'].'">'.'"'.$r['code'].'" '.$r['description'].'</span></div>';
                $res .= '</li>';
        }  

        $res .= '</ul>';
        return $res;
    }

    $tree .= '<div id="treeContainer" >';
    $tree .= '<ul id="tree" class="Tree-Container">';
    $tree .= '<li id="li-top1" class="Tree-Node Tree-IsRoot Tree-ExpandOpen Tree-IsLast">';
    $tree .= '<div class="Tree-Expand"></div>';
    $tree .= '<div  class="Tree-Content "><span><b>Все типы</b></span></div>'; 

    $tree .= treeRecurse(0);


    $tree .= '</li>';
    $tree .= '</ul>';
    $tree .= '</div>';

    $form ->Tree = $tree;
    echo $form->render();
    $Page->onLoad =  ' TurnTree = new Tree("tree","treeContainer",true); ';
    $Page->end();
}
?>