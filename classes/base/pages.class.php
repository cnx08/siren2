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
  
  public 	$onLoad;
  
  function __construct($title)
  {
  
// 	$this->addAnyTag('<!DOCTYPE HTML PUBLIC "	//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">');
	
	
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
  
	echo('<!DOCTYPE HTML PUBLIC "	//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">');
    echo '<html><head><title>'.$this->title.'</title>';
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
   echo '</head><body>';
  }
  //Завершает страницу
  public function end()
  {
  	if ( $this->onLoad != '' )
	{
		echo '<script language="javascript">';
		echo 'window.onload = function(){';
		echo $this->onLoad;
		echo '}';
		echo '</script>';
	}	
	
	
	echo '</body></html>';
  }
  
  public function addToBody($html)
  {
  	echo  $html;
  }
}





