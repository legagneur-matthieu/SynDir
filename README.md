# SynDir

SynDir est un programme de synchronisation de dossier écrit en PHP

## Prérequis

PHP 5.x, 7.x ou 8.x doit être installé et reconnu comme commande interne par votre système d'exploitation

Pour savoir si php est reconnu comme commande interne tapez "php -v" dans votre terminal
Si la commande n'est pas reconnu renseignez vous sur les variables d’environnements (PATH) de votre système d'exploitation

## configuration 

Ouvrez le fichier "config/SynDir.json"

"interval" définit tout les combien de temps (en seconde) la synchronisation doit s'effectuer
laissez a -1 si vous désirez que la synchronisation ne s'effectue qu'une fois

"directories" est un tableau contenant les dossiers source et de destination a synchroniser
exemple (avec un chemin windows et un chemin linux)
"directories": [
    ["C:\\users\\username\Documents", "D:\\Documents"],
    ["/home/username/Documents", "/media/username/HDD/Documents"]
]

## Lancer la synchronisation

Une fois les prérequis et la configuration faite il suffit de lancer SynDir.bat (pour windows)
ou SynDir.sh pour Linux

Vous pouvez renseigner ces fichier dans vos programmes a lancer au démarrage 
ou dans le planificateur de taches (Cron)

## SafeMode (synchronisation des suppression)
Par défaut le safemode est actif, 
cela signifie que si un fichier est supprimé dans un dossier source, 
il ne le sera pas dans le dossier de destination.

Pour désactiver le safemode (et donc synchroniser les suppressions) :
rendez vous dans "engine/SynDir.cli.php" et a la fin du fichier (ligne 159), remplacez
"new SynDirCli($safemode = true);" 
par
"new SynDirCli($safemode = false);"