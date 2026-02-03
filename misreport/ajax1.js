function GenericAjaxFunction(  queryString, outputDiv, recallInterval )
  {
	 //alert("hello") ;
  var xmlHttp;

  ////////////////////////////////
 try
    {
    xmlHttp=new XMLHttpRequest();
    }
  catch (e)
    {
    // Internet Explorer
    try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
    catch (e)
      {
      try
        {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
      catch (e)
        {
        alert("Your browser does not support AJAX!");
        return false;
        }
      }
    }
  ///////////////////////////////

    xmlHttp.onreadystatechange=function()
      {
      if(xmlHttp.readyState==4) //Page download complete
        {
		 document.getElementById(outputDiv).innerHTML=xmlHttp.responseText;
		 //document.write(xmlHttp.responseText);
		if(recallInterval>0)
		{
			 setTimeout("GenericAjaxFunction(  '"+queryString+"', '"+outputDiv+"' ,"+recallInterval+" )", recallInterval);
			 
		}
		}
     }

	xmlHttp.open("POST", queryString ,true); //Async.
	xmlHttp.send(null); 
  }


function GenericAjaxFunctionText(  queryString, outputDiv, recallInterval )
  {
	 //alert("hello") ;
  var xmlHttp;

  ////////////////////////////////
 try
    {
    xmlHttp=new XMLHttpRequest();
    }
  catch (e)
    {
    // Internet Explorer
    try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
    catch (e)
      {
      try
        {
        xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
      catch (e)
        {
        alert("Your browser does not support AJAX!");
        return false;
        }
      }
    }
  ///////////////////////////////

    xmlHttp.onreadystatechange=function()
      {
      if(xmlHttp.readyState==4) //Page download complete
        {
		 document.getElementById(outputDiv).value=xmlHttp.responseText;
		 //document.write(xmlHttp.responseText);
		if(recallInterval>0)
		{
			 setTimeout("GenericAjaxFunction(  '"+queryString+"', '"+outputDiv+"' ,"+recallInterval+" )", recallInterval);
			 
		}
		}
     }

	xmlHttp.open("POST", queryString ,true); //Async.
	xmlHttp.send(null); 
  }