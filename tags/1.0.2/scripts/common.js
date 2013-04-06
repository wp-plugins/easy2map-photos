// JavaScript Document
	
	jQuery.fn.center = function () {
		this.css("position","absolute");
		this.css("top", ((jQuery(window).height() - this.outerHeight()) / 2) + jQuery(window).scrollTop() + "px");
		this.css("left", ((jQuery(window).width() - this.outerWidth()) / 2) + jQuery(window).scrollLeft() + "px");
		return this;
	}
	
	function checkFileExtensionSilent(elem) 
	{
		var acceptedFileTypes = elem.getAttribute('acceptedFileList');
		var filePath = elem.value;
		if (filePath.indexOf('.') == -1) 
		{
			return acceptedFileTypes;
		}
		
		var validExtensions = new Array();
		var ext = filePath.substring(filePath.lastIndexOf('.') + 1).toLowerCase();
		
		var acceptedFileTypesSplit = acceptedFileTypes.split(';');
		for(var r = 0; r < acceptedFileTypesSplit.length; r++)
		{
			validExtensions[r] = acceptedFileTypesSplit[r].toLowerCase();
		}
		
		for(var i = 0; i < validExtensions.length; i++) 
		{
			if(ext == validExtensions[i])
			return '';
		}
		
		return acceptedFileTypes;
	}
	
	function checkFileExtension(elem) 
	{
		var acceptedFileTypes = elem.getAttribute('acceptedFileList');
		var filePath = elem.value;
		if (filePath.indexOf('.') == -1) 
		{
			alert('Invalid file, only the following files are allowed: ' + acceptedFileTypes);
			elem.value = "";
			return false;
		}
		
		var validExtensions = new Array();
		var ext = filePath.substring(filePath.lastIndexOf('.') + 1).toLowerCase();
		
		var acceptedFileTypesSplit = acceptedFileTypes.split(';');
		for(var r = 0; r < acceptedFileTypesSplit.length; r++)
		{
			validExtensions[r] = acceptedFileTypesSplit[r].toLowerCase();
		}
		
		for(var i = 0; i < validExtensions.length; i++) 
		{
			if(ext == validExtensions[i])
			return true;
		}
		
		alert('Invalid file, only the following files are allowed: ' + acceptedFileTypes);
		elem.value = "";
		elem.text = "";
		return false;
	}

	function validateURL(elem)
	{
		if (elem.value == "") return true;
		
		if (!isUrl(elem.value))
		{
			alert('The URL you have specified appears to be invalid.');
			elem.value = "";
			return false;	
		}
		return true;
	}
		
	function roundNumber(number,decimal_points) 
	{
		if(!decimal_points) return Math.round(number);
		if(number == 0) {
			var decimals = "";
			for(var i=0;i<decimal_points;i++) decimals += "0";
			return "0."+decimals;
		}
	
		var exponent = Math.pow(10,decimal_points);
		var num = Math.round((number * exponent)).toString();
		return num.slice(0,-1*decimal_points) + "." + num.slice(-1*decimal_points)
	}
	
	function validateTextValue(field)
	{	
		var isValidChar = true;
		var lwr = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ ';
		
		for (i=0; i < field.value.length; i++) 
		{
			if (lwr.indexOf(field.value.charAt(i),0) == -1) 
			{
				isValidChar =  false;
				break;
			}
		}
		if (!isValidChar)
			field.value = field.value.substring(0,field.value.length - 1); 
	}
	
	function validateNumericValue(field)
	{
		var re = /^-{0,1}\d*\.{0,1}\d+$/;
		//var re = /^[0-9]*$/;
		if (!re.test(field.value))
		{
			field.value = field.value.replace(/[^-{0,1}0-9.{0,1}]/g,"");
		}
	}
        
        function validateEmailAddress(field){  
            var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;  
            return emailPattern.test(field.value);  
        }  
	
	function escapeString(stringField)
	{
		stringField = replaceAll(stringField, "+", "");
		stringField = replaceAll(stringField, " ", "");
		stringField = replaceAll(stringField, "|", "");
		stringField = replaceAll(stringField, "#", "");
		stringField = replaceAll(stringField, "*", "");
		stringField = replaceAll(stringField, "_", "");
		stringField = replaceAll(stringField, "-", "");
		stringField = replaceAll(stringField, "~", "");
		stringField = replaceAll(stringField, "'", "");
		stringField = replaceAll(stringField, "`", "");
		
		return stringField;
	}
	
	function getPosition(e) 
	{
		e = e || window.event;
		var cursor = {x:0, y:0};
		if (e.pageX || e.pageY) {
			cursor.x = e.pageX;
			cursor.y = e.pageY;
		} 
		else {
			var de = document.documentElement;
			var b = document.body;
			cursor.x = e.clientX + 
				(de.scrollLeft || b.scrollLeft) - (de.clientLeft || 0);
			cursor.y = e.clientY + 
				(de.scrollTop || b.scrollTop) - (de.clientTop || 0);
		}
		
		return cursor;
	}
	
	function padDigits(n, totalDigits) 
    { 
        n = n.toString(); 
        var pd = ''; 
        if (totalDigits > n.length) 
        { 
            for (i=0; i < (totalDigits-n.length); i++) 
            { 
                pd += '0'; 
            } 
        } 
        return pd + n.toString(); 
    } 
	
	function isNumeric(sText)
	{
	   var ValidChars = "-0123456789.";
	   var IsNumber=true;
	   var Char;
	   
	   if (sText.length == 0) return false;
	 
	   for (i = 0; i < sText.length && IsNumber == true; i++) 
		  { 
		  Char = sText.charAt(i); 
		  if (ValidChars.indexOf(Char) == -1) 
			 {
			 IsNumber = false;
			 }
		  }
	   return !isNaN(sText);		   
	}
	
	// Replaces every instance of substring 'strFind' in 'strOrig' with 'strReplace'.
	function replaceAll(strOrig, strFind, strReplace) 
	{
		var intCount = strOrig.indexOf(strFind);
		while (intCount != -1) 
		{
			strOrig = replaceChars(strOrig, intCount, strFind.length, strReplace);
			intCount = strOrig.indexOf(strFind);
		}
		return strOrig;
	}
	
	//this function checks if there are any characters in 'strCheck', that are not in 'strValid'
	function IsValidChars(strCheck, strValid) 
	{
		var strChar = "";
	 
		for (var intCount = 0; intCount < strCheck.length; intCount++) 
		{
			strChar = strCheck.charAt(intCount);
			if (strValid.indexOf(strChar) == -1) 
			return false;
		}
		return true;
	}
	
	function replaceChars(strOrig, intPos, intNoChars, strReplace) 
	{
		if (intPos < 0) intPos = 0;
		if (intPos >= strOrig.length) intPos = strOrig.length - 1;
		if (intNoChars < 0) intNoChars = 0;
		if (intNoChars > strOrig.length) intNoChars = strOrig.length;
		return (strOrig.substring(0, intPos) + strReplace + strOrig.substring(intPos + intNoChars));
	}
	