<?php

/*
	Alle Zeichen anzeigen, die im IE nicht angezeigt werden können und deshalb
	gefiltert werden.
	
	Ursprünglicher Grund ist das XML-Interface, da beim parsen der XML-Datei
	fehler auftreten, wenn ungültige Zeichen enthalten sind.
*/

$evilchars = array(173 => 173, 160 => 160, 157 => 157, 144 => 144, 143 => 143, 
                   141 => 141, 129 => 129, 127 => 127, 31 => 31, 30 => 30, 
                   29 => 29, 28 => 28, 27 => 27, 26 => 26, 25 => 25, 24 => 24, 
                   23 => 23, 22 => 22, 21 => 21, 20 => 20, 19 => 19, 18 => 18, 
                   17 => 17, 16 => 16, 15 => 15, 14 => 14, 12 => 12, 11 => 11, 
                   9 => 9, 8 => 8, 7 => 7, 6 => 6, 5 => 5, 4 => 4, 3 => 3, 
                   2 => 2, 1 => 1, 0 => 0);
?>
<html>
<body>
<table>
	<tr>
		<td>Nr</td>
		<td>HTML</td>
		<td>pure</td>
		<td>ctype_print</td>
		<td>ctype_cntrl</td>
		<td>ctype_graph</td>
		<td>ctype_punct</td>
	</tr>
<?php
	for ($i = 0; $i <= 255; $i++)
		echo '<tr><td>' . $i . '</td><td>' . 
											htmlspecialchars(chr($i)) . '</td><td>' . 
											chr($i) . '</td><td>' . 
											(ctype_print(chr($i)) ? 'OK' : '&nbsp;') . '</td><td>' . 
											(ctype_cntrl(chr($i)) ? 'OK' : '&nbsp;') . '</td><td>' . 
											(ctype_graph(chr($i)) ? 'OK' : '&nbsp;') . '</td><td>' . 
											(ctype_punct(chr($i)) ? 'OK' : '&nbsp;') . 
											'</td></tr>' . "\n";
?>
</table>
</body>
</html>