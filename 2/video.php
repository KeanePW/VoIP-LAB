<?php
# interno del citofono (comprensivo del dominio):
$citofono="803@test.snomlabo.local";
# URL delle immagini generate dalla camera:
$cam="http://192.168.100.10/image.jpg";

# invia l'eader che definisce il tipo di contenuto text/xml
header('Content-type: text/xml');

# intestazione XML
echo '<?xml version="1.0" encoding="UTF-8"?>'; 

# se la variabile GET clid corrisponde alla variabile $citofono
# crea l'applicazione XML
if ($citofono == $_GET['clid']) {
	# tipo di aplicazione "SnomIPPhoneImageFile"
	echo' <SnomIPPhoneImageFile track="no" state="relevant">';
	# coordinate che definiscono il posizionamento dell'immagine
	echo '<LocationX>0</LocationX>';
	echo '<LocationY>0</LocationY>';
	# l'indirizzo dell'immagine (la nostra cam IP)
	echo '<url>'$cam+'</url>';
	# tag che effettua l'auto reload dell'applicazione
	echo '<fetch mil="200">'+$_SERVER['PHP_SELF']+'</fetch>';
	# fine dell'applicazione
	echo '</SnomIPPhoneImageFile>';

# clid non corrisponde con il citofono
}else {
	echo "<exit/>";
}
?>
