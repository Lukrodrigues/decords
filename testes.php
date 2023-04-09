<?php
 
/* 
conexão com o banco de dados 
*/
$conexao = mysql_connect("localhost", "root", "") or die(mysql_error());
$db = mysql_select_db("decords", $conexao);
?>
 
<script language="JavaScript">
/* 
script para adicionar as tags 
*/
function addBalise(baliseOn,baliseOff) {
var mess = document.form_post.texto;
//Internet Explorer
if (document.selection) {
var str = document.selection.createRange().text;
mess.focus();
sel = document.selection.createRange();
sel.text = baliseOn + str + baliseOff;
document.form_post.texto.focus();
}
//Firefox
else if (mess.selectionStart || mess.selectionStart == "0") {
var startPos = mess.selectionStart;
var endPos = mess.selectionEnd;
var chaine = mess.value;
var str = chaine.substring( mess.selectionStart, mess.selectionEnd );
mess.value = chaine.substring(0, startPos) + baliseOn + str + baliseOff + chaine.substring(endPos, chaine.length);
mess.selectionStart = startPos + instext.length;
mess.selectionEnd = endPos + instext.length;
mess.focus();
} else {
mess.value += instext;
mess.focus();
}
}
</script>
 
<form name="form_post" method="POST" action="">
Texto
<input type="button" onclick="addBalise('[p]','[/p]')" value="parágrafo">
<input type="button" onclick="addBalise('[b]','[/b]')" value="negrito">
<input type="button" onclick="addBalise('[i]','[/i]')" value="itálico">
<input type="button" onclick="addBalise('[u]','[/u]')" value="sublinhado">
<input type="button" onclick="addBalise('[a]','[/a]')" value="url">
<br>
<textarea name='texto' cols='40' rows='10'></textarea>
<br>
<input type="submit">
</form>

 
<?php
/* 
início da inclusão do texto no banco de dados 
*/
// if($_GET['inserir']=="ok") {
 
/* 
pega o valor do textarea e tira as tags ( < > ) 
*/
$texto = isset($_POST['texto']) ? $_POST['texto'] : '';
$conteudo = isset($_POST['editor1']) ? $_POST['editor1'] : '';
// $texto = strip_tags($texto);
 
/* 
insere o valor na tabela 
*/
$inserir="INSERT into tutorial (editor1) values('$conteudo')";
mysql_query($inserir) or die(mysql_error());
echo "adicionado com sucesso";

?>
 
<?php
 
/* 
busca todos os valores da tabela 
*/
$selec = "SELECT * FROM tutorial";
$exec = mysql_query($selec, $conexao) or die(mysql_error());
?>
 
<?php
/*
mostra os valores já configurados com o BBCode 
*/
while($dados=mysql_fetch_array($exec)) {
echo BBCode($dados['editor1']);
echo "<br>";
}
?>
 
<?php
/* 
função do BBCode 
*/
function BBCode($text) {
// Replace any html brackets with HTML Entities to prevent executing HTML or script
// Don't use strip_tags here because it breaks [url] search by replacing & with amp
//$text=stripslashes($text);
//$text=htmlspecialchars($text);
 
// Convert new line chars to html <br /> tags
 
$text=preg_replace("/(\r\n|\n|\r)/", "<br>", $text);
 
// Set up the parameters for a URL search string
$URLSearchString = " a-zA-Z0-9\:\/\-\?\&\.\=\_\~\#\'";
// Set up the parameters for a MAIL search string
$MAILSearchString = $URLSearchString . " a-zA-Z0-9\.@";
 
// Perform URL Search
// [url]http://[/url]
$text = preg_replace("#\[url\]([\w]+?://[^ \"\n\r\t<]*?)\[/url\]#is", '<a href="$1" target="_blank">$1</a>', $text);
$text = preg_replace("#\[urltop\]([\w]+?://[^ \"\n\r\t<]*?)\[/urltop\]#is", '<a href="$1" target="_top">$1</a>', $text);
$text = preg_replace("#\[url\=([\w]+://[^ \"\n\r\t<]*)\]([\w]+://[^ \"\n\r\t<]*)\[/url\]#is", '<a href="$1" target="_blank">$2</a>', $text);
 
$text = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text);
$text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
 
// matches an email@domain type address at the start of a line, or after a space.
// Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
$text = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);
 
// Perform MAIL Search
$text = preg_replace("(\[mail\]([$MAILSearchString]*)\[/mail\])", '<a href="mailto:$1">$1</a>', $text);
$text = preg_replace("/\[mail\=([$MAILSearchString]*)\](.+?)\[\/mail\]/", '<a href="mailto:$1">$2</a>', $text);
 
//quote and code functions
$text = preg_replace("(\[quote\](.+?)\[\/quote])is",'<center><span class="quote"> Citando...<br>$1</span></center>',$text);
$text = preg_replace("(\[code\](.+?)\[\/code])is",'<center><span class="code">$1</span></center>',$text);
 
// Check for bold text
$text = preg_replace("(\[b](.+?)\[/b])is",'<b>$1</b>',$text);
 
// Check for Italics text
$text = preg_replace("(\[i\](.+?)\[\/i\])is",'<i>$1</i>',$text);
 
// Check for Underline text
$text = preg_replace("(\[u\](.+?)\[\/u\])is",'<u>$1</u>',$text);
 
// Check for strike-through text
$text = preg_replace("(\[s\](.+?)\[\/s\])is",'<span class="strikethrough">$1</span>',$text);
 
// Check for over-line text
$text = preg_replace("(\[o\](.+?)\[\/o\])is",'<span class="overline">$1</span>',$text);
 
// Check for colored text
$text = preg_replace("(\[color=(.+?)\](.+?)\[\/color\])is","<span style=\"color: $1\">$2</span>",$text);
 
// Check for sized text
$text = preg_replace("(\[size=(.+?)\](.+?)\[\/size\])is","<span style=\"font-size: $1px\">$2</span>",$text);
 
// Check for list text
$text = preg_replace("/\[list\](.+?)\[\/list\]/is", '<ul class="listbullet">$1</ul>' ,$text);
$text = preg_replace("/\[list=1\](.+?)\[\/list\]/is", '<ul class="listdecimal">$1</ul>' ,$text);
$text = preg_replace("/\[list=i\](.+?)\[\/list\]/s", '<ul class="listlowerroman">$1</ul>' ,$text);
$text = preg_replace("/\[list=I\](.+?)\[\/list\]/s", '<ul class="listupperroman">$1</ul>' ,$text);
$text = preg_replace("/\[list=a\](.+?)\[\/list\]/s", '<ul class="listloweralpha">$1</ul>' ,$text);
$text = preg_replace("/\[list=A\](.+?)\[\/list\]/s", '<ul class="listupperalpha">$1</ul>' ,$text);
$text = str_replace("[*]", "<li>", $text);
 
// Check for font change text
$text = preg_replace("(\[font=(.+?)\](.+?)\[\/font\])","<span style=\"font-family: $1;\">$2</span>",$text);
 
$text = preg_replace("/\[img\](.+?)\[\/img\]/", '<img src="$1">', $text);
 
return $text;
}
?>
 
<?php
/* 
fechamento da conexão 
*/
mysql_close($conexao);
?>