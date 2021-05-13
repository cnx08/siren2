var CURRENTWINDOW = null;

function OnMenu(obj,flag)
{
  if(flag == 1)
     obj.style.backgroundColor = "#F5F5F5";
  else if(flag == 0)
   obj.style.backgroundColor = "white";

}
function ShowBody(obj)
{
  try
  {
   if(CURRENTWINDOW != null){
           var old = document.getElementById(CURRENTWINDOW);
           old.style.display = "none";
           }
   CURRENTWINDOW = obj.name;

    var w = document.getElementById(obj.name);
    w.style.display = "block";
  }
  catch(e)
  {
    alert("Ошибка: Объект отображения не найден.\n Попробуйте перезагрузить страницу");return;
  }
}

function fillMenu()
{
  var menuBox = document.getElementById("menubox");

  if(!menuBox) return;

  var menuItems = new Array();
      menuItems[0] = "Редактировать табель";
      menuItems[1] = "Реквизиты отчётов";
      menuItems[2] = "Документы";

  var menuItemName = new Array();//массив id окон которые будут отображаться при щелчке
                                 // по пункту меню
      menuItemName[0] = "tabedit";
      menuItemName[1] = "rsettings";
      menuItemName[2] = "documents";

  for ( var i = 0; i < menuItems.length; i++ )
  {
       var newItem = document.createElement("div");
           newItem.className = "menuItem";
           newItem.name = menuItemName[i];
       var t = document.createTextNode(menuItems[i]);
           newItem.appendChild(t);
           newItem.onmouseover = function(){OnMenu(this,1);}
           newItem.onmouseout = function(){OnMenu(this,0);}
           newItem.onclick = function()
           {
              ShowBody(this);
           }

           menuBox.appendChild(newItem);
  }

}
function CheckFiltr(f)
{
  //проверка текстовых полей фильтра на недопустиые символы
  for (var i = 0; i < f.elements.length; i++ )
  {
      var item = f.elements[i];
      if(item.type == "text")
      {
         if(CheckString(item.value)==1)
         {alert("Недопустимый символ при вводе");item.focus();return;}
      }
  }
  f.submit();
}

window.onload = function()
{
  fillMenu();
}