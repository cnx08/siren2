<?php
require_once('include/hua.php');
ob_start();
/*****************ФУНКЦИИ******************************************************/
function ShowTerrList($selflag)
{   //флаг определяет:0-отображение списка для работы с ним
    // 1- для выбора элемента спмска.

 $IDMODUL=6;
 if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
 {
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
 }


  $res='';
  //javscript обработчики для действий над справочником
  $res.='<script type="text/javascript">';
  $res.='function CheckTerrForm(f)
        {
           var erflg=0;
           if(f.nameter.value=="")
           {erflg=1;alert("У территории должно быть название");f.nameter.focus();return;}
           if(CheckString(f.nameter.value)==1)
           {erflg=1;alert("Ошибка:недопустимый символ при вводе названия территории");f.nameter.focus();return;}
           if(CheckString(f.descrip.value)==1)
           {erflg=1;alert("Ошибка:недопустимый символ при вводе описания");f.descrip.focus();return;}
           if(erflg==0)
           {
             //alert("Validate");
             f.submit();
           }
        }';
  $res.='function EditTerr(f,id,name,desc)
         {
            name=name.replace("~"," ");
            desc=desc.replace("~"," ");
            f.nameter.value=name;
            f.descrip.value=desc;
           // alert(f.action);
            f.action="directories.php?action=edit&list=terr&tid="+id;
            // alert(f.action);
            ShowCloseModalWindow("addterr",0);
         }';


  $res.='</script>';
  $col1="silver";
  $col2="#f5f5dc";
  $bgcolor='';
  $flag=0;

   $head=array("Номер","Название","Описание","","");

//   $res .= ''
$res.='<br><div class="listcont" style="width:95%;height:80%;left:2%">';
 $res.='<div class="listconteiner" style="height:95%">';
   $res.='<table border="0" align="center" width="100%" cellpadding="0" cellspacing="1" class="dtab">';
   $res.='<tr class="tablehead">';

   for($i=0;$i<sizeof($head);$i++)
   {
     $res.='<td align="center">'.$head[$i].'</td>';
   }
   $res.='</tr>';
   $res.='<tr>';

   $result=pg_query('select * from BASE_W_S_TERRITORY(NULL)');
   while($r=pg_fetch_array($result))
   {
     if($flag==0){$bgcolor=$col1;$flag=1;}else{$bgcolor=$col2;$flag=0;}
     $res.='<tr bgcolor='.$bgcolor.' onmouseover=this.style.backgroundColor="#89F384" onmouseout=this.style.backgroundColor="'.$bgcolor.'">';
      $res.='<td align="center" width="5%"><p class="tabcontent">'.$r['id'].'</p></td>';
     $res.='<td align="center" width="40%"><p class="tabcontent">'.$r['name'].'</p></td>';
     $res.='<td align="center" width="40%"><p class="tabcontent">'.$r['description'].'</p></td>';
     if($selflag==1)
     {
       $res.='<td align="center" width="10%"><a href="#" class="slink">выбрать</a></td>';
       $res.='<td align="center" width="10%"></td>';
     }
     else
     {
       if($r['del']==1)
       {
       $res.='<td align="center" width="7.5%"><img src="buttons/edit.gif" onclick=\'EditTerr(document.addterr,'.$r['id'].',"'.str_replace(" ","~",$r['name']).'","'.str_replace(" ","~",$r['description']).'")\' class="icons" alt="Править"></td>';
       $res.='<td align="center" width="7.5%"><img src="buttons/remove.gif" onclick=\'document.location.href="directories.php?action=del&amp;list=terr&tid='.$r['id'].'"\' class="icons" alt="Удалить"></td>';
       }
       else
       {
        $res.='<td align="center" width="7.5%"><img src="buttons/edit.gif" onclick=\'EditTerr(document.addterr,'.$r['id'].',"'.str_replace(" ","~",$r['name']).'","'.str_replace(" ","~",$r['description']).'")\' class="icons" alt="Править"></td>';
        $res.='<td align="center" width="7.5%"><p class="text">--</p></td>';
       }
     }
     $res.='</tr>';
   }
   $res.='</table>';
   $res.='</div>';

    $res.='<div class="listhead" style="width:100%;">';
    $res.='<img style="text-align: right; vertical-align:bottom; margin:3px; cursor:pointer;" src="buttons/icons.gif" alt="создать территорию" onclick=\'ShowCloseModalWindow("addterr",0)\'>';
    $res.='</div>';
$res.='</div>';
   //Окно добавления территории
   $res.='<div id="addterr" style="display:none;position:absolute;top:150px;left:300px;z-index:2">';
   $res.='<form name="addterr" action="directories.php?action=add&amp;list=terr" method="POST">';
   $res.='<table border="0" width="300"class="dtab" cellspacing="0" cellpadding="0">';
   $res.='<tr class="tablehead">';
   $res.='<td align="center" colspan="2">Создание новой территории</td>';
   $res.='</tr>';
   $res.='<tr>';
   $res.='<td><p class="text" >Название</p></td>';
   $res.='<td><p class="text"><input name="nameter" type="text" value="" size="20" maxlength="32" class="input"></p></td>';
   $res.='</tr>';
   $res.='<tr><td colspan="2" ><p class="text" >Описание</p></td></tr>';
   $res.='<tr><td colspan="2" >
             <textarea name="descrip" rows="10" cols="40" class="input" ></textarea>
              </td>
          </tr>';

   $res.='<tr bgcolor="gray">
         <td align="left"><input type="button" name="add" onclick=\'CheckTerrForm(document.addterr)\' value="сохранить" class="sbutton"></td>
         <td align="right" ><input type="button" name="cancel" onclick=\'ShowCloseModalWindow("addterr",1)\' value="отмена" class="sbutton"></td>
         </tr>';
   $res.='</table>';
   $res.='</form>';
   $res.='</div>';
   return $res;
}

function ShowZoneList()
{
 $IDMODUL=11;
 if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
 {
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
 }
   $res='';
   $col1="silver";
   $col2="#f5f5dc";
   $bgcolor='';
   $flag=0;
   $head=array("Название","Описание","Территории","");

   //javscript обработчики для действий над справочником
  $res.='<script type="text/javascript">
    function CheckZoneForm(f)
        {
           var erflg=0;
           if(f.namezone.value=="")
           {erflg=1;alert("У  рабочей зоны должно быть название");f.nameter.focus();return;}
           if(CheckString(f.namezone.value)==1)
           {erflg=1;alert("Ошибка:недопустимый символ при вводе названия рабочей зоны");f.nameter.focus();return;}
           if(CheckString(f.descrip.value)==1)
           {erflg=1;alert("Ошибка:недопустимый символ при вводе описания");f.descrip.focus();return;}
           if(erflg==0)
           {
             f.submit();
             //ClearForm();
			 ShowCloseModalWindow("addzonediv",1)
           }
        }
   //для модального окна
   function AddTerr(f)
   {
      var h = f.ter.selectedIndex;
      var n = f.ter.options[h].value;
      if(n==0){alert("Не выбрана территория");return;}
      if(CompareString(f.selstr.value,n,",")==true)
      {alert("Выбранная территоря уже есть в списке");return;}
      f.terr_id.value=n;


      if(f.selstr.value.length==0)
         f.selstr.value+=n;
      else
         f.selstr.value+=","+n;

      var terr="   "+f.ter.options[h].text;
      var l=document.getElementById("tlist");
      var newrec=document.createElement("div");

      l.appendChild(newrec);
      newrec.className="listItem";
      newrec.id=n;
      var but=document.createElement("button");
      newrec.appendChild(but);

      var text=document.createTextNode(terr);
      newrec.appendChild(text);
      but.className="delbut";
      but.innerHTML="-";
      but.onclick=function(){
      var i=document.getElementById(newrec.id);
      l.removeChild(i);
      f.selstr.value=DelSelId(f.selstr.value,n);
              }
   }
   function ClearForm()
   {
     var d=document.getElementById("tlist");
     var arr = new Array();
     arr=document.addzone.selstr.value.split(",");
     for(var i=0;i<arr[i];i++)
     {
       var c=document.getElementById(arr[i]);
       d.removeChild(c);
     }
    document.addzone.selstr.value="";
    document.addzone.namezone.value="";
    document.addzone.descrip.value="";
    document.addzone.ter.selectedIndex=0;
    ShowCloseModalWindow("addzone",1);
   }

   function ShowDropDownList(listid,but)
   {
     //alert(listid);
     var el=document.getElementById(listid);
     var b=document.getElementById(but);
     if(el.style.display=="block")
     {
       el.style.display="none";
       b.value="+";
     }
     else
     {
       el.style.display="block";
       b.value="-";
     }
   }
   //добавляет выбранный элемент списка к спику назначения
   // dest-список назначения(id)
   // source-исходный список(id)
   //itemtext - текст элемента который нужно добавить
   // может содержать id (территории...)для добавление к бд
   // в качестве разделителя между логическими частями текста - "~"

   //selstr(id)-строка выбранных элементов(id) в списке назаначения
   //delflag-1-удалять из исходного списка элементы 0-не удалять

   function FromListInList(dest,source,itemtext,selstr,delflag)
   {
     //alert(dest);
      var d=document.getElementById(dest);
      var s=document.getElementById(source);
      var sel=document.getElementById(selstr);
      var uid=null;//id элемента для selstr
      var param=null;
      var text=null;//текст элемента
      //проверяем нужно ли разделять itemtext или он содержит только текст
      itemtext=itemtext.replace("*"," ");
      if(itemtext.indexOf("~",0)>-1)
      {
        param=new Array();//массив для хранения параметров, переданных в itemtext
        param=itemtext.split("~");
        uid=param[0];
        text="  "+param[1];
      }
      else
      {
        text=itemtext;
      }

      //проверяем есть ли такой элемент
       if(CompareString(sel.value,uid,",")==true){alert("Этот элемент уже добавлен в список");return;}

        if(sel.value.length!==0)sel.value+=","+uid;
      else sel.value+=uid;

      //  создаём дочерний элемент в списке назначения
         var newitem=document.createElement("div");
             newitem.className="listItem";
             newitem.id=d.id+"t"+uid;
      // создаём кнопку для удаления
         var newbut=document.createElement("button");
                 newbut.className="delbut";
                 newbut.innerHTML="-";
                  //обработчик для кнопки
                 newbut.onclick=function(){
                 DeleteItem(d.id,newitem.id,uid,selstr);
                 }
                 newitem.appendChild(newbut);

         var newtext=document.createTextNode(text);
         newitem.appendChild(newtext);
         d.appendChild(newitem);
   }
   function SaveChanges(f)
   {
     var itog=f.itogstr.value;
     var itogold=f.itogstrold.value;
     f.savestr.value="";
     var itog="";
     var itogold="";

     for(var i=0;i<f.elements.length;i++)
     {
        var item=f.elements[i];
        if(item.name.substr(0,1)=="z" && item.name.indexOf("selstrnew")!=-1 )
        { // alert(item.name.substr(1,item.name.length-10));
           var zonid=item.name.substr(1,item.name.length-10);
          if(item.value.length==0)
            itog+=zonid+";";
           else
           itog+=zonid+","+item.value+";";
        }
        if(item.name.substr(0,1)=="z" && item.name.indexOf("selstrold")!=-1)
        {
           var zonid=item.name.substr(1,item.name.length-10);
           if(item.value.length==0)
            itogold+=zonid+";";
           else
           itogold+=zonid+","+item.value+";";
        }
        if(item.name.indexOf("name")>-1 && item.name.indexOf("old")==-1)
        {
             var oldid=item.name+"old";
             var old=document.getElementById(oldid);
             if(old.value!=item.value)
             {
                if(f.savestr.value=="")
                   f.savestr.value+=item.id.substr(1,item.name.length-5);
                else
                  f.savestr.value+=","+item.id.substr(1,item.name.length-5);
             }
        }
        if(item.name.indexOf("descript")>-1 && item.name.indexOf("old")==-1)
        {
             var oldid=item.name+"old";
             var old=document.getElementById(oldid);
             if(old.value!=item.value)
             {
                if(f.savestr.value=="")
                   f.savestr.value+=item.id.substr(1,item.name.length-9);
                else
                  f.savestr.value+=","+item.id.substr(1,item.name.length-9);
             }
        }

     }
     //alert(itogold);

    itog=itog.substr(0,itog.length-1);
    itogold=itogold.substr(0,itogold.length-1);
    f.itogstr.value=itog;
    f.itogstrold.value=itogold;
    f.submit();
   }
   function EditZone(zid)
   {

      var id="z"+zid+"name";
      var did="z"+zid+"descript"
      var zone=document.getElementById(id);
      var desc=document.getElementById(did);

      var ce=document.getElementById("curedit");
      if(ce.value!=id && ce.value.length!=0)
      {
          var oid=ce.value;
          var old=document.getElementById(oid);
          var desid=ce.value.replace("name","descript");
          var od=document.getElementById(desid);
          old.style.border=0+"px";
          old.style.backgroundColor="silver";
          old.readOnly=1;
          od.style.border=0+"px";
          od.style.backgroundColor="silver";
          od.readOnly=1;

          ce.value=id;
      }
      else
      {
        ce.value=id;
      }
      zone.style.border="1px solid midnightblue";
      zone.style.backgroundColor="white";
      zone.readOnly=0;
      desc.style.border="1px solid midnightblue";
      desc.style.backgroundColor="white";
      desc.readOnly=0;
      zone.focus();


   }

   </script>';

   $res.='<form name="zonelist" action="directories.php?action=save&amp;list=workzone" method="POST">';
   $res.='<table border="0" align="center" width="80%" cellpadding="0" cellspacing="1" class="dtab">';

   $res.='<tr class="tablehead">';

   for($i=0;$i<sizeof($head);$i++)
   {
     $res.='<td align="center">'.$head[$i].'</td>';
   }
   $res.='</tr>';
   //$res.='<tr>';
   $result=pg_query('select * from BASE_W_S_ZONE(NULL)');
   while($r=pg_fetch_array($result))
   {
     $res.='<tr bgcolor="silver"  class="tabletr">';
     $res.='<td align="center" width="20%"><input id="z'.$r['id'].'name" name="z'.$r['id'].'name" type="text" maxvalue="32" size="32" value="'.$r['name'].'" readonly class="editinput" style="background-color:silver">
                                           <input id="z'.$r['id'].'nameold" name="z'.$r['id'].'nameold" type="hidden" maxvalue="32" size="20" value="'.$r['name'].'" readonly class="editinput" style="background-color:silver">
                                           </td>';
     $res.='<td align="center" width="20%">
            <input id="z'.$r['id'].'descript" name="z'.$r['id'].'descript" type="text" maxvalue="32" size="32" value="'.$r['description'].'" readonly class="editinput" style="background-color:silver">
            <input id="z'.$r['id'].'descriptold" name="z'.$r['id'].'descriptold" type="hidden" value="'.$r['description'].'">
            </td>';
     $res.='<td align="center" width="" valign="top">';
     $res.='<table border=0 cellpadding="0" cellspacing="0" width=100%>';

     //выводим группы принадлежащие этой зоне
     $res.='<div id="z'.$r['id'].'" style="width:100%;">';
     $result1=pg_query('select * from BASE_W_S_ZONE_TERR( '.$r['id'].')');
      $but='';
           $sstr='';//строка id-ов выбраных территорий для зоны
           while($r1=pg_fetch_array($result1))
           {
             //if($r['del']==1)
                 $but='<input type="button" class="delbut" value="-" oncLick=\'DeleteItem("z'.$r['id'].'","z'.$r['id'].'t'.$r1['id_territory'].'",'.$r1['id_territory'].',"z'.$r['id'].'selstrnew")\' />&nbsp;&nbsp;';
              // else
               //   $but='';

              $res.='<div id="z'.$r['id'].'t'.$r1['id_territory'].'" class="listItem" >'.$but.$r1['name'].'</div>';
              $sstr.=$r1['id_territory'].',';

           }
          $res.='</div>';
      $sstr=substr($sstr,0,strlen($sstr)-1);
     //if($r['del']==1)
        $res.='<tr>
               <td >
               <input id="z'.$r['id'].'selstrnew" type="hidden" value="'.$sstr.'" name="z'.$r['id'].'selstrnew">
               <input id="z'.$r['id'].'selstrold" type="hidden" value="'.$sstr.'" name="z'.$r['id'].'selstrold">
               </td>
               </tr>';

     //if($r['del']==1)
     //{
       $res.='<div class="listitem" style="color:white;border:none;background-color:#7F7F7F" >
                  <input id="addbut'.$r['id'].'" class="addbut" type="button" value="+" onClick=\'ShowDropDownList("z'.$r['id'].'addlist","addbut'.$r['id'].'")\' />
                  Добавить территорию
                  </div>';
        //список доступных территорий
       $res.='<div id="z'.$r['id'].'addlist" class="lconteiner">';
             $q='select * from base_w_s_territory_free('.$r['id'].')';
             //echo $q;
             $result2=pg_query($q);

             while($r2=pg_fetch_array($result2))
             {
               $res.='<div id="z'.$r['id'].'addlistt'.$r2['id'].'"
                      class="listItem"
                      style="width:70%;background-color:#f5f5dc;cursor:pointer;"
                      onmouseover=this.style.backgroundColor="#e8e8a8"
                      onmouseout=this.style.backgroundColor="#f5f5dc"
                      onclick=\'FromListInList("z'.$r['id'].'","z'.$r['id'].'addlist","'.$r2['id'].'~'.str_replace(" ","*",$r2['name']).'","z'.$r['id'].'selstrnew",0)\'
                      >'.$r2['name'].'</div>';
             }
       $res.'</div>';
     //}
     $res.='</table></td>';

     
       $res.='<td align="center" width="10%">
              <img src="buttons/edit.gif" onClick=\'EditZone('.$r['id'].')\' alt="править" style="cursor:pointer" />';
            if($r['del']==1)
            {
              $res.='<img src="buttons/remove.gif" onClick=\'document.location.href="directories.php?action=del&amp;list=workzone&amp;zid='.$r['id'].'"\' alt="удалить зону" style="margin-left:10px;cursor:pointer" />';
            }
             $res.=' </td>';

     $res.='</tr>';
   }
   $res.='<tr class="tablehead">';
   //эти поля нужны для определения изменений, внесённых юзверем
   $res.='<td colspan="'.sizeof($head).'" align="right">
         <input id="curedit" type="hidden" name="curedit" value="" >
         <input id="savestr" type="hidden" name="savestr" value="" >
         <input id="itogstr" type="hidden" name="itogstr" value="" >
         <input id="itogstrold" type="hidden" name="itogstrold" value="" >
         <img src="buttons/save.gif" name="save" alt="сохранить" onclick=\'SaveChanges(document.zonelist)\' style="cursor:pointer" />
         <img src="buttons/icons.gif" name="save" alt="сохранить" onclick=\'ShowCloseModalWindow("addzonediv",0)\' style="cursor:pointer" />
         </td>';
   $res.='</tr>';
  $res.='</table>';
  $res.='</form>';
  //окно для добавления
  $res.='<div id="addzonediv" style="display:none;position:absolute;top:150px;left:300px;z-index:2">';
  $res.='<form name="addzone" action="directories.php?action=add&amp;list=workzone" method="POST">';
   $res.='<table border="0" width="310"class="dtab" cellspacing="0" cellpadding="0">';
   $res.='<tr class="tablehead">';
   $res.='<td align="center" colspan="2">Создание новой рабочей зоны</td>';
   $res.='</tr>';
   $res.='<tr >';
   $res.='<td><p class="text" >Название</p></td>';
   $res.='<td><p class="text"><input name="namezone" type="text" value="" size="20" maxlength="32" class="input"></p></td>';
   $res.='</tr>';
   $res.='<tr><td colspan="2" align="center"><p class="text" >Описание</p></td></tr>';
   $res.='<tr><td colspan="2" align="center"><textarea name="descrip" rows="5" cols="35" class="input" style="width:90%">
                   </textarea>
          </tr>';
   $res.='<tr>';
   $res.='<td><p class="text" >Территории</p></td>';
   $res.='<td><select name="ter" class="select"><option value="0"></option>';
               $result=pg_query('select * from BASE_W_S_TERRITORY(NULL)');
               while($r=pg_fetch_array($result))
               {
                 $res.='<option value='.$r['id'].'>'.$r['name'].'</option>';
               }
   $res.='</select><input  type="button" value="+" onclick=\'AddTerr(document.addzone)\' class="sbutton" />
          <input type="hidden" name="terr_id" value="">';
   $res.='</tr>';
   //контейнер для списка выбраных территорий
   $res.='<tr>';
   $res.='<td colspan="2"><div id="tlist" style="width:100%;" >
           </div>';
   
   $res.='<tr bgcolor="gray">
         <td align="left"><input type="button" name="add"  value="сохранить" onclick=\'CheckZoneForm(document.addzone)\' class="sbutton" /></td>
         <td align="right" ><input type="button" name="cancel" onclick=\'ShowCloseModalWindow("addzonediv",1)\' value="отмена" class="sbutton" /></td>
         </tr>';
   $res.='<tr><td colspan="2"><input type="hidden" name="selstr"></td></tr></td>';
   $res.='</tr>';
   $res.='</table>';
   $res.='</form>';

  $res.='</div>';

  return $res;
}

function ShowDopuskList($selflag)
{
 $IDMODUL=8;
 if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
 {
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
 }
  $res='';

  $res.='<script type="text/javascript">';

  //забиваем массив с режимами
  $res.='var REGIM = new Array();';
  $q='select * from BASE_W_S_REG_NAME(NULL)';
  $result=pg_query($q);
  $i=0;
  while($r=pg_fetch_array($result))
  {
   $res.='REGIM['.$i.']="'.$r['id'].'-'.$r['name'].'";';
   $i++;
  }
  $res.='

  var CURENTEDIT=null;



  function ClearAddGroupForm(f)
  {
     f.namedop.value="";
     f.incheck.checked=0;
     f.outcheck.checked=0;
  }
  function Cancel()
  {
    ClearAddGroupForm(document.frm_adddopusk);
    ShowCloseModalWindow("adddopusk",1);
  }
  function AddDopusk(f)
  {
     if(f.namedop.value=="")
     { alert("Допуск должен иметь название");return;}
     if(CheckString(f.namedop.value)>-1)
     { alert("Недопустимый символ при вводе названия");return;}
    f.submit();
  }

  function SaveDopuskChanges(f)
  {
    var igs=document.getElementById("itgroupstr");
    igs.value="";
    f.itnamestr.value=0;
    f.itgroupstr.value="";
    f.itgroupstrold.value="";

    for(var i=0;i<f.elements.length;i++)
    {
       var item=f.elements[i];
       //имена
       if(item.name.indexOf("name")>-1 && item.name.indexOf("old")==-1 && item.name.indexOf("str")==-1)
       {
            var idold="dopnameold"+item.name.substr(7,item.name.length);
            var old=document.getElementById(idold);
            if(old.value!=item.value)
            {
               if(f.itnamestr.value==0)
                  f.itnamestr.value=item.name.substr(7,item.name.length);
                    else f.itnamestr.value+=","+item.name.substr(7,item.name.length);
            }
       }

       if(item.name.indexOf("inst")!=-1)
       {
          var status="00000000";
          var el="outst"+item.name.substr(4,item.name.length);
          var outst=document.getElementById(el);
          var el1="statusold"+item.name.substr(4,item.name.length);
          var stat=document.getElementById(el1);
          if(item.checked==1 && outst.checked==1)status="01100000";
          if(item.checked==0 && outst.checked==1)status="00100000";
          if(item.checked==1 && outst.checked==0)status="01000000";

          if(stat.value!=status)
          {
             if(f.itnamestr.value==0)
               f.itnamestr.value=item.name.substr(4,item.name.length);
             else
              f.itnamestr.value+=","+item.name.substr(4,item.name.length);
          }
       }
       if(item.name.indexOf("selstr")!=-1 && item.name.indexOf("old")==-1)
       {
         var old = document.getElementById(item.id.replace("selstr","selstrold"));
         if(item.value!=old.value)
         {
           if(f.itgroupstr.value=="")
           {
             if(item.value=="")
               f.itgroupstr.value=item.name.substr(6,item.name.length)+"-0-0";
             else
               f.itgroupstr.value=item.value;

              if(old.value=="")
               f.itgroupstrold.value=item.name.substr(6,item.name.length)+"-0-0";
             else
               f.itgroupstrold.value=old.value;

           }
           else
           {
             if(item.value=="")
              f.itgroupstr.value+=";"+item.name.substr(6,item.name.length)+"-0-0";
             else
              f.itgroupstr.value+=";"+item.value;

             if(old.value=="")
              f.itgroupstrold.value+=";"+item.name.substr(6,item.name.length)+"-0-0";
             else
              f.itgroupstrold.value+=";"+old.value;

           }
         }
       }

      }
   f.submit();
  }

  function EditDopusk(elid,id)
  {
  
	var el=document.getElementById(elid);
	
	 /*
		в любом случае снять выделение с элемента CURENTEDIT
	*/
	if((CURENTEDIT!=null))
    {
       var old=document.getElementById(CURENTEDIT);
       old.style.border=0+"px";
       old.style.backgroundColor="silver";
    }
	

	if(el.disabled)
	{
		el.style.border="1px solid midnightblue";
	    el.style.backgroundColor="white";
		el.disabled = false;
	    el.focus();
	}
	else
	{
	    el.style.border=0+"px";
        el.style.backgroundColor="silver";
		el.disabled = true;
	}	
	
     CURENTEDIT=elid;	
  }
  function RemoveTurnGroup(id_group,id_select)
  {
     var values = id_group.split("-");
     var turn_group = document.getElementById(id_group);
     var turn_list = document.getElementById("tglist"+values[0]);
     var reglist = document.getElementById("reglist" + values[0]);
     var regselect = document.getElementById("regselect-"+id_group);
     var ss = document.getElementById(id_select);
     var removeitem = id_group+"-"+regselect.options[regselect.selectedIndex].value;
     var ssarr = ss.value.split(",");
         ss.value = "";
         for(i=0;i<ssarr.length;i++)
         {
           var item = ssarr[i];
               if(item!=removeitem) ss.value+=item+",";
         }
    ss.value = ss.value.substr(0,ss.value.length-1);
    AddOptions("select"+values[0],values[1],turn_group.childNodes[1].nodeValue);
    turn_list.removeChild(turn_group);
    reglist.removeChild(regselect);

  }

  function AddTurnGroup(parent,select)
  {
    var p = document.getElementById(parent);
    var s = document.getElementById(select);
    var n = s.selectedIndex;
    var txt = s.options[n].text;
    if(n == 0) { alert("Группа не выбрана"); return; }
    var item_id = parent.substr(6,parent.length) + "-" +s.options[n].value;
    var regsel_id = "regselect-" + parent.substr(6,parent.length) + "-" + s.options[n].value;
    var reggroup_id = "reglist" + parent.substr(6,parent.length);

    var newitem = document.createElement("div");
        newitem.id = item_id;
        newitem.className = "listitem";
        newitem.style.width = 100 + "%";
    p.appendChild(newitem);

    RemoveOptions(select,n);
    var button = CreateButton("but","-","delbut",newitem.id);
        button.style.marginRight = 2+"px";
        button.onclick = function()
        {
          RemoveTurnGroup(newitem.id,"selstr"+parent.substr(6,parent.length));
        }

    var text = document.createTextNode(txt);
        newitem.appendChild(text);

    var newselect = CreateSelect(regsel_id,"","",reggroup_id);
        newselect.style.width = 100 + "%";
        newselect.style.marginTop = 1 +"px";
        newselect.onchange = function()
        {
          RegimChange(newselect,"selstr"+parent.substr(6,parent.length));
        }

    for(var i=0;i<REGIM.length;i++)
    {
        var item = REGIM[i];
        var itemval = item.split("-");
        AddOptions(regsel_id,itemval[0],itemval[1]);

    }
    var g = newselect.selectedIndex;
    var ss_id = "selstr" + parent.substr(6,parent.length);
    var ss = document.getElementById(ss_id);
    if( ss.value == "")
       ss.value = newitem.id + "-" + newselect.value;
    else
      ss.value += ","+ newitem.id+ "-" + newselect.value;


  }
  function RegimChange(obj,selstr)
  {
    // alert(obj.id,selstr);
     var ss = document.getElementById(selstr);
     var nreg = obj.options[obj.selectedIndex].value;
     var tg = obj.id.substr(10,obj.id.length);
     var ssarr = ss.value.split(",");
         ss.value="";
         for(var i=0;i<ssarr.length;i++)
         {   var item = ssarr[i];
             if(item.indexOf(tg+"-",0)!=-1)
              ss.value+=tg+"-"+nreg+",";
             else
             ss.value+=item+",";
         }
   ss.value = ss.value.substr(0,ss.value.length-1);
  }
  </script>';

$res.='<form name="dopusklist" action="directories.php?action=save&amp;list=dopusk" method="POST">';
$res.='<div class="listcont" style="width:95%;height:85%;position:absolute;left:20px;border:0px;">';
  $res.='<div class="listcont" style="width:100%;height:95%;overflow:auto;position:relative;border:0px;">';
  $res.='<table style="border:0; width:100%;" cellpadding=2 cellspacing=1>';
  $res.='<tr class="tablehead">';
      $res.='<td align="center" width="15%">Название</td>';
      $res.='<td align="center" width="15%">Контроль двойных засечек </td>';
      $res.='<td align="center" width="25%">Группа турникетов</td>';
      $res.='<td align="center" width="25%">Режим доступа</td>';
      $res.='<td align="center" width="5%"></td>';
      $res.='<td align="center" width="5%"></td>';
  $res.='</tr>';

  $q='select * from BASE_W_S_DOPUSK(NULL)';
  $result=pg_query($q);
  $chk='';
  while($r=pg_fetch_array($result))
  {
     $tgselstr='';
     $res.='<tr bgcolor="silver">';
	 //textarea вместо input
        $res.='<td class="tabcontent" align="center" width="15%">
               <textarea id="dopname'.$r['id'].'"  type="text" name="dopname'.$r['id'].'" value="'.$r['name'].'" disabled class="editinput" style="background-color:silver">'.$r['name'].'</textarea>
               <input id="dopnameold'.$r['id'].'" type="hidden" name="dopnameold'.$r['id'].'" value="'.$r['name'].'">
               </td>';
         $res.='<td class="tabcontent" align="center" width="20%">';

             if(substr($r['status'],1,1)=="1")$chk='checked';else $chk='';
             $res.='<input id="inst'.$r['id'].'" name="inst'.$r['id'].'" type="checkbox" '.$chk.'>вход&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
             if(substr($r['status'],2,1)=="1")$chk='checked';else $chk='';
             $res.='<input id="outst'.$r['id'].'" name="outst'.$r['id'].'" type="checkbox" '.$chk.'>выход';
             $res.='<br><input id="statusold'.$r['id'].'" type="hidden" name="statusold'.$r['id'].'" size="10" value='.$r['status'].'>';
         $res.='</td>';


         //ГРУППЫ ТУРНИКЕТОВ
         $REGIM = ''; //переменная для формирования списка режимов
         $turnselstr = '';
         $k = 0;
         $res.='<td valign="top" width="30%">';
         $res.='<div id="tglist'.$r['id'].'" style="width:100%">';
                $group_query = 'select * from BASE_W_S_DOPUSK_TURN('.$r['id'].')';
                $group_result = pg_query($group_query);

                //РЕЖИМы для групп турникетов
				// см. коментарий ниже
                $REGIM .= '<div id="reglist'.$r['id'].'" style="width:100%">';

				
                while($g = pg_fetch_array($group_result))
                {
                  $res.='<div id="'.$r['id'].'-'.$g['turn_id'].'" class="listitem" style="width:100%;">';
                  $res.='<input type="button" class="delbut" value="-" style="margin-right:2px;" onclick = RemoveTurnGroup("'.$r['id'].'-'.$g['turn_id'].'","selstr'.$r['id'].'")>';
                  $res.=$g['turn_name'];
                  $res.='</div>';

                  $turnselstr .= $r['id'].'-'.$g['turn_id'].'-'.$g['reg_id'].',';
                  //получаем режимы для турникетов
                  $REGIM .= '<select id="regselect-'.$r['id'].'-'.$g['turn_id'].'" name="regselect-'.$r['id'].'-'.$g['turn_id'].'" style="width:99%;margin:1px;" onChange=RegimChange(this,"selstr'.$r['id'].'")>';
                  $sel = '';
                  $reg_query ='select * from BASE_W_S_REG_NAME(NULL)';
                  $reg_result=pg_query($reg_query);
                  while($reg=pg_fetch_array($reg_result))
                  {
                      if($g['reg_id']==$reg['id']){$sel='selected';}else {$sel='';}

                        $REGIM.='<option value="'.$reg['id'].'" '.$sel.'>'.$reg['name'].'</option>';
                  }
                  $REGIM .= '</select>';
                  $k++;

                }
							
				
                $REGIM .= '</div>';
                if($k == 0)
                {
                    $REGIM = '<div id="reglist'.$r['id'].'" style="width:100%"></div>';

                }
         $res .= '</div>';

         $res.='<select id="select'.$r['id'].'" style="width:90%;margin:1px;" ><option value="0">Не назначена</option>';
               $q4='select * from BASE_W_S_DOPUSK_TURN_FREE('.$r['id'].')';
               $result4=pg_query($q4);
               while($r4=pg_fetch_array($result4))
               {
                 $res.='<option value="'.$r4['id'].'">'.$r4['name'].'</option>';
               }

        $res.='</select>';
        $res.='<input type="button" value="+" class="addbut" style="margin-bottom:4px;" onClick = \'AddTurnGroup("tglist'.$r['id'].'","select'.$r['id'].'")\' />';
        $turnselstr=substr($turnselstr,0,strlen($turnselstr)-1);
        $res.='<input id="selstr'.$r['id'].'" name="selstr'.$r['id'].'" type="hidden" value="'.$turnselstr.'">';
        $res.='<input id="selstrold'.$r['id'].'" name="selstrold'.$r['id'].'" type="hidden" value="'.$turnselstr.'">';
        $res.='</td>';

         //Режимы доступа
         $res.='<td valign="top" width="20%">';
           $res .= $REGIM;
         $res.='</td>';

         $res.='<td class="tabcontent" align="center"><a href="#" class="slink" onclick=\'EditDopusk("dopname'.$r['id'].'",'.$r['id'].')\'><img src="buttons/edit.gif" class="icons" /></a></td>';
         $res.='<td class="tabcontent" align="center" width="10%"><a href="directories.php?action=del&amp;list=dopusk&amp;did='.$r['id'].'" ><img src="buttons/remove.gif" class="icons" /></a></td>';

     $res.='</tr>';
  }

   $res.='</table>';
  $res.='</div>';

  $res.=    '<div class="listhead" style="margin-left:5px;width:100%">';
  $res.=    '<img style="text-align: right; vertical-align:bottom; margin:3px;cursor:pointer;" src="buttons/icons.gif"  alt="создать допуск" onclick=\'ShowCloseModalWindow("adddopusk",0)\' />';
  $res.=    '<img style="text-align: right; vertical-align:bottom; margin:3px;cursor:pointer;" src="buttons/save.gif"  alt="сохранить изменения" onclick=\'SaveDopuskChanges(document.dopusklist)\' />';
  $res.=    '<input id="itgroupstr" type="hidden" name="itgroupstr" value="" size="30">';
  $res.=    '<input id="itgroupstrold" type="hidden" name="itgroupstrold" value="" size="30">';
  $res.=    '<input id="itnamestr" type="hidden" name="itnamestr" value="">';



  $res.=    '</div>';

  $res.='</div>';//главная дивка

$res.='</form>';
//$res.='<div class="listhead" ></div>';
  //окошко добавления
  $res.='<div id="adddopusk" style="display:none;position:absolute;top:150px;left:50px;z-index:2">';
      $res.='<form name="frm_adddopusk" action="directories.php?action=add&amp;list=dopusk" method="POST">';
          $res.='<table border="0" width=370 cellpadding="1" cellspacing="0" class="dtab">';
          $res.='<tr class="tablehead">';
               $res.='<td align="center" colspan="2">Создание нового допуска</td>';
          $res.='</tr>';
          $res.='<tr>';
              $res.='<td><p class="text">Название</p></td>';
              $res.='<td><input type="text" name="namedop" value="" class="input" size="25"></td>';
          $res.='</tr>';
          $res.='<tr>';
              $res.='<td><p class="text">Контроль двойных засечек</p></td>';
              $res.='<td><span class="text">вход</span><input type="checkbox" name="incheck">
                         <span class="text">выход</span><input type="checkbox" name="outcheck">
                     </td>';
          $res.='</tr>';

          $res.='<tr class="tablehead">';
                  $res.='<td align="left"><input type="button" class="sbutton" value="cохранить" onclick=\'AddDopusk(document.frm_adddopusk)\' /></td>';
                  $res.='<td align="right"><input type="button" class="sbutton" value="отмена" onclick=\'Cancel()\' /></td>';
          $res.='</tr>';
          $res.='</table>';
      $res.='</form>';
  $res.='</div>';
  return $res;
}
function ShowTurnList($flag)
{
 $IDMODUL=5;
 if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
 {
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
 }

  $res='';
    $res.='<script type="text/javascript" src="../gscripts/jquery/lib/jquery-2.1.1.js"></script>';
  $res.='<script type="text/javascript">

  var EDITITEM=null;

  function SelectGroupFlag(f0,idstr,flagname)
  {
  
         var f=document.getElementById(f0);	
		 
          var id=document.getElementById(idstr);
          var flag=document.getElementById(flagname);
          if(flag.checked==1)
          {
            for(var i=0;i<f.elements.length;i++){
                var item=f.elements[i];
                if(item.name.indexOf("check",0)>-1)
                  {
                    if(item.name!=flag.name)
                       item.checked=false;
                  }
              id.value="list-"+flag.name.substr(5,flag.name.length);
            }
          }
          else {id.value="";}
   }
   function SetTurnInGroup(turn_id)
   {

      var group=document.getElementById("checkedflag");
      if(!group){alert("Fatal error: Can\'t find destination list");return;}

      var source=document.getElementById(turn_id);
      if(group.value.length==0)
         {alert("Не выбрана группа турникетов для добавления");return;}
      var gid=group.value;
      var grouplist=document.getElementById(gid);
      var selstrid="selstr"+gid.substr(5,gid.length);

      var item=document.createElement("div");
          item.className="listitem";
          item.name="item";
          item.id=gid+"-"+turn_id.substr(5,gid.length);

      var but=document.createElement("button");
          but.className="delbut";but.innerHTML ="-";
          but.onclick=function(){
          RemoveItemFromList(gid,item.id,selstrid,1,"stack_turn","stackselstr");
          }
          item.appendChild(but);
      var text=source.childNodes[3].nodeValue;
      var t=document.createTextNode(text);
          item.appendChild(t);
      grouplist.insertBefore(item,grouplist.childNodes[0]);

      var ss=document.getElementById(selstrid);

      if(ss.value.length!=0)
             ss.value+=","+turn_id.substr(5,gid.length);
          else
             ss.value=turn_id.substr(5,gid.length);
        RemoveItemFromList("stack_turn",turn_id,"stackselstr",0);

      var sss=document.getElementById("stackselstr");
      sss.value=DelSelId(sss.value,turn_id.substr(5,gid.length));

   }


   function RemoveItemFromList(owner,child,selstr,flag,destlist,destselstr)
   {
        var ow=document.getElementById(owner);
        var ch=document.getElementById(child);
        if(!ch){alert("Fatal error: Can\'t find removing object");return;}
        var ss=document.getElementById(selstr);
        var name=child.split("-");
        id=name[2];
        ss.value=DelSelId(ss.value,id);
        if(flag==1 && destlist!=null && destselstr!=null)
        {
            var text=ch.childNodes[1].nodeValue;
            var d=document.getElementById(destlist);
            var ss=document.getElementById(destselstr);
            var item=document.createElement("div");
                item.id="stack"+id;
                item.className="listitem";
                item.name="item";
                item.style.cursor="pointer";

            var im=document.createElement("img");
                im.src="buttons/left.gif";
                            im.style.margin = 1+"px";
                im.onclick=function(){
                 SetTurnInGroup(item.id);
                }
                    item.appendChild(im);

                        im=document.createElement("img");
                im.src="buttons/edit.gif";
                            im.style.margin = 1+"px";
                            im.height = 15;
                            im.name = "editbut";
                    im.onclick=function(){
                    DefinedAction(this,item.id.substr(5,item.id.length));
                   }
                    item.appendChild(im);

                            im=document.createElement("img");
                im.src="buttons/remove.gif";
                            im.style.margin = 1+"px";
                            im.height = 15;
                    im.onclick=function(){
                    RemoveTurn(item.id.substr(5,item.id.length));
                   }
                    item.appendChild(im);

                    var t=document.createTextNode(text);
                item.appendChild(t);
            d.insertBefore(item,d.childNodes[0]);
            if(ss.value.length!=0)
               ss.value+=","+id;
            else
               ss.value=id;
        }
        //alert(ch.id);
        ow.removeChild(ch);
        //alert("end");
   }
   function ClearAddForm(f,list)
   {
      //alert(f.name);alert(list);
      var l=document.getElementById(list);
      var selstr=document.getElementById("selstr0");
      var ss=selstr.value.split(",");
      if(ss.length!=1)
      {
         for(var i=0;i<=ss.length;i++)
         {
             var id=list+"-"+ss[i];
             var item=document.getElementById(id);
            if(item)
              RemoveItemFromList("list-0",id,"selstr0",1,"stack_turn","stackselstr");

         }
      }
     f.namegroup.value="";
     f.descrip.value="";
     f.check0.checked=0;
     ShowCloseModalWindow("addturngroupdiv",1)
   }
   function CheckAddForm(f)
   {
     var erflg=0;
           if(f.namegroup.value=="")
           {erflg=1;alert("У  группы должно быть название");f.namegroup.focus();return;}
           if(CheckString(f.namegroup.value)==1)
           {erflg=1;alert("Ошибка:недопустимый символ при вводе названия группы");f.namegroup.focus();return;}
           if(CheckString(f.descrip.value)==1)
           {erflg=1;alert("Ошибка:недопустимый символ при вводе описания");f.descrip.focus();return;}
           if(erflg==0)
           {
             f.submit();
             ClearAddForm(f,"list-0");
           }
   }
   function EditGroup(id)
   {
      var n="groupname"+id;
      var d="descript"+id;
      var name=document.getElementById(n);
      var desc=document.getElementById(d);
      if(EDITITEM==null)
      {
         EDITITEM=id;
         name.readOnly=0;name.focus();name.style.backgroundColor="white";
         name.style.border="1px solid midnightblue";
         desc.readOnly=0;desc.style.backgroundColor="white";
         desc.style.border="1px solid midnightblue";

      }
      else
      {
         var on="groupname"+EDITITEM;
         var od="descript"+EDITITEM;
         var oname=document.getElementById(on);
         var odesc=document.getElementById(od);
         oname.readOnly=1;oname.style.backgroundColor="silver";oname.style.border="0px";
         odesc.readOnly=1;odesc.style.backgroundColor="silver";odesc.style.border="0px";
         EDITITEM=id;
         name.readOnly=0;name.focus();name.style.backgroundColor="white";
         name.style.border="1px solid midnightblue";
         desc.readOnly=0;desc.style.backgroundColor="white";
         desc.style.border="1px solid midnightblue";
      }

   }
   function SaveGroupChanges()
   {
   
	var f=document.getElementById("turngrouplist");
     f.editingstr.value="";
     f.itogselstr.value="";
     f.itogselstrold.value="";

     for(var i=0;i<f.elements.length;i++)
     {
        var item=f.elements[i];
        if(item.name.indexOf("groupname",0)>-1 && item.name.indexOf("old",0)==-1)
        {
           if(item.value=="")
           {alert("У  группы должно быть название");f.elements[i].focus();return;}
           if(CheckString(item.value)==1)
           {alert("Ошибка:недопустимый символ при вводе названия группы");f.elements[i].focus();return;}

            var on=item.name+"old";
            var el=document.getElementById(on);
            if(item.value!=el.value)
            {
                var id=item.id.substr(9,item.id.length);
                  if(f.editingstr.value=="")
                      f.editingstr.value+=id;
                   else
                       f.editingstr.value+=","+id;
            }
        }
        if(item.name.indexOf("descript",0)>-1 && item.name.indexOf("old",0)==-1)
        {
           if(CheckString(item.value)==1)
           {alert("Ошибка:недопустимый символ при вводе описания");f.elements[i].focus();return;}

            var on=item.name+"old";
            var el=document.getElementById(on);
            if(item.value!=el.value)
            {
                var id=item.id.substr(8,item.id.length);
                  if(f.editingstr.value=="")
                      f.editingstr.value+=id;
                   else
                       f.editingstr.value+=","+id;
            }
        }
       if(item.name.indexOf("selstr")>-1 && item.name.indexOf("old")==-1 && item.name.indexOf("itog")==-1)
       {
         var id=item.id.substr(6,item.id.length);
         var on=item.name+"old";
         var oel=document.getElementById(on)
         if(item.value!=oel.value)
         {
            var newstr=item.value;
            var oldstr=oel.value;
               if(f.itogselstr.value=="")
               {
                   if(newstr=="")
                      f.itogselstr.value+=id;
                    else
                      f.itogselstr.value+=id+","+newstr;
               }
               else
               {
                  if(newstr=="")
                     f.itogselstr.value+=";"+id;
                  else
                    f.itogselstr.value+=";"+id+","+newstr;
               }

               if(f.itogselstrold.value=="")
               {
                   if(oldstr=="")
                      f.itogselstrold.value+=id;
                    else
                      f.itogselstrold.value+=id+","+oldstr;
               }
               else
               {
                  if(oldstr=="")
                     f.itogselstrold.value+=";"+id;
                  else
                    f.itogselstrold.value+=";"+id+","+oldstr;
               }
         }
       }
     }
    if(f.editingstr.value=="" && f.itogselstr.value=="" && f.itogselstrold.value=="")
       alert("Изменений не было.Сохранять нечего");
    else
     f.submit();

   }
   function ClearTurnForm()
   {
      ShowCloseModalWindow("addturn",0);
      var f = document.getElementById("addturnfrm");
      f.tnum.value = "";f.nameturn.value = "";f.turndesc.value="";f.turngroup.selectedIndex=0;
      f.flag_block.checked=0;f.flag_terr_in.checked=0;f.flag_terr_out.checked=0;
      f.in_terr.selectedIndex=0;f.out_terr.selectedIndex=0;
   }
   function calc_turn()
   {
        $("#t_info").hide();
        $("#unit_num").show();
        $("#contr_num").show();
        $("#unit_num,#contr_num").on("input",function(){
            var contr_num = Number($("#contr_num").val());
            var unit_num = Number($("#unit_num").val());
        if (unit_num.length != 0 && unit_num>0 && contr_num.length != 0 && contr_num>0){
                $("#tnum").val( (unit_num-1)*32+contr_num);
        }
        });
   }
   </script>';
   
   
   
   
$res .='<div id="div_turngrouplist" style="height:85%; width:65%; position: absolute; top: 47px;">';

  //главная дивка 
 $res.= '<div class="listhead" style="top:0; width:100%; height:5%;"> Список групп турникетов </div>'; 
  $res.= '<form id="turngrouplist" style="width: 100%; position: relative; height: 100%;" action="directories.php?action=save&amp;list=turngroup" method="POST" >';
   $res.= '<div class="listcont" style="position:relative;top:0px;left:0;width:100%;height:95%;">';

  $res.=    '<div class="listcont" style="border:0px;position:relative;width:99%;height:90%;margin:1px;overflow:auto" >';
  $res.=    '<table border=0 cellpadding="1" cellspacing="1" width=100%>';
             $q='select * from BASE_W_S_TURN_GROUP(NULL)';
             //echo $q;
             $result=pg_query($q);
             while($r=pg_fetch_array($result))
             {

               $res.='<tr bgcolor="silver">';
               $res.='<td valign="top" width="10%">
                      <input id="groupname'.$r['id'].'" type="text" name="groupname'.$r['id'].'" maxlength="64" size="30" value="'.$r['name'].'" class="editinput" style="background-color:silver" readonly>
                      <br><input id="groupname'.$r['id'].'old" type="hidden" name="groupname'.$r['id'].'old" maxlength="64" size="30" value="'.$r['name'].'" class="editinput" >
                      </td>';
               $res.='<td valign="top" width="10%">
                      <textarea id="descript'.$r['id'].'" name="descript'.$r['id'].'" rows=3 cols=15 class="editinput" style="background-color:silver;" readonly>'.$r['description'].'</textarea>
                      <input id="descript'.$r['id'].'old" type="hidden" name="descript'.$r['id'].'old"  size="30" value="'.$r['description'].'">
                      </td>';
               $res.='<td valign="top"  width="60%">';
                      $res.='<div id="list-'.$r['id'].'" style="width:100%;">';

                            $selstr=null;
                            $q1='select * from BASE_W_S_TURN_IN_GROUP('.$r['id'].')';
                            $result1=pg_query($q1);
                            while($r1=pg_fetch_array($result1))
                            {
                               $res.='<div id="list-'.$r['id'].'-'.$r1['id'].'" class="listitem">';
                               $res.='<input  type="button" value="-" class="delbut" onclick=\'RemoveItemFromList("list-'.$r['id'].'","list-'.$r['id'].'-'.$r1['id'].'","selstr'.$r['id'].'",1,"stack_turn","stackselstr")\' />&nbsp;';
 
                                   $unit = ceil($r1['num']/32); 
                                   $contr = $r1['num']-(($unit-1)*32);

                               $res.=$r1['name'].'(#'.$r1['num'].', прошивка '.$contr.', юнит '.$unit.')';
                               $res.='</div>';
                               $selstr.=$r1['id'].',';
                            }

                      $res.='<div class="listItem"><input id="check'.$r['id'].'" name="check'.$r['id'].'" type="checkbox" onclick=\'SelectGroupFlag("turngrouplist","checkedflag","check'.$r['id'].'","turnselstr")\' />&nbsp;добавить из спиcка</div>';
                      $res.='</div>';
                      $selstr=substr($selstr,0,strlen($selstr)-1);
                      $res.='<input type="hidden" id="selstr'.$r['id'].'" name="selstr'.$r['id'].'" size="5" value="'.$selstr.'">';
                      $res.='<input type="hidden" id="selstr'.$r['id'].'old" name="selstr'.$r['id'].'old" size="5" value="'.$selstr.'">';

               $res.='</td >';
               $res.='<td align = "center" style="padding:4px;"  width="4%">';
               $res.='<img src="buttons/edit.gif"  onclick=\'EditGroup('.$r['id'].')\' style="cursor:pointer;" alt="редактировать" />
                      <img src="buttons/remove.gif"   onclick = \'document.location.href="directories.php?action=del&amp;list=turngroup&amp;gid='.$r['id'].'"\' style="cursor:pointer;" alt="удалить" />';
               $res.='</td>';

               $res.='</tr>';
             }
  $res.=    '</table>';
  $res.=    '</div>';		// class="listcont" перед TABLE
  

  $res .= '<div class="listhead" style="position: absolute; bottom: 0; width: 100%; height: 25px; text-align:right;">';
	  $res.=    '<img style="text-align: right; vertical-align:top; margin:3px;cursor:pointer;" src="buttons/icons.gif"  alt="создать группу турникетов" onclick=\'ShowCloseModalWindow("addturngroupdiv",0)\' />';
	  $res.=    '<img style="text-align: right; vertical-align:top; margin:3px;cursor:pointer;" src="buttons/save.gif" alt="сохранить изменения" onclick=\'SaveGroupChanges()\' />';
	  $res.=    '<input id="checkedflag" type="hidden" name="checkedflag" >';
	  $res.=    '<input id="editingstr" type="hidden" name="editingstr" value="">';
	  $res.=    '<input id="itogselstr" type="hidden" name="itogselstr" value="">';
	  $res.=    '<input id="itogselstrold" type="hidden" name="itogselstrold" value="">';
  $res.=     '</div>';
  
  
  $res.='</div>';
  $res.='</form>';
  
$res .= '</div>';  
  
  
  //Окно добавления новой группы
   $res.='<div id="addturngroupdiv" style="display:none;position:fixed;top:100px;left:100px;z-index:2000; background-color:gray;">';

    $res .= '<div id="data_addturngroupdiv" style="position:relative; padding:2px;">';
   
   $res.='<form name="addturngroup" action="directories.php?action=add&amp;list=turngroup" method="POST">';
   $res.='<table border="0" width="310"class="dtab" cellspacing="0" cellpadding="0">';
   $res.='<tr class="tablehead">';
   $res.='<td align="center" colspan="2">Создание группы турникетов</td>';
   $res.='</tr>';
   $res.='<tr >';
   $res.='<td><p class="text" >Название</p></td>';
   $res.='<td><p class="text"><input name="namegroup" type="text" value="" size="20" maxlength="32" class="input"></p></td>';
   $res.='</tr>';
   $res.='<tr><td colspan="2" align="center"><p class="text" >Описание</p></td></tr>';
   $res.='<tr><td colspan="2" align="center"><textarea name="descrip" rows="5" cols="35" class="input">&nbsp;</textarea>
          </tr>';
   $res.='<tr><td colspan="2">';
   $res.='<div id="list-0" style="width:100%;">';
          $res.=  '<div id="conteiner" class="listItem" >
                   <input id="check0" name="check0" type="checkbox" onclick=\'SelectGroupFlag("turngrouplist","checkedflag","check0","turnselstr")\' />&nbsp;добавить из спиcка';
          $res.= '</div>';
   $res.='</div>';
   $res.='<input id="selstr0" type="hidden" name="selstr0">';
   $res.='</td></tr>';
   $res.='<tr class="tablehead">
          <td colspan="1" align="left"><input type="button" class="sbutton" value="сохранить" onclick=\'CheckAddForm(document.addturngroup)\' /></td>
          <td align="right"><input type="button" class="sbutton" value="отмена" onclick=\'ClearAddForm(document.addturngroup,"list-0")\' /></td>
          </tr>';
   $res.='</table>';
   $res.='</form>';
   
    $res .= '</div>';
   $res.='</div>';
  //стек турникетов
  $res.='<div class="listcont" style="top: 47px;left:66%;width:33%;height:85%; width:33%;">';
  $res.=    '<div class="listhead" >Турникеты</div>';
  $res.=    '<div id="stack_turn" class="listcont" style="border:0px;position:relative;width:100%;height:90%;margin:1px;overflow:auto" >';

             $turn_id=null;
             $q='select * from BASE_W_S_TURN_FREE()';
             $result=pg_query($q); //echo '<br>'.$q;
             while($r=pg_fetch_array($result))
             {
                $res.='<div id="stack'.$r['id'].'" class="listitem" style="margin:1px;cursor:pointer">';
                $res.='<img src="buttons/left.gif" onclick=\'SetTurnInGroup("stack'.$r['id'].'")\' alt="добавить в группу турникетов" style="margin:1px;" />';
                $res.='<img src="buttons/edit.gif"  name="editbut" height="15" alt="редактировать" style="margin:1px;"  onclick=\'DefinedAction(this,'.$r['id'].')\' />';
                              $res.='<img src="buttons/remove.gif" onclick=\'RemoveTurn('.$r['id'].')\'  height="15" alt="удалить" style="margin:1px;" />';
                                $res.=$r['name'].'(#'.$r['num'].')';
                                $res.='</div>';
                $turn_id.=$r['id'].',';
             }
            $turn_id=substr($turn_id,0,strlen($turn_id)-1);
  $res.=    '<div>&nbsp;</div>';
  $res.=    '</div>';
  $res.=    '<div class="listhead" style="position: absolute; bottom: 0pt; height: 25px; text-align: center;">';
  $res.=    '<input id="stackselstr" type="hidden" size=15 value="'.$turn_id.'">';
  $res.=    '<img style="text-align: right; vertical-align:bottom; margin:3px;cursor:pointer;" name="addbut"  src="buttons/icons.gif"  alt="создать турникет" onclick=\'DefinedAction(this,0)\' />';

  $res.=    '</div>';
  $res.='</div>';
  // окошко добавления турникета
  //тень
  $res.='<div id="addturnshadow" style="display:none;position:absolute;top:105px;left:655px;width:310px;height:78px;
                                     Z-INDEX:48;display:none;" class="shadowwindow">5&nbsp;</div>';
  $res.='<div id="addturn"  style="display:none; position:absolute; top:50px; left:65px;z-index:50; border: 1px solid gray;">';
  $res.='<form id="addturnfrm" name="addturnfrm" action="" method="POST">';
  $res.='<input type="hidden" value="" name="tid">';
  $res.='<table border="0" width="550"class="dtabturn" cellspacing="0" cellpadding="0">';
  $res.='<tr class="tablehead">';
  $res.='<td align="left" >Турникет</td>
         <td align="right"><img src="buttons/crossline.gif" style="text-align: right;" class="icons" onclick=\'ShowCloseModalWindow("addturn",1)\' /></td>';
  $res.='</tr>';
  $res.='<tr>';
        $res .= '<td><span class="text">Номер</span></td>';
        $res .= '<td><input type="text" name="tnum" id="tnum" class="input" value="" size="7">
                    <img id="t_info" class="icons" alt="Расчитать" title="Расчитать" onclick="calc_turn()" src="buttons/info3.gif">
                    <input type="text" id="unit_num" class="input" placeholder="юнит" value="" size="3" maxlength = "4" style="display:none;">
                    <input type="text" id="contr_num" placeholder="прошивка" class="input" value="" size="7" maxlength = "2" style="display:none;"></td>';
  $res.='</tr>';
  $res.='<tr>';
  $res.='<td><span class="text" >Название</span></td>';
  $res.='<td><p class="text"><input name="nameturn" type="text" value="" size="35" maxlength="32" class="input"></p></td>';
  $res.='</tr>';
  $res.='<tr>';
        $res .= '<td><span class="text">Добавить к группе</span></td>';
        $res .= '<td><select name="turngroup"><option value="0">Не добавлять</option>';
                $q='select  *from BASE_W_S_TURN_GROUP(NULL)';
                $result = pg_query($q);
                while($r = pg_fetch_array($result))
                {
                   $res .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
                }
        $res .= '</select>';
        $res .='</td>';
  $res.='</tr>';
  $res .= '<tr>';
        $res .= '<td colspan="2">
                 <textarea name="turndesc" rows="4" cols="50" placeholder="Описание"></textarea>
                 </td>';
  $res .= '</tr>';
  $res .= '<tr>';
        $res .= '<td><span class="text">Тип устройства</span></td>';
        $res .= '<td>';
              $res .= '<select name="turn_type">';
              $result=pg_query('select * from base_turn_type');
              while($r = pg_fetch_array($result))
              {
                 $res .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
              }
        $res .= '</select></td>
  </tr>
    <tr>
        <td><span class="text">Считыватель на входе</span></td>
        <td>
              <select name="reader_in">';
              $result=pg_query('select * from base_reader_type');
              while($r = pg_fetch_array($result))
              {
                 $res .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
              }
        $res .= '</select></td>
  </tr>
  <tr>
        <td><span class="text">Считыватель на выходе</span></td>
        <td>
              <select name="reader_out">';
              $result=pg_query('select * from base_reader_type');
              while($r = pg_fetch_array($result))
              {
                 $res .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
              }
        $res .= '</select></td>
  </tr>
  <tr>
       <td><span class="text">Статус</span></td>
       <td>
            <input id="flag_block" type="checkbox" name="flag_block"><span class="text">Блокиророванный</span><br>
            <input id="flag_terr_in" type="checkbox" name="flag_terr_in"><span class="text">Контроль двойных засечек на вход</span><br>
            <input id="flag_terr_out" type="checkbox" name="flag_terr_out"><span class="text">Контроль двойных засечек на выход</span><br>
       </td>
  </tr>
  <tr>
        <td><span class="text">Внешняя территория</span></td>
        <td>
              <select name="in_terr"><option value="0">Нет</option>';
              $result=pg_query('select * from BASE_W_S_TERRITORY(NULL)');
              while($r = pg_fetch_array($result))
              {
                 $res .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
              }
        $res .= '</select></td>';
  $res .= '</tr>';
   $res .= '<tr>';
        $res .= '<td><span class="text">Внутренняя территория</span></td>';
        $res .= '<td>';
              $res .= '<select name="out_terr"><option value="0">Нет</option>';
              $result=pg_query('select * from BASE_W_S_TERRITORY(NULL)');
              while($r = pg_fetch_array($result))
              {
                 $res .= '<option value="'.$r['id'].'">'.$r['name'].'</option>';
              }
        $res .= '</select></td>';
  $res .= '</tr>';
  $res.='<tr>';
        $res.='<td colspan="2">';
            $res .= '<div id="statusbar" style="display:none;margin:2px;border-top:1px solid midnightblue;width:100%;padding:2px;font-family:Verdana;font-size:8pt; color:black;" >';
                 $res.='<img src="buttons/indicator.gif" width="20" height="20" style="margin-right:10px;">';
                 $res.='<span class="text">&nbsp;&nbsp;Обработка...</span>';
            $res .= '</div>';
        $res.='</td>';
  $res.='</tr>';
  $res.='<tr class="tablehead">
          <td colspan="1" align="left"><input name="addturnbut" type="button" class="sbutton" value="сохранить"  onclick=\'AddTurn(document.addturnfrm,"add")\' /></td>
          <td align="right"><input type="button" class="sbutton" value="отмена" onclick=\'ShowCloseModalWindow("addturn",1)\' /></td>
          </tr>';
  $res.='</table>';
  $res.='</form>';
  $res.='</div>';




  return $res;
}
function ShowModeList($flag)
{
  $IDMODUL=7;
 if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
 {
   require_once("include/menu.php");
   echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
   exit();
 }

  $res='';
   $col1="silver";
   $col2="#f5f5dc";
   $bgcolor='';
   $flag=0;
   $res.='<script type="text/javascript">
           window.onload=function(){
           var timeArray=new Array();
           var timeValue=document.getElementById("timevalue");
           var panel=document.getElementById("timepanel-0");
           var timeline=document.getElementById("timeline");

          // заполняем массив и строку времени нулями
           for(var i=0;i<96;i++)timeArray[i]=0;
           timeValue.value=timeArray;
          //генерим отображение временной оси
           for(var i=0;i<24;i++)
           {
             var subpanel=document.createElement("div");
                 subpanel.id="0"+"-"+i;
                 subpanel.className="timebasesubpanel";

             for(var j=0;j<4;j++)
             {
               var item=document.createElement("div");
                   item.id="0"+"-"+i+"-"+j;
                   item.className="timebaseitem";
                   item.onclick=function(){
                     ClickItem(this.id,this,"timevalue");
                   }

                   subpanel.appendChild(item);
             }
            panel.appendChild(subpanel);

           }
           //создаём линейку времени
           for(var i=0;i<24;i++)
           {
              var tl=document.createElement("div");
              tl.className="timeline";
              var time=i;

              var text=document.createTextNode(time);
                  tl.appendChild(text);
              timeline.appendChild(tl);
           }
    }
      //при выделении промежутка
      function ClickItem(id,obj,timeStr)
      {

        var timeStr=document.getElementById(timeStr);
        //alert(tStr.id);
        //alert(id);alert(obj.id);alert(timeStr);
        var temp = new Array(); //временный массив, куда помещаем
                                //значение timeStr;
        temp=timeStr.value.split(",");
        var idstr = new Array()
        idstr=id.split("-");
        var panel=idstr[0];
        var sub=idstr[1];
        var item=idstr[2];
        var pos;//позция в массиве текущего элемента
        pos=(sub*4)+parseInt(item);

        if(temp[pos]==0)
        {
           obj.style.backgroundColor="green";
           temp[pos]=1;
        }
        else
         {
           obj.style.backgroundColor="white";
             temp[pos]=0;
         }
          timeStr.value=temp;
       }

    function AddMode(f)
    {
      var err=0;
      if(f.modename.value.length==0)
      {
        alert("Режим должени иметь название");
        f.modename.focus();
        err=1;
        return;
      }

      if(CheckString(f.modename.value)==1)
      {
        alert("Недопустимый символ при вводе названия режима");
        f.modename.focus();
        err=1;
        return;
      }
      if(err==0)
       f.submit();

    }
    function SaveChanges(f)
    {
      var err=0;
      var savestring=null;
      for(var i=0;i<f.length;i++)
      {
        if(f.elements[i].name.indexOf("modename",0)>-1)
        {
           var item=f.elements[i];
           if(item.value.length==0)
            {
              alert("Режим должен иметь название");
              item.focus();
              err=1;
              return;
            }
            if(CheckString(item.value)==1)
             {
              alert("Недопустимый символ при вводе названия режима");
              item.focus();
              err=1;
              return;
            }
        }

      }
     if(err==0)
       f.submit();
    }

    function SetInterval(start,finish,timeStr)
    {
       var timeStr=document.getElementById(timeStr);
       var st=document.getElementById(start);
       var fin=document.getElementById(finish);
       //alert(st.value+"-"+fin.value);
       if(st.value=="00:00" && fin.value=="00:00")return;
       if(isTime(st.value)==false || isTime(fin.value)==false)return;
       var shh=st.value.substr(0,2);
       if(shh.substr(0,1)==0)shh=shh.substr(1,1);
       var smm=st.value.substr(3,2);
       var fhh=fin.value.substr(0,2);
       if(fhh.substr(0,1)==0)fhh=fhh.substr(1,1);
       var fmm=fin.value.substr(3,2);
       //alert(shh);alert(smm);
       var hourstpos,minstpos,hourfinpos,minfinpos;
       hourstpos=parseInt(shh);
       minstpos=parseInt(smm);
       hourfinpos=parseInt(fhh);
       minfinpos=parseInt(fmm);

       var stpos=(hourstpos*4);
       var finpos=(hourfinpos*4);
       for(var j=0;j<=60;j=j+15)
       {
          var f=j+15;
          if(minstpos<=f && minstpos>=j)
          {
            if(minstpos==0)
            stpos+=0;
            else
            stpos=stpos+(f/15);
            break;
          }
       }
       for(var j=0;j<=60;j=j+15)
       {
          var f=j+15;
          if(minfinpos<=f && minfinpos>=j)
          {
            if(minfinpos==0)
            finpos+=0;
            else
            finpos=finpos+(f/15);
            break;
          }
       }
      var temp=new Array();
      var temp=timeStr.value.split(",");
      for(var k=stpos;k<finpos;k++)
           temp[k]=1;

      timeStr.value=temp;
      //temp=timeStr.value.split(",");

      for(var i=0;i<24;i++)
      {
        for(var j=0;j<4;j++)
        {
            var id;id="0"+"-"+i+"-"+j;
          var el=document.getElementById(id);

          if(temp[(i*4)+j]==1)
            el.style.backgroundColor="green";
           else
             el.style.backgroundColor="white";
        }
      }

    }
    function InactiveMode()
    {
       alert("Редактировать данный режим запрещено системой");
    }
    </script>';

   $res.='<form name="modelist" action="directories.php?action=save&amp;list=mode" method="POST">';
   $result=pg_query('select * from BASE_W_S_REG_NAME(NULL)');

   $countitem=1;
   $iditems=null;
   $eidtting = '';
   $inactive = 'silver';
   $write = '';

   while($r=pg_fetch_array($result))
   {
    $reg_code=null;
    $res.='<br><table border=0 cellpadding="0" cellspacing="0" width=100% class="dtab" >';

    if( $r['id'] == 1 || $r['id'] == 2) $write = 'readonly';
      else $write = '';

    $res.='<tr><td align="left"><input type="text" size="20" class="input" value="'.$r['name'].'"  maxlength="50" name="modename'.$r['id'].'" '.$write.'>
				<input type="hidden" size="20" class="input" value="'.$r['name'].'"  maxlength="50" name="modename'.$r['id'].'old" >
            </td></tr>';
    $res.='<tr><td align="left">
              <div  class="timebasepanel-'.$r['id'].'">';
              for($j=0;$j<24;$j++)
              {
                 $res.='<div id="'.$r['id'].'-'.$j.'" class="timebasesubpanel">';
                 for($k=0;$k<4;$k++)
                 {
                    $bgcolor='';
                    $position=($j*4)+$k;
                    $currentbyte=substr($r['rej_code'],$position,1);
                    if($currentbyte=='0')
                       $bgcolor='white';
                      else
                        $bgcolor='green';

                    if( $r['id'] == 1 || $r['id'] == 2)
                    {
                      $editing = 'InactiveMode()';
                      if( $currentbyte == 0)$bgcolor = $inactive;
                         else $bgcolor = 'green';
                    }
                    else
                    {
                      $editing = 'ClickItem("'.$r['id'].'-'.$j.'-'.$k.'",this,"timevalue-'.$r['id'].'")';
                    }

                    $res.='<div id="'.$r['id'].'-'.$j.'-'.$k.'" class="timebaseitem" style="background-color:'.$bgcolor.'" onclick=\''.$editing.'\' >';
                    $res.='</div>';
                    $reg_code.=$currentbyte;
                    $reg_code.=',';
                 }
                 $res.='</div>';

              }

    $res.=   '</div>
              <div id="timeline-'.$r['id'].'" class="timebasepanel">';
            for($i=0;$i<24;$i++)
            {
              $_time = $i;
              $res.='<div class="timeline" style="background-color:#f5f5f5;">'.$_time.'</div>';

           }
    $res.='</div></td>
          </tr>';
   $res.='<tr><td align="right">';
   $reg_code=substr($reg_code,0,strlen($reg_code)-1);
   $res.='<input id="timevalue-'.$r['id'].'"  name="timevalue-'.$r['id'].'" type="hidden" size="20" class="input" value="'.$reg_code.'">';
   $res.='<input id="timevalue-'.$r['id'].'old"  name="timevalue-'.$r['id'].'old" type="hidden" size="20" class="input" value="'.$reg_code.'">';
    if($r['id']!=1 && $r['id']!=2)
      $res.='<input type="button" class="sbutton" value="удалить" onclick=\'document.location.href="directories.php?action=del&amp;list=mode&amp;rid='.$r['id'].'"\' />';
   $res.='</td></tr>';
   $res.='</table>';
   $countitem++;
   $iditems.=$r['id'].',';
   }
   $iditems=substr($iditems,0,strlen($iditems)-1);
   $res.='<input type="hidden" name="iditems" value="'.$iditems.'">';
   $res.='</form>';


   $res.='<form action="directories.php?action=add&amp;list=mode" method="POST" name="addmode">';
   $res.='<table border=0 cellpadding="0" cellspacing="0" width=100% style="BORDER-TOP:1px solid gray;BORDER-LEFT:1px solid gray;BORDER-RIGHT:1px solid gray;BORDER-BOTTOM:1px solid gray;">';
   $res.='<tr><td align="left" colspan="2"><p class="text">Создание нового режима</p></td></tr>';

   $res.='<tr><td align="left" width="20%"><p class="text">Название:</p></td>
               <td align="left" width=""><p class="text">Промежуток времени:(минимальный интервал 15 минут)</p></td>
         </tr>';

   $res.='<tr><td align="left"><input type="text" size="20" class="input" maxlength="50" name="modename"></td>
              <td align="left"><font family=verdana size=3><b>c:</b></font>
              <input id="stime" type="text" size="4" maxlength="5" class="input" value="00:00">
                               <font family=verdana size=3><b>по:</b></font>
              <input id="ftime" type="text" size="4" maxlength="5" class="input" value="00:00">
              <input type="button" value="ок"  class="sbutton" onClick=\'SetInterval("stime","ftime","timevalue")\' />

              </td>
         </tr>';
   $res.='<tr><td align="left"  colspan="2"><p class="text">Временная ось:</p></td></tr>';
   $res.='<tr><td align="left"  colspan="2"><div id="timepanel-0" class="timebasepanel"></div>
              <div id="timeline" class="timebasepanel"></div>
              </td>
          </tr>';
   $res.='<tr><td align="right"  colspan="2"><input id="timevalue" name="timepanelvalue" type="hidden" size="160" class="input">
          <input type="button" value="добавить" class="sbutton" onclick=\'AddMode(document.addmode)\' /></td></tr>';
   $res.='</table>';
   $res.='</form>';

   $res.='<table border=0 cellpadding="0" cellspacing="0" width=100%>';
   $res.='<tr class="tablehead"><td align="right">';
   $res.='<input type="button" value="сохранить" class="sbutton" onclick=\'SaveChanges(document.modelist)\' />';
   $res.='</td></tr>';
   $res.='</table>';


   return $res;
}
/******************************************************************************/
include("include/input.php");
require("include/common.php");
require("include/head.php");

$msg='';

if(!isset($_REQUEST['action']))$_REQUEST['action']='';
if(!isset($_REQUEST['list']))
{
  $_REQUEST['list']='';
  $msg='<center><p class="text">Не выбрано ни одного справочника</p></center>';
  exit();
}


//выбор справочника для работы  с ним
if(($_REQUEST['action']=='show' || $_REQUEST['action']=='select') && $_REQUEST['list']!='')
{
  $chooseflag=0;// флаг нужен для определения выведен ли какой нибудь справочник
                //или нет
  //определяем действие (просмотр или выбор)
  $selflag=0;
  if($_REQUEST['action']=='select')$selflag=1;else $selflag=0;

  //справочник территорий
  if($_REQUEST['list']=='terr')
  {
   echo PrintHead('СКУД','Справочник территорий');
   require("include/menu.php");
   $chooseflag=1;
   echo ShowTerrList($selflag);
  }
  //справочник рабочих зон
  if($_REQUEST['list']=='workzone')
  {
   echo PrintHead('СКУД','Справочник рабочих зон');
   require("include/menu.php");
   $chooseflag=1;
   echo ShowZoneList();
  }


  if($_REQUEST['list']=='dopusk')
  {
   echo PrintHead('СКУД','Справочник допусков');
   require("include/menu.php");
   $chooseflag=1;
   echo ShowDopuskList($selflag);
  }
  //справочник турникетов
  if($_REQUEST['list']=='turn')
  {
   echo PrintHead('СКУД','Справочник турникетов');
   require("include/menu.php");
   $chooseflag=1;
   echo ShowTurnList($selflag);
  }
   //справочник режимов
  if($_REQUEST['list']=='mode')
  {
   echo PrintHead('СКУД','Справочник режимов');
   require("include/menu.php");
   $chooseflag=1;
   echo ShowModeList($selflag);
  }
  //если некорректный выбор справочника
  if($chooseflag==0)
  {
   echo PrintHead('СКУД','');
   require("include/menu.php");
   $msg='<center><p class="text">Не выбрано ни одного справочника</p></center>';
  }

}
//Обработка событий на добавление правку
if(($_REQUEST['action']=='add' || $_REQUEST['action']=='save'  || $_REQUEST['action']=='edit' || $_REQUEST['action']=='del') && $_REQUEST['list']!='')
{
     $actionflag=0;
  
///////////////////////////////////////////////////////////////////////////////
     if($_REQUEST['list']=='terr')
     {

         $IDMODUL=6;
         if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
         {
           echo PrintHead('СКУД','Справочник территорий');
           require_once("include/menu.php");
           echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
           exit();
         }
         $q='';
         $actionflag=1;
         $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
         if($_REQUEST['action']=='add')
         $q.='select BASE_W_I_TERRITORY(\''.CheckString($_REQUEST['nameter']).'\',\''.$_REQUEST['descrip'].'\')';

         if($_REQUEST['action']=='del' && is_numeric($_REQUEST['tid'])!=0 && $_REQUEST['tid']>0)
         {$q.='select BASE_W_D_TERRITORY('.$_REQUEST['tid'].')';}

         if($_REQUEST['action']=='edit' && is_numeric($_REQUEST['tid'])!=0 && $_REQUEST['tid']>0)
         {$q.='select BASE_W_U_TERRITORY('.$_REQUEST['tid'].',\''.CheckString($_REQUEST['nameter']).'\',\''.$_REQUEST['descrip'].'\')';}

         if($q!='')
          pg_query($q) or die('<center><p class="text">Ошибка при выполнении запроса </p></center>');
         else
             $msg='<center><p class="text">Не возможно выполнить действие</p></center>';

        echo PrintHead('СКУД','Справочник территорий');
        require("include/menu.php");
        echo ShowTerrList(0);
     }
///////////////////////////////////////////////////////////////////////////////////
    if($_REQUEST['list']=='workzone')
    {

         $IDMODUL=11;
         if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
         {
           echo PrintHead('СКУД','Справочник рабочих зон');
           require_once("include/menu.php");
           echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
           exit();
         }

          $SS=array();
          $actionflag=1;
          if(isset($_REQUEST['selstr']) && $_REQUEST['selstr']!='')
          $SS=explode(",",$_REQUEST['selstr']);

          if($_REQUEST['action']=='add')
          {
                $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
               $q.='select * from BASE_W_I_ZONE(\''.CheckString($_REQUEST['namezone']).'\',\''.CheckString($_REQUEST['descrip']).'\')';
               $r=pg_fetch_array(pg_query($q));
               $id=$r['id'];
               if(sizeof($SS)!=0)
               {
                 for($i=0;$i<sizeof($SS);$i++)
                 {
                   $q='select BASE_W_I_ZONE_TERR('.$id.','.$SS[$i].',0)';
                   pg_query($q) or die("Ошибка при добавление территорий");
                 }
               }
          }

          if($_REQUEST['action']=='del'&& isset($_REQUEST['zid']) && is_numeric($_REQUEST['zid'])!=0 && $_REQUEST['zid']>0)
          {
              $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
              $q.='select BASE_W_D_ZONE('.$_REQUEST['zid'].')';
             pg_query($q);
          }
          //Обработка редактирования
          if($_REQUEST['action']=='save')
          {
             $idcort='';
             $IS=array();
             $IS=explode(";",@$_REQUEST['itogstr']);
             $OS=explode(";",@$_REQUEST['itogstrold']);
            for($i=0;$i<sizeof($IS);$i++)
            {
             // разбиваем массивы на массивы id-ов для каждой зоны
             $item=explode(',',$IS[$i]);
             $item1=explode(',',$OS[$i]);
             $idzone=$item[0];//id зоны для которой выполняются обновления
                       //всегда идёт первым элементом
             array_shift($item);
             array_shift($item1);
            //смотрим было ли добавление элементов

            $newarr=array_diff($item,$item1);
            sort($newarr,SORT_NUMERIC);
            //print_r($newarr);

              if(sizeof($newarr)!=0)
              {
                  //echo "Добавление"; print_r($newarr);echo '<br>';
                 for($j=0;$j<sizeof($newarr);$j++)
                 {
                    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                    $q .= 'select BASE_W_I_ZONE_TERR('.$idzone.','.$newarr[$j].',0)';
                    pg_query($q);
                 }
               }
            //проверяем  было ли удалённие элементов
               $newarr=array_diff($item1,$item);
               sort($newarr,SORT_NUMERIC);
               //print_r($newarr);
               if(sizeof($newarr)!=0)
               {
                 // echo "Удаление"; print_r($newarr);echo '<br>';
                  for($j=0;$j<sizeof($newarr);$j++)
                  {
                      $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                    $q .= 'select BASE_W_I_ZONE_TERR('.$idzone.','.$newarr[$j].',1)';
                    pg_query($q);
                   }
               }
            }
            //сохраняем изменения в описание и именах
          if(@$_REQUEST['savestr']!='')
          {
             $SS=explode(",",@$_REQUEST['savestr']);
             $SS=array_unique($SS);
             sort($SS,SORT_NUMERIC);
            
            if(sizeof($SS)>0)
            {
              for($i=0;$i<sizeof($SS);$i++)
              {
                 $itemname=$_REQUEST['z'.$SS[$i].'name'];
                 $itemdesc=$_REQUEST['z'.$SS[$i].'descript'];
                  $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                 $q.='select BASE_W_U_ZONE('.$SS[$i].',\''.CheckString($itemname).'\',\''.CheckString($itemdesc).'\')';
                
                 pg_query($q) or die('<p class="text">Невозможно выполнить запрос</p>');
              }
            }
           }
          }

        echo PrintHead('СКУД','Справочник рабочих зон');
        require("include/menu.php");
        echo ShowZoneList();
        $_REQUEST['action']='show'; $_REQUEST['list']='workzone';

  }

  if($_REQUEST['list']=='mode')
  {
      $IDMODUL=7;
      if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
      {
         echo PrintHead('СКУД','Справочник Режимов');
         require_once("include/menu.php");
         echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
         exit();
      }

     $actionflag=1;
     if($_REQUEST['action']=='add')
     {
        if(isset($_REQUEST['timepanelvalue']))
        {
            $reg_code_arr=explode(",",$_REQUEST['timepanelvalue']);
            $reg_code=null;
            for($i=0;$i<sizeof($reg_code_arr);$i++)
            {
              $reg_code.=$reg_code_arr[$i];
            }
             $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
            $q.='select BASE_W_I_REG_NAME(\''.CheckString($_REQUEST['modename']).'\',\''.$reg_code.'\')';
            pg_query($q) or die('<center><p class="text">Ошибка при выполнении запроса</p></center>');
        }
     }
    if($_REQUEST['action']=='save')
    {
        $name_num = array();
        $idsave=array();
        $ID=explode(",",$_REQUEST['iditems']);
        //проверяем были ли изменения
        for($i=0;$i<sizeof($ID);$i++)
        {
           $item='timevalue-'.$ID[$i];
           $olditem='timevalue-'.$ID[$i].'old';
	   
            $itemn='modename'.$ID[$i];
           $olditemn='modename'.$ID[$i].'old';
          // echo '<br>'.$item.'<br>'.$olditem.'<br>'.$itemn.'<br>'.$olditemn.'<br>';
	  // echo '<br>'.$_REQUEST[$item].'<br>'.$_REQUEST[$olditem].'<br>'.$_REQUEST[$itemn].'<br>'.$_REQUEST[$olditemn].'<br>';
           if($_REQUEST[$item]!=$_REQUEST[$olditem] || $_REQUEST[$itemn]!=$_REQUEST[$olditemn])
            {
               $idsave[]=$ID[$i];
	       $name_num[]=$ID[$i];
            }

        }
       if(sizeof($idsave)!=0)
       {
           for($i=0;$i<sizeof($idsave);$i++)
           {
               $name='modename'.$name_num[$i];
               $timeval='timevalue-'.$idsave[$i];
               $reg_code_arr=explode(",",$_REQUEST[$timeval]);
               $reg_code=null;
               for($j=0;$j<sizeof($reg_code_arr);$j++)
                  $reg_code.=$reg_code_arr[$j];
                 $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
              $q.='select BASE_W_U_REG_NAME('.$idsave[$i].',\''.CheckString($_REQUEST[$name]).'\',\''.$reg_code.'\')';
	      //echo '<br>'.$q.'<br>';
             pg_query($q) or die ('<center><p class="text">Не возможно выполнить запрос </p></center>');
           }
       }

    }

    if($_REQUEST['action']=='del' && isset($_REQUEST['rid']) && is_numeric($_REQUEST['rid'])!=0 && $_REQUEST['rid']>0)
    {
         $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
         $q .= 'select BASE_W_D_REG_NAME('.$_REQUEST['rid'].')';
      pg_query($q) or die ('<center><p class="text">Не возможно выполнить запрос </p></center>');
    }
    echo PrintHead('СКУД','Справочник Режимов');
    require("include/menu.php");
    echo ShowModeList(0);
    $_REQUEST['action']='show'; $_REQUEST['list']='mode';
  }

  if($_REQUEST['list']=='turngroup')
  {
    $IDMODUL=5;
    if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
    {
      echo PrintHead('СКУД','Справочник  турникетов');
     require_once("include/menu.php");
     echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
     exit();
     }
    $actionflag=1;
    if($_REQUEST['action']=='add')
    {//сначала добавляем группу турникетов
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
       $q.='select BASE_W_I_TURN_GROUP(\''.CheckString($_REQUEST['namegroup']).'\',\''.CheckString($_REQUEST['descrip']).'\')';
        $result= pg_query($q) or die('<center><p class="text">Не возможно выполнить запрос </p></center>');
        while($r = pg_fetch_array($result)) 
        {
            $idgroup=$r[0];
        }
      
       if(strlen($_REQUEST['selstr0'])!=0)
       {
          $ID=explode(",",$_REQUEST['selstr0']); 
          for($i=0;$i<sizeof($ID);$i++)
          {
              $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
              $q.='select BASE_W_U_TURN_G('.$ID[$i].','.$idgroup.')';
              pg_query($q) or die('<center><p class="text">Не возможно выполнить запрос </p></center>');
           }
         }
      }
    if($_REQUEST['action']=='del' && is_numeric($_REQUEST['gid'])!=0 && $_REQUEST['gid']>0)
    {
        $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
        $q.='select BASE_W_D_TURN_GROUP('.$_REQUEST['gid'].')';
        pg_query($q)  or die('<center><p class="text">Не возможно выполнить запрос </p></center>');
    }
    if($_REQUEST['action']=='save')
    {
      if(strlen($_REQUEST['editingstr'])!=0)
      {
          $ID=explode(",",$_REQUEST['editingstr']);
          $ID=array_unique($ID);
          sort($ID,SORT_NUMERIC);
          for($i=0;$i<sizeof($ID);$i++)
          {
              $itemname='groupname'.$ID[$i];
              $itemdesc='descript'.$ID[$i];
              $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
              $q.='select BASE_W_U_TURN_GROUP('.$ID[$i].',\''.CheckString($_REQUEST[$itemname]).'\',\''.trim(CheckString($_REQUEST[$itemdesc])).'\')';
             pg_query($q) or die('<center><p class="text">Не возможно выполнить запрос </p></center>');
          }
      }
      if(strlen($_REQUEST['itogselstrold'])!=0 && strlen($_REQUEST['itogselstr'])!=0)
      {
        $NS=explode(";",$_REQUEST['itogselstr']);
        $OS=explode(";",$_REQUEST['itogselstrold']);
        for($i=0;$i<sizeof($NS);$i++)
        {
           $item=explode(",",$NS[$i]);
           $item1=explode(",",$OS[$i]);
           $idgroup=$item[0];
           array_shift($item);
           array_shift($item1);
           
           $newarr=array_diff($item1,$item);
           sort($newarr,SORT_NUMERIC);
           if(sizeof($newarr)!=0)
           {
              for($j=0;$j<sizeof($newarr);$j++)
              {
                $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                $q.='select BASE_W_U_TURN_G('.$newarr[$j].',NULL)';
                pg_query($q) or die('<center><p class="text">Не возможно выполнить запрос </p></center>');
              }
           }
        }
        for($i=0;$i<sizeof($NS);$i++)
        {
           $item=explode(",",$NS[$i]);
           $item1=explode(",",$OS[$i]);
           $idgroup=$item[0];
           array_shift($item);
           array_shift($item1);
           
           $newarr=array_diff($item,$item1);
           sort($newarr,SORT_NUMERIC);
           if(sizeof($newarr)!=0)
           {  for($j=0;$j<sizeof($newarr);$j++)
              {
                $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                $q.='select BASE_W_U_TURN_G('.$newarr[$j].','.$idgroup.')';
                pg_query($q) or die('<center><p class="text">Не возможно выполнить запрос </p></center>');
              }
           }
        }
      }
    }
    echo PrintHead('СКУД','Справочник турникетов');
    require("include/menu.php");
    echo ShowTurnList(0);
  }

  //допуска
  if($_REQUEST['list']=='dopusk')
  {
    //print_r($_REQUEST);
    $IDMODUL=8;
    if(CheckAccessToModul($IDMODUL,$_SESSION['modulaccess'])==false)
    {
       echo PrintHead('СКУД','Справочник допусков');
       require_once("include/menu.php");
       echo "<center><p class=text><b>Доступ закрыт. Не хватает прав доступа</b></p></center>";
       exit();
    }

    $actionflag=1;
    if($_REQUEST['action']=='add')
    {
      $status='00000000';
      if(isset($_REQUEST['incheck']) && !isset($_REQUEST['outcheck']))$status='01000000';
      if(isset($_REQUEST['outcheck'])&& !isset($_REQUEST['incheck'])) $status='00100000';
      if(isset($_REQUEST['outcheck'])&& isset($_REQUEST['incheck'])) $status='01100000';
      $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
      $q.='select * from BASE_W_I_DOPUSK(\''.CheckString($_REQUEST['namedop']).'\',\''.$status.'\')';
      pg_query($q) or die('<center><p class="text">Не возможно выполнить запрос</p></center>');

    }
    if($_REQUEST['action']=='del' && is_numeric($_REQUEST['did'])!=0 && $_REQUEST['did']>0)
    {
            $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
            $q.='select BASE_W_D_DOPUSK('.$_REQUEST['did'].')';
            
            pg_query($q) or die('<center><p class="text">Не возможно выполнить запрос</p></center>');
    }

    if($_REQUEST['action']=='save')
    {
      //сохраняем имя и статус
      if(isset($_REQUEST['itnamestr']))
      {
        
         $SS=explode(',',$_REQUEST['itnamestr']);
         $SS=array_unique($SS);
         sort($SS,SORT_NUMERIC);
         if(sizeof($SS)>0 && $SS[0]!=0)
         {
            for($i=0;$i<sizeof($SS);$i++)
            {
              $itemname=$_REQUEST['dopname'.$SS[$i]];
              if ($itemname===null) $itemname=$_REQUEST['dopnameold'.$SS[$i]];
              $itemstatus='00000000';
              if(isset($_REQUEST['inst'.$SS[$i]]))$itemstatus='01000000';
              if(isset($_REQUEST['outst'.$SS[$i]]))$itemstatus='00100000';
              if(isset($_REQUEST['inst'.$SS[$i]]) && isset($_REQUEST['outst'.$SS[$i]]))$itemstatus='01100000';
              $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
              $q.='select BASE_W_U_DOPUSK('.$SS[$i].',\''.trim(CheckString($itemname)).'\',\''.$itemstatus.'\')';
             
             pg_query($q) or die('<p class="text">Не возможно выполнить запрос</p>');
            }
         }
      }

      //сохраняем значения групп турникетов
      if(isset($_REQUEST['itgroupstr']))
      {
         $NSS = explode(";",$_REQUEST['itgroupstr']);
         $OSS = explode(";",$_REQUEST['itgroupstrold']);
         $size = sizeof($NSS);
         for($i=0;$i<$size;$i++)
         {
           $newitem = explode(",",$NSS[$i]);
           $olditem = explode(",",$OSS[$i]);
           $id_dopusk = $newitem[0];

        if(sizeof($newitem)==sizeof($olditem))
        {
         if(sizeof($olditem)==1 && sizeof($newitem)==1)
         {
           $temp=explode("-",$olditem[0]);
           if(@$temp[1]==0 && @$temp[2]==0)
           {
		$val = explode("-",$newitem[0]);
                $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                $q .=  'select BASE_W_I_DOPUSK_REG('.@$val[0].','.@$val[1].','.@$val[2].',0)';

             @pg_query($q);

             echo PrintHead('СКУД','Справочник допусков');
             require("include/menu.php");
             echo ShowDopuskList(0);
             exit();
           }
         }
         $updateval = array_diff($newitem,$olditem);
         sort($updateval,SORT_NUMERIC);
         if(sizeof($updateval)>0)
         {
           for($j=0;$j<sizeof($updateval);$j++)
             {
                $VAL = explode("-",$updateval[$j]);
                $id_dopusk = $VAL[0];
                $id_tg = $VAL[1];
                $id_reg = $VAL[2];
                $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                $q .=  'select BASE_W_I_DOPUSK_REG('.$id_dopusk.','.$id_tg.','.$id_reg.',2)';
                 pg_query($q);
          }
         }
        }
         //проверяем было ли добавление
         $addval = array_diff($newitem,$olditem);
         sort($addval,SORT_NUMERIC);
         if(sizeof($addval)>0 && sizeof($newitem)!=sizeof($olditem))
         {

             for($j=0;$j<sizeof($addval);$j++)
             {
                $VAL = explode("-",$addval[$j]);
                $id_dopusk = $VAL[0];
                $id_tg = $VAL[1];
                $id_reg = $VAL[2];
                if($id_tg>0 && $id_reg>0)
                {
                    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                   $q .=  'select BASE_W_I_DOPUSK_REG('.$id_dopusk.','.$id_tg.','.$id_reg.',0)';
                   pg_query($q);
                }
             }
         }
         //проверяем было ли удаление
         $remval = array_diff($olditem,$newitem);
         sort($remval,SORT_NUMERIC);
         if(sizeof($remval)>0 && sizeof($newitem)!=sizeof($olditem))
         {
             for($j=0;$j<sizeof($remval);$j++)
             {
                $VAL = explode("-",$remval[$j]);
                $id_dopusk = $VAL[0];
                $id_tg = $VAL[1];
                $id_reg = $VAL[2];
                if($id_tg>0 && $id_reg>0)
                {
                    $q = 'insert into temp_user_id_ip_info values ('.$_SESSION['iduser'].',\''.$_SERVER['REMOTE_ADDR'].'\',\''.add_rubish($_SERVER['HTTP_USER_AGENT']).'\');';
                   $q .=  'select BASE_W_I_DOPUSK_REG('.$id_dopusk.','.$id_tg.','.$id_reg.',1)';
                    pg_query($q);
                }
             }
         }

      }//цикл по $NSS


      }
    }//save
      echo PrintHead('СКУД','Справочник допусков');
    require("include/menu.php");
    echo ShowDopuskList(0);
  }//dopusk

  if($actionflag==0)
  {
   echo PrintHead('СКУД','');
   require("include/menu.php");
   $msg='<center><p class="text">Выбранное действие не применимо ни к одному справочнику </p></center>';
  }
}

echo $msg;
echo PrintFooter();
ob_flush();
?>