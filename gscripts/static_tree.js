Tree = function( idRootElement, idContainer, useCheckBoxes )
{
	
	this.rootElement = document.getElementById( idRootElement );
	this.container  = document.getElementById( idContainer );
	this.useCheckBoxes = useCheckBoxes;
	if ( !this.rootElement )
		return;
	
	if ( !this.container )
		return;	
	//события поддерживаемые деревом
	this.events = 
	{
		onClick : null,
		onDblClick : null
	};
	//присваиваем контейнеру ссылку на объект дерева
	this.container.treeObj = this;
	this.container.onclick = Tree.prototype.toggleNode;
	
	
	this.selelectedNode = null;
	this.checkedNodes = new Array();
	this.addNode = function ( content, parentNode )
	{
		//var iconDiv, checkbox, contentDiv, liElement;
		//alert (this.selectedNode);
	}
	
	this.destroy = function ()
	{
		this.container.treeObj = null;
		this.container = null;
		this.selelectedNode = null;
	}
	
	this.getSelectedNodeId  = function ()
	{
		if ( this.selelectedNode == null )
			return null;
		else 
			return this.selelectedNode.id;
	}
	
}

Tree.prototype = 
{
	
 	toggleNode : function (event)
	{
		event = event || window.event;
		var clickedElem = event.target || event.srcElement;
		var treeObj = this.treeObj;
		
		//очищаем выделеный элемент
		if ( treeObj.selectedNode != null )
				treeObj.selectedNode.className = '';
		//клик  на чекбоксе
		if ( clickedElem.tagName.toLowerCase() == 'input' )
		{
			
			if( treeObj.useCheckBoxes )
			{
					var checkValue = ( clickedElem.checked ) ? true : false;
					Tree.prototype.selectAllChildren(clickedElem.parentNode,checkValue,treeObj);
					
					return;
			}
			return;
		}	
		//клик  на название
		if ( clickedElem.tagName.toLowerCase() == 'span' )
		{
			
			clickedElem.className =  'Tree-LeafSelected';
			treeObj.selectedNode = clickedElem; 
			return;// клик не там
		}
		
		
		// Node, на который кликнули
		var node = clickedElem.parentNode
		// клик на листе
		if (Tree.prototype.hasClass(node, 'Tree-ExpandLeaf')) 
		{
			//alert ( node.id);
			return ;// клик на листе
		}
		
		//значит кликнули на развёртывание/свёртывание
		/*
		if ( node.className.indexOf('Tree-useTransport') != -1 )
		{
			alert ( ' using transport ' );
			clickedElem.className = ' Tree-ExpandLoading';
		}*/
		
		// определить новый класс для узла
		var newClass = Tree.prototype.hasClass(node, 'Tree-ExpandOpen') ? 'Tree-ExpandClosed' : 'Tree-ExpandOpen'
		// заменить текущий класс на newClass
		// регексп находит отдельно стоящий open|close и меняет на newClass
		var re =  /(^|\s)(Tree-ExpandOpen|Tree-ExpandClosed)(\s|$)/
		node.className = node.className.replace(re, '$1'+newClass+'$3')
		//alert ( node.id);
	
	},
	
	hasClass : function(elem, className) 
	{
		return new RegExp("(^|\\s)"+className+"(\\s|$)").test(elem.className)
	},
	
	selectAllChildren  : function ( rootNode, checkValue, treeObj)
	{
		
		for ( var i = 0; i < rootNode.childNodes.length; i ++ )
		{
			var node = rootNode.childNodes[i];
			
			if ( node.tagName != 'undefined' && node.tagName != null && node.tagName.toLowerCase() == 'input' )
			{
				var nodeId = node.id;
				if ( checkValue == true )
				{
				   //если нет его в списке выделенных то положить
				   if ( Tree.prototype.getChekedNodeIndex( treeObj.checkedNodes, nodeId ) < 0 )
				   {
						treeObj.checkedNodes.push(nodeId);
				   }
				}
				else
				{
					var nodeIndex = Tree.prototype.getChekedNodeIndex( treeObj.checkedNodes, nodeId );
					if ( nodeIndex >= 0  )
					{
						treeObj.checkedNodes[ nodeIndex ] = null;
					}
				}
				
				node.checked = checkValue;
			}
			if ( node.hasChildNodes() )
			{
				Tree.prototype.selectAllChildren(node,checkValue,treeObj);
			}
		
		}
	},
	
	getCheckedNodesAsStr : function ( treeObj, separator )
	{
		var str = '';
		
		//alert ( treeObj.checkedNodes.toString() );
		var validNodesCount = 0;  
		for( var i = 0; i < treeObj.checkedNodes.length; i++)
		{
			if( treeObj.checkedNodes[i] != null  )
			{
				str += treeObj.checkedNodes[i] + separator;
				validNodesCount ++ ;
			}
		}
		
		if ( validNodesCount >= 500 )
		{
			alert ('Внимание: вы выбрали более 500 узлов. MSSQL не может обработать такую порцию информации.\n Пожалуйста отменить выбор некоторых узлов.');
			return null;
		}
		
		return str.substr(0,str.length-1);
	},
	
	returnCheckedNodesTo : function (deptNameElement,deptIdElement )
	{
		
		var checkedStr = '';
		//может быти выбран один отдел
		if ( this.selectedNode != null )
		{
		
			checkedStr =  this.selectedNode.id;
			eval ('opener.document.' + deptNameElement + '.value="'+this.selectedNode.firstChild.nodeValue+'";');
			eval ('opener.document.' + deptIdElement + '.value="' + checkedStr +'";');
			self.close();
		}
		else
		{
			checkedStr = Tree.prototype.getCheckedNodesAsStr(this,',');
			//очищаем массив выбраных
			this.checkedNodes.length = 0;	
			this.checkedNodes = null;
			if ( checkedStr  != null  && checkedStr != '' )
			{
			
				eval ('opener.document.' + deptNameElement + '.value="множественный выбор";');
				eval ('opener.document.' + deptIdElement + '.value="' + checkedStr +'";');
				self.close();
			}	
		
			else if ( checkedStr == '')
			{
				if ( confirm('Вы не выбрали ни одного элемента.\n Хотетите завершить выбор? ') )
				{
					self.close();
				}			
			}
		}
		
		
		
	},
	
	getChekedNodeIndex : function (list,value)
	{
		for ( var i = 0; i < list.length;  i ++   )
		{
			if ( list[i] == value ) return i; 
		}
		return -1;
	}
}	
