<?php

/**
 * Cette classe gère la synchronisation des fichiers
 *
 * @author LEGAGNEUR Matthieu <legagneur.matthieu@gmail.com>
 */
class SynDirCli {

    /**
     * Liste des fichiers
     * @var array Liste des fichiers
     */
    private $_files = [];

    /**
     * Resultats et log
     * @var array Resultats et log
     */
    private $_stat = [
        "A" => 0, //Add
        "U" => 0, //Update
        "D" => 0, //Delete
        "E" => 0, //Error
        "log" => ""
    ];

    /**
     * Cette classe gère la synchronisation des fichiers
     * @param boolean $safemode si false : les ficher supprimé en source le seront aussi en destination
     */
    public function __construct($safemode = true) {
        include_once './cli.class.php';
        while (true) {
            $SynDir = json_decode(file_get_contents("../config/SynDir.json"), true);
            $this_files = [];
            $this_stat = [
                "A" => 0,
                "U" => 0,
                "D" => 0,
                "E" => 0,
                "log" => ""
            ];
            foreach ($SynDir["directories"] as $dirs) {
                cli::write("{$dirs[0]} -> {$dirs[1]}\n");
                cli::write("Parcours des dossiers :\n");
                $this->set_file_list($dirs);
                cli::rewrite("Ok");
                cli::write("Copies en cours...");
                $this->exec();
                cli::rewrite("Copies termiées");
                if (!$safemode) {
                    cli::write("Parcours de fichiers a supprimer...");
                    $this->sup($dirs);
                    cli::rewrite("Suppressions termiées");
                }
                $this->get_stat();
            }
            ($SynDir["interval"] >= 0 ? cli::wait($SynDir["interval"]) : exit());
        }
    }

    /**
     * Definit la liste des fichier a ajouter ou metre à jour
     * @param array $dirs ["SOURCE", "DESTINATION"]
     * @param string $sub Sous-dossier
     */
    private function set_file_list($dirs, $sub = "") {
        foreach (glob($dirs[0] . $sub . "/*") as $file) {
            $dest = strtr($file, [$dirs[0] => $dirs[1]]);
            if (is_dir($file)) {
                $this->set_file_list($dirs, strtr($file, [$dirs[0] => ""]));
                if (!file_exists($dest)) {
                    mkdir($dest, 0777, true);
                }
                cli::rewrite($file);
            } else {
                if (
                        ($add = !file_exists($dest)) or
                        filemtime($file) > filemtime($dest)
                ) {
                    $mode = ($add ? "A" : "U");
                    $this->_files[] = [$file, $dest, $mode];
                }
            }
        }
    }

    /**
     * Affiche le resultat de la synchronisation et créé le log
     */
    private function get_stat() {
        $log = "{$this->_stat["A"]} Fichiers ajouté\n"
                . "{$this->_stat["U"]} Fichier modifié\n";
        if ($this->_stat["D"] > 0) {
            $log .= "{$this->_stat["D"]} Fichiers ont été supprimé !\n";
        }
        $log .= "Total : " . ($this->_stat["A"] + $this->_stat["U"] + $this->_stat["D"]) . " Modifications\n";

        if ($this->_stat["E"] > 0) {
            $log .= "ATTENTION : {$this->_stat["E"]} Fichiers n'ont pu être copié !\n";
        }
        cli::write($log);
        $date = new DateTime();
        $datenow = $date->format($format = "YmdHis");
        $datem1m = $date->sub(new DateInterval("P1M"));
        file_put_contents("../logs/$datenow.log", $log . $this->_stat["log"]);
        foreach (glob("../logs/*.log") as $log) {
            if ((int) basename($log, ".log") < $datem1m->format($format)) {
                unlink($log);
            }
        }
    }

    /**
     * Execution de la synchronisation
     */
    private function exec() {
        $count = count($this->_files);
        foreach ($this->_files as $key => $file) {
            if (copy($file[0], $file[1])) {
                $cp = "Ok !";
                $this->_stat[$file[2]]++;
            } else {
                $cp = "ERROR !";
                $this->_stat["E"]++;
            }
            $this->_stat["log"] .= "$cp $file[2] $file[0] -> $file[1] \n";
            cli::rewrite("Copies en cours... " . ((int) (($key + 1) / $count * 100)) . "%");
        }
    }

    /**
     * Synchronise les fichier supprimé si $safemode = false
     * @param array $dirs ["SOURCE", "DESTINATION"]
     * @param string $sub Sous-dossier
     */
    private function sup($dirs, $sub = "") {
        foreach (glob($dirs[1] . $sub . "/*") as $dest) {
            $source = strtr($dest, [$dirs[1] => $dirs[0]]);
            if (is_dir($dest)) {
                $this->sup($dirs, strtr($dest, [$dirs[1] => ""]));
            }
            if (!file_exists($source)) {
                if (unlink($dest)) {
                    $this->_stat["D"]++;
                    $cp = "Ok !";
                } else {
                    $this->_stat["E"]++;
                    $cp = "ERROR !";
                }
                $this->_files[] = [$source, $dest, "D"];
                $this->_stat["log"] .= "$cp D $source -> $dest \n";
            }
        }
    }

}

new SynDirCli($safemode = true);
