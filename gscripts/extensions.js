Array.prototype.indexOf = function(obj)
{
 var result =-1;
 for(var i=0;i<this.length;i++)
 {
    if(this[i]==obj){result=i;break;}
 }
 return result;
}
Array.prototype.contains = function(obj)
{
	return (this.indexOf(obj)>=0);
}
Array.prototype.append = function(obj,nodup)
{
  if(!(nodup && this.contains(obj)))
  {
    this[this.length] = obj;
  }
}
Array.prototype.remove = function(obj)
{
 var delflag = 0;
 for(i=0;i<this.length;i++)
 {
     if(this[i]==obj && delflag==0)
     {
     	delflag = 1; this[i] = this[i+1];
     }
     if(delflag==1)
     {
       this[i] = this[i+1];
     }
  }
  this.length = this.length-1;
}
Array.prototype.clear = function()
{
    var len = this.length;
   for(var i=0;i<len;i++)
   {
     this.shift();
   }

}
