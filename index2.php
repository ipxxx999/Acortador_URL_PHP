<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>


<!-- Acortador de URL PHP simple por luis

Website: 

-->

<head>
   <title>Acortador de URL PHP simple</title>
  
     <link href="style.css" rel="stylesheet" type="text/css" /> 
       
 <style>
.tooltip {
  position: relative;
  display: inline-block;
}

.tooltip .tooltiptext {
  visibility: hidden;
  width: 140px;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px;
  position: absolute;
  z-index: 1;
  bottom: 150%;
  left: 50%;
  margin-left: -75px;
  opacity: 0;
  transition: opacity 0.3s;
}

.tooltip .tooltiptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
}

.tooltip:hover .tooltiptext {
  visibility: visible;
  opacity: 1;
}
</style>
     

</head>
<body>

  <script>
function myFunction() {
  var copyText = document.getElementById("myInput");
  copyText.select();
  document.execCommand("copy");
  
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Copiado: " + copyText.value;
}

function outFunc() {
  var tooltip = document.getElementById("myTooltip");
  tooltip.innerHTML = "Copiar al portapapeles";
}
</script>


<?php 

$servername = 'localhost';
$username = 'root';
$password = 'xxxx'; // en localhost por defecto no hay contraseña
$dbname = 'urlcorto';
$base_url='http://10.0.8.4/test'; // es la URL de tu aplicación




if (isset($_GET['url']) && $_GET['url']!="")
{ 
$url=urldecode($_GET['url']);
if (filter_var($url, FILTER_VALIDATE_URL)) 
{
// Crea una conexión.
$conn = new mysqli($servername, $username, $password, $dbname);
// Verifica la conexión
if ($conn->connect_error) {
die("La conexión falló: " . $conn->connect_error);
} 
$slug=GetShortUrl($url);
$conn->close();

//echo $base_url.$slug;

?>
<center>
<td style="width: 500px; height: 22px;">Perfecto</p>
<?php
  
 echo 'Aquí está mi URL corto <a href="'; echo $base_url; echo"/"; echo $slug;
 echo '" target="_blank">'; echo '</a>: ';
  
  ?><input type="text" value="<?php echo $base_url; echo"/"; echo $slug; ?>" id="myInput">
  
  </p><div class="tooltip"><button onclick="myFunction()" onmouseout="outFunc()"><span class="tooltiptext" id="myTooltip" ></p>Copiar al portapapeles</span>Copiar URL</button></div></center>
 <?php

} 
else 
{
die("$url no es una URL válida");
}

}
else
{
?>
<center>
<h1>Pegue su URL aquí</h1>
<form>
<p><input style="width: 500px; height: 22px;" type="url" name="url" required /></p>
<p><input class="button" type="submit" /></p>
</form>
</center>
<?php
}


function GetShortUrl($url){
 global $conn;
 $query = "SELECT * FROM url_shorten WHERE url = '".$url."' "; 
 $result = $conn->query($query);
 if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
 return $row['short_code'];
} else {
$short_code = generateUniqueID();
$sql = "INSERT INTO url_shorten (url, short_code, hits)
VALUES ('".$url."', '".$short_code."', '0')";
if ($conn->query($sql) === TRUE) {
return $short_code;
} else { 
die("Ocurrió un error desconocido");
}
}
}



function generateUniqueID(){
 global $conn; 
 $token = substr(md5(uniqid(rand(), true)),0,5); // crea una identificación corta única de 5 dígitos. Puede maximizarlo, pero recuerde cambiar también el valor de .htaccess
 $query = "SELECT * FROM url_shorten WHERE short_code = '".$token."' ";
 $result = $conn->query($query); 
 if ($result->num_rows > 0) {
 generateUniqueID();
 } else {
 return $token;
 }
}


if(isset($_GET['redirect']) && $_GET['redirect']!="")
{ 
$slug=urldecode($_GET['redirect']);

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
// Verifica la conexión
if ($conn->connect_error) {
die("La conexión falló: " . $conn->connect_error);
}
$url= GetRedirectUrl($slug);
$conn->close();
header("location:".$url);
exit;
}


function GetRedirectUrl($slug){
 global $conn;
 $query = "SELECT * FROM url_shorten WHERE short_code = '".addslashes($slug)."' "; 
 $result = $conn->query($query);
 if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
// si aumento el hits
$hits=$row['hits']+1;
$sql = "update url_shorten set hits='".$hits."' where id='".$row['id']."' ";
$conn->query($sql);
return $row['url'];
}
else 
 { 
die("Enlace inválido!");
}
}

?>
  </body>
  
