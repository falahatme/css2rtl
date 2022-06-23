<?php

// CSS TO RTL
// BY in:@elyarxan (Mohammad Falahat)
// Github: falahatme
// WWW.FLHT.IR

function BreakCSS($css, $sub = false)
{

    $results = array();

    preg_match_all('/(?ims)([a-z0-9\s\,\/\^\.\:#_\-@*()\[\]"=]+)\{([^\}]*)\}/', $css, $matches);

    // if(!$sub) print_r($matches);

    foreach ($matches[0] as $i => $original)
        foreach (explode(';', $matches[2][$i]) as $attr)
            if (strlen(trim($attr)) > 0) // for missing semicolon on last element, which is legal
            {

                if (strpos($matches[1][$i], '@media') === false) {
                    list($name, $value) = explode(':', $attr);
                    $results[trim($matches[1][$i])][trim($name)] = trim($value);
                    // if there is a comment , we have to ignore comment!
                    if (strpos($value, '/*') === true)
                        $results[trim($matches[1][$i])][trim($name)] = trim(substr($value, 0, strpos($value, '/*')));
                } else
                    $results[trim($matches[1][$i])][] = BreakCSS($matches[2][$i] . '}', true);
            }
    return $results;
}


$newline = '
';


if (isset($_FILES['css'])) {

    foreach($_FILES['css']['name'] as $cssFile){

        @$postfix = array_pop(explode('.', $cssFile));
        if ($postfix != 'css' and !isset($_POST['editedValue'])) die("*{\r\ncontent: \"ERROR: File type is not CSS.\"\r\n}");

    }

    if(@$_POST['justecho'] == 'true'){
 
        $text = '';
        foreach($_FILES['css']['tmp_name'] as $tmpName)
        {
            $text .= file_get_contents($tmpName);
        }
        die($text);

    }


    $foundFonts = 0;

    $lookingFor = [
        'margin' => 'inline',
        'padding' => 'inline',
        'direction' => 'reverse',
        'text-align' => 'reverse',
        'float' => 'reverse',
        'font' => 'add',
        'font-family' => 'add',
        'margin-right' => 'margin-left',
        'margin-left' => 'margin-right',
        'padding-left' => 'padding-right',
        'padding-right' => 'padding-left',
        'border-left' => 'border-right',
        'border-right' => 'border-left',
        'left' => 'right',
        'right' => 'left',

    ];

    $fontStr = '@font-face {
        font-family: "Vazir";
        src: url("https://fdn.fontcdn.ir/Fonts/Vazir/ad3cd4cbda94aee8578c1b622b9002f9dfe345c05870eb375a02da853d08f072.woff2") format("woff2");
        font-weight: 100;
        font-style: normal;
   }
   @font-face {
        font-family: "Vazir";
        src: url("https://fdn.fontcdn.ir/Fonts/Vazir/046a76746039189feb148c360dfb82d07a1e3464d31a2078363587af6f5a1cfb.woff2") format("woff2");
        font-weight: 300;
        font-style: normal;
   }
   @font-face {
        font-family: "Vazir";
        src: url("https://fdn.fontcdn.ir/Fonts/Vazir/d783603a0dd07db6896ecd8a3460e2256a48dca62373a0478706a05490c1a2d8.woff2") format("woff2");
        font-weight: 400;
        font-style: normal;
   }
   @font-face {
        font-family: "Vazir";
        src: url("https://fdn.fontcdn.ir/Fonts/Vazir/fc6648da06acebfe96ad5a8f077f569c5c4dd75b77122951723ddfbfeb191728.woff2") format("woff2");
        font-weight: 500;
        font-style: normal;
   }
   @font-face {
        font-family: "Vazir";
        src: url("https://fdn.fontcdn.ir/Fonts/Vazir/3b2277e82a583c9f91de41aa9e198a14d7ef7f4ccd04828abdd623acd116a88b.woff2") format("woff2");
        font-weight: 700;
        font-style: normal;
   }
   @font-face {
        font-family: "Vazir";
        src: url("https://fdn.fontcdn.ir/Fonts/Vazir/71671477a4b62305fbd3ed4976a31c3c08520cf914656ece6b79543524a49132.woff2") format("woff2");
        font-weight: 900;
        font-style: normal;
   }

';

    $text = '';
    if(isset($_POST['editedValue']))
        $text = $_POST['editedValue'];
    else
        foreach($_FILES['css']['tmp_name'] as $tmpName)
        {
            $text .= file_get_contents($tmpName);
        }
    $text = preg_replace('!/\*.*?\*/!s', '', $text);
    $text = preg_replace('/\n\s*\n/', "\n", $text);
    $cssArray = BreakCSS(str_replace($newline, '', $text));
    //print_r($cssArray);

    $outputStr = "\r\n\r\n";

    foreach ($cssArray as $cssSelector => $cssItems) {
        $newSelector = [];


        if (strpos($cssSelector, '@media') !== false) {

            foreach ($cssItems as $cssItemKeyItem) {
                //@media is $cssSelector
                //.element is $cssSubItemsKey

                foreach ($cssItemKeyItem as $cssSubItemsKey =>  $cssSubItemsValues)
                    foreach ($cssSubItemsValues as $cssSubItemKey => $cssSubItemValue) {

                        if (array_key_exists($cssSubItemKey, $lookingFor)) {

                            switch ($lookingFor[$cssSubItemKey]) {


                                case 'inline':

                                    $important = false;
                                    if (strpos($cssSubItemValue, 'important') > 0) {
                                        $important = true;
                                        $cssSubItemValue = str_replace('!important', '', $cssSubItemValue);
                                    }

                                    $splittedCssSubItemValue = explode(' ', trim($cssSubItemValue));

                                    if (count($splittedCssSubItemValue) > 3) {

                                        $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] = $splittedCssSubItemValue[0] . ' ' . $splittedCssSubItemValue[3] . ' ' . $splittedCssSubItemValue[2] . ' ' . $splittedCssSubItemValue[1];

                                        if ($important)
                                            $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] .= ' !important';
                                    }

                                    break;


                                case 'reverse':

                                    if ("left" == trim($cssSubItemValue))
                                        $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] = "right";
                                    else if ("right" == trim($cssSubItemValue))
                                        $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] = "left";

                                    if ("ltr" == trim($cssSubItemValue))
                                        $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] = "rtl";
                                    else if ("rtl" == trim($cssSubItemValue))
                                        $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] = "ltr";

                                    break;


                                case 'add':

                                    $foundFonts++;
                                    $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] = "'Vazir', " . $cssSubItemValue;

                                    break;


                                default:

                                    if (!isset($newSelector[$cssSelector][$cssSubItemsKey])) {
                                        $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] = "initial";

                                        if (strpos($cssSubItemValue, 'important') > 0)
                                            $newSelector[$cssSelector][$cssSubItemsKey][$cssSubItemKey] .= " !important";
                                    }

                                    $newSelector[$cssSelector][$cssSubItemsKey][$lookingFor[$cssSubItemKey]] = $cssSubItemValue;

                                    break;
                            }

                            //$newSelector[$cssSelector][$cssSubItemsKey] = "";

                        }
                    }
            }
        } else {
            foreach ($cssItems as $cssItemKey => $cssItemValue) {

                if (array_key_exists($cssItemKey, $lookingFor)) {

                    //echo "<br>$cssItemKey : $cssItemValue : " . $lookingFor[$cssItemKey];

                    switch ($lookingFor[$cssItemKey]) {


                        case 'inline':

                            $important = false;
                            if (strpos($cssItemValue, 'important') > 0) {
                                $important = true;
                                $cssItemValue = str_replace('!important', '', $cssItemValue);
                            }

                            $splittedCssItemValue = explode(' ', trim($cssItemValue));

                            if (count($splittedCssItemValue) > 3) {

                                $newSelector[$cssSelector][$cssItemKey] = $splittedCssItemValue[0] . ' ' . $splittedCssItemValue[3] . ' ' . $splittedCssItemValue[2] . ' ' . $splittedCssItemValue[1];

                                if ($important)
                                    $newSelector[$cssSelector][$cssItemKey] .= ' !important';
                            }

                            break;


                        case 'reverse':

                            if ("left" == trim($cssItemValue))
                                $newSelector[$cssSelector][$cssItemKey] = "right";
                            else if ("right" == trim($cssItemValue))
                                $newSelector[$cssSelector][$cssItemKey] = "left";

                            if ("ltr" == trim($cssItemValue))
                                $newSelector[$cssSelector][$cssItemKey] = "rtl";
                            else if ("rtl" == trim($cssItemValue))
                                $newSelector[$cssSelector][$cssItemKey] = "ltr";

                            break;


                        case 'add':

                            $foundFonts++;
                            $newSelector[$cssSelector][$cssItemKey] = "'Vazir', " . $cssItemValue;

                            break;


                        default:

                            if (!isset($newSelector[$cssSelector][$cssItemKey])) {
                                $newSelector[$cssSelector][$cssItemKey] = "initial";

                                if (strpos($cssItemValue, 'important') > 0)
                                    $newSelector[$cssSelector][$cssItemKey] .= " !important";
                            }

                            $newSelector[$cssSelector][$lookingFor[$cssItemKey]] = $cssItemValue;

                            break;
                    }

                    //$newSelector[$cssSelector][$cssItemKey] = "";

                }
            }
        }


        if (isset($newSelector[$cssSelector])) {
            $outputStr .= "\r\n";

            $outputStr .= $cssSelector . "{\r\n";
            foreach ($newSelector[$cssSelector] as $cssItemKey => $cssItemValue) {

                if (is_array($cssItemValue)) {

                    $outputStr .= $cssItemKey . "{\r\n";

                    foreach ($cssItemValue as $cssSubItemKey => $cssSubItemValue) {
                        $outputStr .= '  ' . $cssSubItemKey . ": " . $cssSubItemValue . ";\r\n";
                    }

                    $outputStr .= "}\r\n";
                } else
                    $outputStr .= '  ' . $cssItemKey . ": " . $cssItemValue . ";\r\n";
            }
            $outputStr .= "}\r\n";


            $outputStr .= "\r\n";
        }
    }

    $outputStr = "\r\n *{direction: rtl; text-align: right;} \r\n" . $outputStr;

    if ($foundFonts > 0)
        $outputStr = $fontStr . $outputStr;

    /*ob_start();
    header("Content-Type: text/css");
    header("Content-Disposition: attachment; filename=rtl.css");
    header("Content-Length: " . strlen($outputStr));
    header("Content-Transfer-Encoding: binary");*/
    //readfile($path);
    echo $outputStr;
    //ob_end_flush();
} else {
    ?><!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
        <title>اسکریپت راست چین کردن استایل ها توسط محمد فلاحت</title>
        <link rel="stylesheet" href="./css/style.css">
    </head>

    <body>

		<div class="sidebar">
			<h1>رایگان و سریع فایل های CSS را راست چین کن!</h1>
			<h2>Margin &nbsp; Padding &nbsp; Border &nbsp; Direction &nbsp; Text-align &nbsp; Float</h2>

            <form action="index.php" method="post" enctype="multipart/form-data">
                <label for=cssFiles>
				<div class="dropzone">
					<h3 id="cssValue">Click To Select Multiple Files.</h3>
					<input type="file" class="d-none" id="cssFiles" name="css[]" multiple id="css" onchange="fileselected()" />
				</div>
                </label>
                <input type="button" value="DO LTR -> RTL" class="refresh disable" >
            </form>
		</div>

		<div class="container" id="container"></div>

		<div class="container" id="containerOutput"></div>

    </body>

	<script src="./monaco-editor/min/vs/loader.js"></script>
	<script src="jquery-3.6.0.min.js"></script>

	<script>

        var editorLTR = false;
        var editorRTL = false;

		require.config({ paths: { vs: './monaco-editor/min/vs' } });

		require(['vs/editor/editor.main'], function () {
			editorLTR = monaco.editor.create(document.getElementById('container'), {
				theme: 'vs-dark',
				value: getCode(false),
				language: 'css'
			});
            editorLTR.getModel().onDidChangeContent((event) => {
                enableRefresh();
            });
		});

		require(['vs/editor/editor.main'], function () {
			editorRTL = monaco.editor.create(document.getElementById('containerOutput'), {
				theme: 'vs-dark',
				value: getCode(),
				language: 'css'
			});
		});

		function getCode(output=true) {
			if(!output)
				return [
					'*{',
					'content: "کد استایل چپ چین را اینجا الصاق کن.\n یا میتوانی از جعبه سمت چپ ، \nاستایل ها را انتخاب کرده و منتظر پردازش باشی"',
					'}',
				].join('\n');
			else
				return [
					'*{',
					'content: "خروجی راست چین در اینجا نمایش \nداده خواهد شد"',
					'}',
				].join('\n');
		}
        

    var defaultCssText = document.getElementById('cssValue').innerHTML;

    function enableRefresh(){
        $('.refresh').removeClass('disable');
    }

    function disableRefresh(){
        $('.refresh').addClass('disable');
    }

    function updateRTL(EditedLTR){

        var data = new FormData($('form')[0]);
        data.append('editedValue', EditedLTR); 

        $.ajax({
            url: 'index.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            success: function(data){

                editorRTL.setValue(data)
                document.getElementById('cssValue').innerHTML = defaultCssText;
                disableRefresh()
                
            }
        });     

    }

    function fileselected(){
        var selectedFilesCount = document.getElementById('cssFiles').files.length;
        var OutputText = (selectedFilesCount==1) ? selectedFilesCount + ' file selected. processing ...' : selectedFilesCount + ' files selected. processing ...';
        document.getElementById('cssValue').innerHTML = OutputText;
        setTimeout(() => {
            $('form').submit();
        }, 200);
    }

    $('.refresh').on('click', function(){
        if($('.refresh').hasClass('disable'))
            return false;
        else
            updateRTL(editorLTR.getValue());
    })

    $( 'form' ).submit( function( e ) {
        var data = new FormData($('form')[0]);
        jQuery.each($('#cssFiles').files, function(i, file) {
            data.append('css[]', file);
        });
        $.ajax({
            url: 'index.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            success: function(data){

                editorRTL.setValue(data)
                
            }
        });        
            data.append('justecho', 'true'); 
        $.ajax({
            url: 'index.php',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            success: function(data){

                editorLTR.setValue(data)
                document.getElementById('cssValue').innerHTML = defaultCssText;
                disableRefresh()
                
            }
        });   
        e.preventDefault();
    });


    </script>

    </html>
<?php
}
?>