safemode_zip

Wrapper für zip, bzip und gzip mit aktiviertem php-safemode.


Das php-CLI-Script muss in safe_mode_exec_dir installiert 
sein und für den Apache-Account ausführbar.
Jedoch Keine Schreibrechte für den Apache-Account!

Empfehlung:

Owner root, Group root
Zugriffsrechte: 755 / rwxr-xr-x



In dem Script muss $basedir gesetzt werden - es können nur 
Dateien unterhalb dieses Verzeichnis gezippt und nur unterhalb 
dieses Verzeichnis abgelegt werden.



phpzip.php --help
--type   can be zip, gzip or bzip2
--src    relative* path to source file
--dst    relative* path to destination file

*relative to $basedir