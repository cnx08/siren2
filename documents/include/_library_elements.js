//REAPLACE 04.07.07
// Функция создаёт пустой элемент Select
// id - идентификатор создаваемого элемента
// name - имя элемента
//owner - владелец создаваемого элемента
function CreateSelect(id,name,nameClass,owner)
{
  var maket = document.createElement("select");
      maket.id = id;
      maket.name = name;
      maket.className = nameClass;
  if (owner)
  {
     var parent = document.getElementById(owner);
         if (parent) parent.appendChild(maket);
  }
  else
  {
   document.body.appendChild(maket);
  }
 return maket;
}
//Функция добавляет элемент options в Select id которого
// равен sid.
// val - значение
// text - текст
function AddOptions(sid,val,text)
{
  if (!sid) return;
  var select = document.getElementById(sid);
  if (!select) return;
  var opt = document.createElement("option");
      opt.value = val;
  var t = document.createTextNode(text);
      opt.appendChild(t);
      select.appendChild(opt);
  return  opt;

}
//Функция удаляет из Select элемент списка
// num номер удаляемого элемента списка
function RemoveOptions(sid,num)
{
  if (!sid) return;
  var select = document.getElementById(sid);
  if (!select) return;
  select.removeChild(select.childNodes[num]);

}

//Функция создаёт кнопку
//
function CreateButton(id,val,nameClass,owner)
{
   var maket = document.createElement("button");
      maket.id = id;
          maket.className = nameClass;
          maket.value = val;
  if (owner)
  {
     var parent = document.getElementById(owner);
         if (parent) parent.appendChild(maket);
  }
  else
  {
   document.body.appendChild(maket);
  }
 return maket;
}
//функция создаёт пустую дивку для списка
function createDiv(owner,id,name,nameClass)
{
   try
   {
       var element = document.createElement("div");
       element.id = id;
       element.name = name;
       element.className = nameClass;
       var parent = document.getElementById(owner);

       if(!parent) document.body.appendChild(element);
          else parent.appendChild(element);

      return element;
   }
   catch(e){alert("Ошибка при создании элемента");}
}