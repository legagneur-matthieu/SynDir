# SynDir

SynDir est un programme de synchronisation de dossier écrit en PHP

## Prérequis

PHP 5.x, 7.x ou 8.x doit être installé et reconnu comme commande interne par votre système d'exploitation

Pour savoir si php est reconnu comme commande interne tapez "php -v" dans votre terminal</br>
Si la commande n'est pas reconnu renseignez vous sur les variables d’environnements (PATH) de votre système d'exploitation

## Configuration 

Ouvrez le fichier "config/SynDir.json"

"interval" définit tout les combien de temps (en seconde) la synchronisation doit s'effectuer</br>
laissez a -1 si vous désirez que la synchronisation ne s'effectue qu'une fois

"directories" est un tableau contenant les dossiers source et de destination a synchroniser </br>
exemple (avec un chemin windows et un chemin linux)
<pre>
"directories": [
    ["C:\\users\\username\\Documents", "D:\\Documents"],
    ["/home/username/Documents", "/media/username/HDD/Documents"]
]
</pre>
## Lancer la synchronisation

Une fois les prérequis et la configuration faite il suffit de lancer SynDir.bat (pour windows)</br>
ou SynDir.sh pour Linux

Vous pouvez renseigner ces fichier dans vos programmes a lancer au démarrage </br>
ou dans le planificateur de taches (Cron)

## SafeMode (synchronisation des suppression)
Par défaut le safemode est actif, </br>
cela signifie que si un fichier est supprimé dans un dossier source, </br>
il ne le sera pas dans le dossier de destination.</br>

Pour désactiver le safemode (et donc synchroniser les suppressions) :</br>
rendez vous dans "engine/SynDir.cli.php" et a la fin du fichier (ligne 160), remplacez</br>
"new SynDirCli($safemode = true);" </br>
par</br>
"new SynDirCli($safemode = false);"
