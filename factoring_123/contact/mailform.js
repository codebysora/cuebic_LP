<!--
	var conservationKey = "[resume]";
	var mustId = "(必須)";
	var construct = new Array("－","～");
	
	function sendmail(obj){
		var caution = "";
		var errorflag = 0;
		var must = mustId;
		var error_element_number = new Array();
		var email_address = "";
		var checkflag = new Object();
		
		for (var i = 0; i < obj.length; i++) {
			var elementType = obj.elements[i].type;
			var errortext = obj.elements[i].name.replace(must,"");
			var must_flag = obj.elements[i].name.indexOf(must,0);
			
			if(!checkflag[obj.elements[i].name]){
				// フォーマットチェック
				if(errortext == "メールアドレス"){
					email_address = obj.elements[i].value;
					
					// 必須入力指定 or 入力値あり
					if(must_flag > -1 || obj.elements[i].value != ""){
						chkMail = obj.elements[i].value;
						check = /.+@.+\..+/;
						
						if (!chkMail.match(check)){
							obj.elements[i].style.backgroundColor='#FFEEEE';
							obj.elements[i].style.color='#FF0000';
							error_element_number.push(i);
							caution = caution + "メールアドレスが正しくありません。\n";
							errorflag = 2;
						}
						else{
							obj.elements[i].style.backgroundColor='#FFFFFF';
							obj.elements[i].style.color='#000000';
						}
					}
				// 必須入力チェック
				} else if(must_flag > -1){
					if(elementType == "text" || elementType == "textarea"){
						if(obj.elements[i].value == ""){
							obj.elements[i].style.backgroundColor='#FFEEEE';
							error_element_number.push(i);
							caution = caution + errortext +"が未入力です。\n";
							errorflag = 1;
						}
						else{
							  if(obj.elements[i].value.match(/^[ 　\r\n\t]*$/)){
							  
								obj.elements[i].style.backgroundColor='#FFEEEE';
								error_element_number.push(i);
								caution = caution + errortext +"がスペースのみ入力されています。\n";
								errorflag = 1;
							  }else{
								obj.elements[i].style.backgroundColor='#FFFFFF';
							　}
						}
					}
					else if(elementType == "checkbox" || elementType == "radio"){
						if(obj.elements[obj.elements[i].name].length > 0){
							var checkbox_checked_count = 0;
							for(ii=0;ii<obj.elements[obj.elements[i].name].length;ii++){
								if(obj.elements[obj.elements[i].name][ii].checked)
									checkbox_checked_count++;
							}
							if(checkbox_checked_count < 1){
								caution = caution + errortext +"がチェックされていません。\n";
								error_element_number.push(i);
								errorflag = 1;
							}
						}
						else if(!obj.elements[i].checked){
							caution = caution + errortext +"がチェックされていません。\n";
							error_element_number.push(i);
							errorflag = 1;
						}
					}
					else if(elementType == "select-multiple" || elementType == "select-one"){
						if(obj.elements[i].selectedIndex > -1){
							var selectCnt = obj.elements[i].selectedIndex;
							if(obj.elements[i].options[selectCnt].value == ""){
								error_element_number.push(i);
								caution = caution + errortext +"が選択されていません。\n";
								errorflag = 1;
							}
						}
						else{
							error_element_number.push(i);
							caution = caution + errortext +"が選択されていません。\n";
							errorflag = 1;
						}
					}
				}
			}
			checkflag[obj.elements[i].name] = 1;
		}
		
		if(errorflag == 0){
				for(i=0;i<obj.length ;i++){
					// name属性の「必須」は除外した上で送信する
					obj.elements[i].name = obj.elements[i].name.replace(must,"");
					if(obj.elements[i].type == "submit"){
						obj.elements[i].disabled = true;
					}
				}
				return true;
/*
			if(confirm("送信してもよろしいですか？")){
				for(i=0;i<obj.length ;i++){
					// name属性の「必須」は除外した上で送信する
					obj.elements[i].name = obj.elements[i].name.replace(must,"");
					if(obj.elements[i].type == "submit"){
						obj.elements[i].disabled = true;
					}
				}
				return true;
			}
			else{
				return false;
			}
*/
		}
		else{
			//caution = "TYPE "+errorflag+" ERROR\n"+caution;
			caution = "■入力エラーがあります■\n\n"+caution;
			alert(caution);
			obj.elements[error_element_number[0]].focus();
			return false;
		}
	}
	function keepField(){
		var setValue = "";
		var obj = mfObj;
		var elementsList = new Array();
		for(i=0;i<obj.length;i++){
			if(obj.elements[i].type == "checkbox" || obj.elements[i].type == "radio"){
				if(obj.elements[i].checked)
					setValue += "1" + "&";
				else
					setValue += "0" + "&";
			}
			else if(obj.elements[i].type == "text" || obj.elements[i].type == "textarea"){
				setValue += escape(obj.elements[i].value) + "&";
			}
			else if(obj.elements[i].type == "select-multiple"){
				var selected_multiple = new Array();
				for(multiplect=0;multiplect<obj.elements[i].length;multiplect++){
					if(obj.elements[i].options[multiplect].selected)
						selected_multiple.push(multiplect);
				}
				setValue += selected_multiple.join(",") + "&";
			}
			else if(obj.elements[i].type == "select-one"){
				setValue += obj.elements[i].selectedIndex + "&";
			}
		}
		setValue = conservationKey + setValue + conservationKey;
		mfp_setCookie("mailform",setValue)
	}
	function mfp_setCookie(name,val){
		var current_dir = location.pathname;
		var current_dirs = new Array();
		current_dirs = current_dir.split("/");
		if(current_dirs[current_dirs.length-1] != ""){
			current_dirs[current_dirs.length-1] = "";
			current_dir = current_dirs.join("/");
		}
		document.cookie = name + "=" + val + "; path=" + current_dir + "; expires=";
	}
	function formatCharset(obj){
		var befor = new Array("ｶﾞ","ｷﾞ","ｸﾞ","ｹﾞ","ｺﾞ","ｻﾞ","ｼﾞ","ｽﾞ","ｾﾞ","ｿﾞ","ﾀﾞ","ﾁﾞ",
			"ﾂﾞ","ﾃﾞ","ﾄﾞ","ﾊﾞ","ﾋﾞ","ﾌﾞ","ﾍﾞ","ﾎﾞ","ﾊﾟ","ﾋﾟ","ﾌﾟ","ﾍﾟ","ﾎﾟ","ｦ","ｧ",
			"ｨ","ｩ","ｪ","ｫ","ｬ","ｭ","ｮ","ｯ","ｰ","ｱ","ｲ","ｳ","ｴ","ｵ","ｶ","ｷ","ｸ","ｹ",
			"ｺ","ｻ","ｼ","ｽ","ｾ","ｿ","ﾀ","ﾁ","ﾂ","ﾃ","ﾄ","ﾅ","ﾆ","ﾇ","ﾈ","ﾉ","ﾊ","ﾋ",
			"ﾌ","ﾍ","ﾎ","ﾏ","ﾐ","ﾑ","ﾒ","ﾓ","ﾔ","ﾕ","ﾖ","ﾗ","ﾘ","ﾙ","ﾚ","ﾛ","ﾜ","ﾝ",
			'Ａ','Ｂ','Ｃ','Ｄ','Ｅ','Ｆ','Ｇ','Ｈ','Ｉ','Ｊ','Ｋ','Ｌ','Ｍ','Ｎ','Ｏ','Ｐ','Ｑ','Ｒ','Ｓ','Ｔ','Ｕ','Ｖ','Ｗ','Ｘ','Ｙ','Ｚ','ａ','ｂ','ｃ','ｄ','ｅ','ｆ','ｇ','ｈ','ｉ','ｊ','ｋ','ｌ','ｍ','ｎ','ｏ','ｐ','ｑ','ｒ','ｓ','ｔ','ｕ','ｖ','ｗ','ｘ','ｙ','ｚ','＠','０','１','２','３','４','５','６','７','８','９','．',
			'①','②','③','④','⑤','⑥','⑦','⑧','⑨','⑩','Ⅰ','Ⅱ','Ⅲ','Ⅳ','Ⅴ','Ⅵ','Ⅶ','Ⅷ','Ⅸ','Ⅹ','㈱','㈲','－','～');
		var after = new Array("ガ","ギ","グ","ゲ","ゴ","ザ","ジ","ズ","ゼ","ゾ","ダ","ヂ",
			"ヅ","デ","ド","バ","ビ","ブ","ベ","ボ","パ","ピ","プ","ペ","ポ","ヲ","ァ",
			"ィ","ゥ","ェ","ォ","ャ","ュ","ョ","ッ","ー","ア","イ","ウ","エ","オ","カ",
			"キ","ク","ケ","コ","サ","シ","ス","セ","ソ","タ","チ","ツ","テ","ト","ナ",
			"ニ","ヌ","ネ","ノ","ハ","ヒ","フ","ヘ","ホ","マ","ミ","ム","メ","モ","ヤ",
			"ユ","ヨ","ラ","リ","ル","レ","ロ","ワ","ン",
			'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','@','0','1','2','3','4','5','6','7','8','9','.',
			'(1)','(2)','(3)','(4)','(5)','(6)','(7)','(8)','(9)','(10)','(1)','(2)','(3)','(4)','(5)','(6)','(7)','(8)','(9)','(10)','(株)','(有)','-','～');
		for(i=0;i<befor.length;i++){
			var temp = new Array();
			temp = obj.value.split(befor[i]);
			obj.value = temp.join(after[i]);
		}
		var temp = new Array();
		temp = obj.value.split("\n");
		for(i=0;i<temp.length;i++){
			if(temp[i].length > 64){
				var chars = new Array();
				chars = temp[i].split("");
				for(ii=63;ii<chars.length;ii+=63){
					chars[ii] += "\n";
				}
				temp[i] = chars.join("");
			}
		}
		obj.value = temp.join("\n");
	}
	function debug(){
		alert("this");
	}
//-->