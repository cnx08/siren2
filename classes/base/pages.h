<?php

abstract class CHTMLPage
{

  private   $title; //заголовок страницы
  protected $MetaTags   = array();//массив для хранения тегов <meta>
  protected $AnyTags    = array();// иассив для хранения различных тегов, данные
                                  //включаются в страницу сразу после тэгов <meta>
  protected $JSIncludes  = array();//массив для хранения ссылок на js скриптов
  protected $CSSIncludes = array();//массив для хранения ссылок на css скриптов
  protected $body_content;//строка содержащая HTML текст тела страницы
  function __construct($title)
  {
  	 $this->title = $title;
  	 //добовляем в массив $MetaTags два тега по умолчанию
  	 $this->addMetaTag('http-equiv="Content-Language" content="ru"');
  	 $this->addMetaTag('http-equiv="Content-Type" content="text/html; charset=windows-1251"');
  }
  function __destruct()
  {
  	unset($this->MetaTags);
  	unset($this->JSIncludes);
  	unset($this->CSSIncludes);
  	unset($this->AnyTags);
  }

  //добавляет тег <meta> в массив $MetaTags
  // $tagContent - строка с атрибутами тега
  public function addMetaTag($tagContent)
  {
     $this->MetaTags[] = '<meta '.$tagContent.'>';
  }
  //добавляет тег пользователя в массив $AnytaTags
  // $tagContent - <имя тега + строка с атрибутами тега + > если нужно то + </имя тега>
  public function addAnyTag($tagContent)
  {
     $this->AnyTags[] = $tagContent;
  }
  //прикрепляет к странице JS скрипт
  //$file_path - имя прикрепляемого файла
  public function addJSInclude($file_path)
  {
  	$this->JSIncludes[] = '<script type="text/javascript" src="'.$file_path.'"></script>';
  }
  //прикрепляет к странице css скрипт
  //$file_path - имя прикрепляемого файла
  public function addCSSInclude($file_path)
  {
  	$this->CSSIncludes[] = '<link rel="stylesheet" type="text/css" href="'.$file_path.'">';
  }

  //abstract function InsertBody();

  //выводит заголовок страницы
  public function start()
  {
   echo '<html><head><title>'.$this->title.'</title><head>';
   //выводим мета теги
   $size = sizeof($this->MetaTags);
   for( $i = 0; $i < $size; $i++ ) echo $this->MetaTags[$i];
   //выводим CSS инклюды
   $size = sizeof($this->CSSIncludes);
   for( $i = 0; $i < $size; $i++ ) echo $this->CSSIncludes[$i];
   //выводим JS инклюды
   $size = sizeof($this->JSIncludes);
   for( $i = 0; $i < $size; $i++ ) echo $this->JSIncludes[$i];
   //выводим остальный теги
   $size = sizeof($this->AnyTags);
   for( $i = 0; $i < $size; $i++ ) echo $this->AnyTags[$i];
   echo '</head>';
  }
  //Завершает страницу
  public function end()
  {
  	echo '</html>';
  }
  
  public function addToBody($html)
  {
  	echo  $html;
  }
}


class CEmptyPage extends CHTMLPage
{
  public function __construct($title)
  {
		parent :: __construct($title);
  }
  public function __destruct()
  {
	parent :: __destruct();
  }
}

//Класс пользовательской страницы
//Формирует каркас, состоящий из строки с логотипом, строки меню, строки действий
//клиентской области,
//панели задач и строки состояния

class CBasePage extends CHTMLPage
{
    //private $logo_panel;
   /* private $logo_html = '';
    private $menu_html = '';
    private $status_bar = '';
    private $content_html = '';*/
   // private $task_panel;

   //определяем абстрактную функцию для формирования тела страници
   function InsertBody()
   {
    $c = '<body style="margin:0px">';
    $c .= '<table border="0" cellpading="0" cellspacing="0" width="100%" height="100%" >';
    //строка для логотипа
    $c .= '<tr height="5%">';
    $c .= '<td colspan="2">лого</td>';
    $c .= '</tr>';
    //строка для меню и содержимого
    $c .= '<tr>';
    $c .= '<td width="20%">';
    $c .= '<table border="0" cellpading="0" cellspacing="0" class="menu_table">';
    $c .= '<tr >';
    $c .= '<th>Навигатор</th>';
    $c .= '';
    $c .= '</tr>';
    $c .= '<tr height="99%">';
    $c .= '<td>ele</td>';
    $c .= '</tr>';
    $c .= '</table>';
    $c .= '</td>';
    $c .= '<td>контент</td>';
    $c .= '</tr>';

    //строка для строки состояния
    $c .= '<tr height="2%">';
    $c .= '<td colspan="2">status bar</td>';
    $c .= '</tr>';
    $c .= '</body>';
    echo $c;
   }
}
