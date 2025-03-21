<?php
namespace FancyDumpVar;

/**
 * FancyDumpVar
 *
 * Eine Utility-Klasse zur eleganten Ausgabe und Darstellung von Variablen.
 *
 * Version: 2.5.8
 * Datum: 2025-03-19
 *
 * Copyright (C) 2025 Johannes Teitge <johannes@teitge.de>
 *
 * Dieses Programm ist freie Software: Du kannst es unter den Bedingungen der
 * GNU General Public License, wie von der Free Software Foundation veröffentlicht,
 * entweder Version 3 der Lizenz oder (nach Deiner Wahl) jeder späteren Version,
 * weiterverbreiten und/oder modifizieren.
 *
 * Dieses Programm wird in der Hoffnung, dass es nützlich sein wird, aber OHNE
 * JEDE GEWÄHRLEISTUNG bereitgestellt; auch ohne die implizite Garantie der
 * MARKTREIFE oder der VERWENDBARKEIT FÜR EINEN BESTIMMTEN ZWECK.
 * Lies die GNU General Public License für weitere Details.
 *
 * Du solltest eine Kopie der GNU General Public License zusammen mit diesem Programm
 * erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.
 *
 * @package      FancyDumpVar
 * @author       Johannes Teitge
 * @email        johannes@teitge.de
 * @website      https://teitge.de
 * @license      GPL-3.0-or-later
 */





// Konfiguriere die Basis-URL der Anwendung (kann auch aus einer Konfigurationsdatei kommen)
function getBaseUrl() {
    // Überprüfe, ob HTTPS verwendet wird
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    
    // Erhalte den Hostnamen und den Pfad zum Root-Verzeichnis
    $host = $_SERVER['HTTP_HOST'];
    $rootPath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');  // Entfernt das abschließende / von SCRIPT_NAME
    
    // Erstelle die Basis-URL
    return $protocol . '://' . $host . $rootPath;
}

if (!function_exists('_t')) {
    /**
     * Globale Übersetzungsfunktion mit Fallback.
     *
     * @param string $key Der Schlüssel der Übersetzung.
     * @param string|null $default Fallback-Text, falls keine Übersetzung gefunden wird.
     * @param array $placeholders Platzhalter für dynamische Werte.
     * @return string Die übersetzte Zeichenkette oder der Fallback-Text.
     */
    function _t($key, $default = null, $placeholders = []) {
        return FancyDumpVar::translate($key, $default, $placeholders);
    }
}



/**
 * Stopwatch
 *
 * Diese Klasse implementiert eine einfache Stoppuhr, die es ermöglicht, Zeit zu messen,
 * zu starten, zu pausieren, fortzusetzen, zu stoppen und zurückzusetzen.
 */
class Stopwatch {
    
    // Die Startzeit der Stoppuhr
    private $startTime;
    
    // Die akkumulierte Zeit, die bereits vergangen ist
    private $elapsedTime;
    
    // Flag, ob die Stoppuhr läuft oder nicht
    private $running;

    /**
     * Konstruktor
     * 
     * Initialisiert die Stoppuhr, setzt die akkumulierte Zeit auf 0 und stellt sicher, 
     * dass die Stoppuhr zu Beginn nicht läuft.
     */
    public function __construct() {
        $this->elapsedTime = 0;  // Anfangszeit ist 0
        $this->running = false;  // Stoppuhr ist zu Beginn nicht aktiv
    }

    /**
     * Startet die Stoppuhr
     * 
     * Setzt die Startzeit auf die aktuelle Zeit und markiert die Stoppuhr als laufend.
     */
    public function start() {
        $this->startTime = microtime(true);  // Startzeit auf die aktuelle Zeit setzen (mit Mikrosekunden)
        $this->running = true;  // Stoppuhr läuft jetzt
    }

    /**
     * Pausiert die Stoppuhr
     * 
     * Wenn die Stoppuhr läuft, wird die vergangene Zeit zur akkumulierten Zeit hinzugefügt,
     * und die Stoppuhr wird gestoppt.
     */
    public function pause() {
        if ($this->running) {
            $this->elapsedTime += microtime(true) - $this->startTime;  // Zeit seit dem letzten Start zur akkumulierten Zeit hinzufügen
            $this->running = false;  // Stoppuhr ist jetzt gestoppt
        }
    }

    /**
     * Setzt die Stoppuhr fort
     * 
     * Wenn die Stoppuhr pausiert ist, wird die Startzeit auf die aktuelle Zeit gesetzt,
     * und die Stoppuhr wird fortgesetzt.
     */
    public function resume() {
        if (!$this->running) {
            $this->startTime = microtime(true);  // Setzt die Startzeit auf die aktuelle Zeit
            $this->running = true;  // Stoppuhr läuft wieder
        }
    }

    /**
     * Stoppt die Stoppuhr und gibt die gesamte vergangene Zeit zurück
     * 
     * Wenn die Stoppuhr läuft, wird die vergangene Zeit zur akkumulierten Zeit hinzugefügt,
     * und die Stoppuhr wird gestoppt. Gibt die gesamte vergangene Zeit (in Sekunden) zurück.
     * 
     * @return float Die gesamte vergangene Zeit in Sekunden.
     */
    public function stop() {
        if ($this->running) {
            $this->elapsedTime += microtime(true) - $this->startTime;  // Zeit zur akkumulierten Zeit hinzufügen
            $this->running = false;  // Stoppuhr gestoppt
        }
        return $this->elapsedTime;  // Gibt die gesamte verstrichene Zeit zurück
    }

    /**
     * Setzt die Stoppuhr zurück
     * 
     * Setzt die akkumulierte Zeit auf 0 zurück und stoppt die Stoppuhr.
     */
    public function clear() {
        $this->elapsedTime = 0;  // Akkumulierte Zeit zurücksetzen
        $this->running = false;  // Stoppuhr stoppen
    }

    /**
     * Gibt die aktuelle verstrichene Zeit zurück
     * 
     * Wenn die Stoppuhr läuft, wird die Zeit seit dem letzten Start zur akkumulierten Zeit hinzugefügt.
     * Gibt die verstrichene Zeit in Sekunden zurück.
     * 
     * @return float Die verstrichene Zeit in Sekunden.
     */
    public function getElapsedTime() {
        if ($this->running) {
            // Wenn die Stoppuhr läuft, fügt sie die Zeit seit dem letzten Start zur akkumulierten Zeit hinzu
            return $this->elapsedTime + (microtime(true) - $this->startTime);
        }
        return $this->elapsedTime;  // Wenn die Stoppuhr gestoppt ist, gibt sie nur die akkumulierte Zeit zurück
    }
}





class FancyDumpVar {

    // Stack zum Speichern der gedumpten Variablen
    protected static $stack = [];

    // Zähler für Dumps
    protected static $dumpCounter = 0; // Zähler für Dumps  

    // Array zur Objektverfolgung, um rekursive Strukturen zu vermeiden
    protected static $processedObjects = []; // Objekt-Tracking für rekursive Strukturen     

    // Flag, um Mehrfachladung von CSS & JS zu verhindern
    protected static $assetsLoaded = false; // Verhindert Mehrfachladung von CSS & JS   
    
    // Ein Stack, um die aktuell verarbeiteten Objekte in der Rekursion zu speichern
    // Dadurch können rekursive Objektreferenzen erkannt werden, sodass das Objekt
    // beim zweiten (und weiteren) Auftreten nicht erneut vollständig ausgegeben wird.
    protected static $currentStack = [];

    // Stoppuhr-Instanz
    protected static $stopwatch;    
	
    // Definiere die Standard-Sprache
    protected static $currentLanguage = 'en';

    // Definiere das Übersetzungs-Array
    protected static $translations = [];	

    // Array zum Speichern der TODO-Elemente
    // Feste TODO-Liste als Array
    protected static $todoArray = [
        [
            'title' => 'Parsing funktioniert noch nicht zu 100%',
            'priority' => 'Low',
            'timestamp' => '2025-03-14 12:30:00',
            'description' => 'Wenn man Werte anstatt Variablen übergibt, funktioniert das Parsing noch nicht korrekt.'
        ],
        [
            'title' => 'Template "VisualStudioStyle.css"',
            'priority' => 'Medium',
            'timestamp' => '2025-03-14 13:00:00',
            'description' => 'Das Template muss noch verbessert werden.'
        ],
        [
            'title' => 'Template "Monocrom.css"',
            'priority' => 'Medium',
            'timestamp' => '2025-03-14 13:00:00',
            'description' => 'Das Template muss noch verbessert werden.'
        ],  
        [
            'title' => 'maxElementsPerLevel',
            'priority' => 'Medium',
            'timestamp' => '2025-03-14 13:00:00',
            'description' => 'Dass muss noch umgesetzt werden, dass bis zum Limt alle Array-Elemente angezeigt werden'
        ],                
        [
            'title' => 'Templates für Wordpressmodul',
            'priority' => 'High',
            'timestamp' => '2025-03-14 14:00:00',
            'description' => 'Feature für das WP-Modul muss noch integriert werden. Optionen für das WP-Modul fehlen.'
        ],
        [
            'title' => 'Optionen',
            'priority' => 'High',
            'timestamp' => '2025-03-14 20:59:00',
            'description' => 'Optionen weiter ausbauen: Imagefiles'
        ], 
        [
            'title' => 'Optionen',
            'priority' => 'High',
            'timestamp' => '2025-03-14 20:59:00',
            'description' => 'Optionen weiter ausbaue: Mulitlanguage'
        ],                
        // Weitere TODO-Elemente können hier hinzugefügt werden
    ];
  
 


    // Statische Methode, die eine externe Klasse (Stopwatch) initialisiert
    public static function initStopwatch() {
        // Überprüfen, ob die Stoppuhr bereits instanziiert ist
        if (self::$stopwatch === null) {
            // Wenn nicht, dann instanziiere sie
            self::$stopwatch = new Stopwatch();
        }
    }    


    // Optionen als Array
    protected static $options = [
        'pluginBaseUrl' => '',      // Basis-URL für das Plugin
        'language' => 'en',         // Standard-Sprache
        'maxRecursionDepth' => 50,  // Maximal erlaubte Rekursionstiefe (kann angepasst werden)      
        'maxDepth' => 8,   
        'maxExecutionTime' => 5,    // Maximal erlaubte Ausführungszeit in Sekunden 
        'maxElementsPerLevel' => 10,
        'assetsNoCache' => false,   // Option zur Deaktivierung des Caching (Standard: false)   
        'sortPropertiesAndMethods' => false, // Flag, ob Properties und Methoden sortiert ausgegeben werden sollen
        'ShowTimeInfo' => false, 
        'TimeInfoFormat' => "h:i:s",    
        'DateInfoFormat' => "d.m.Y ", 
        'OverwriteStackVars' => true, 
        'Title' => '',
        'customCssFile' => '', 
        'helpUrl' => 'https://github.com/johannes-teitge/FancyDumpVar',

        'images' => [               // Beispiel für Bildoptionen
            'iconInfo' => 'path/to/icon_info.svg', 
            'iconError' => 'path/to/icon_error.svg'
        ],
    
		// Neue Optionen für den Wrapper und den Stil
		'dumpWrapper' => true,   // Flag, um den Wrapper zu aktivieren
		// 'dumpWrapperStyle' => 'border: 2px solid #ccc; padding: 10px;', // Standard CSS-Stil für den Wrapper
		'dumpWrapperStyle' => '', // Standard CSS-Stil für den Wrapper
		
    ];

    /**
     * Getter für eine Option
     *
     * @param string $key Der Schlüssel der Option
     * @return mixed Der Wert der Option
     */
    public static function getOption($key) {
        return self::$options[$key] ?? null;
    }

    /**
     * Setter für eine Option
     *
     * @param string $key Der Schlüssel der Option
     * @param mixed $value Der Wert der Option
     */
    public static function setOption($key, $value) {
        self::$options[$key] = $value;

        // Wenn die Option 'customCssFile' gesetzt wird, setze $assetsLoaded zurück
        if ($key === 'customCssFile') {
            self::$assetsLoaded = false; // Zurücksetzen der geladenen Assets 
        }
    }


    /**
     * Gibt alle aktuellen Optionen zurück.
     *
     * @return array Alle Optionen
     */
    public static function getOptions() {
        return self::$options;  // Gibt das gesamte Optionen-Array zurück
    }    




    /**
     * Lädt die Übersetzungen aus einer JSON-Datei oder PHP-Array.
     * Die Sprachdateien müssen im Format `translations/de.json` oder `translations/en.php` vorliegen.
     */
	public static function loadTranslations() {
		$language = self::getOption('language'); // Holt die Sprache aus den Optionen
		$filePathJson = __DIR__ . "/translations/{$language}.json";
		$filePathPhp  = __DIR__ . "/translations/{$language}.php";

		if (file_exists($filePathJson)) {
			self::$translations = json_decode(file_get_contents($filePathJson), true);
		} elseif (file_exists($filePathPhp)) {
			self::$translations = include $filePathPhp;
		} else {
			self::$translations = [];
		}
	}

    /**
     * Setzt die Sprache für Übersetzungen.
     * @param string $language Sprachcode (z.B. 'de', 'en', 'fr')
     */
    public static function setLanguage($language) {
        self::$currentLanguage = $language;
        self::loadTranslations($language);
    }

/**
 * Holt eine Übersetzung anhand eines Schlüssels.
 * Falls keine Übersetzung gefunden wird, wird der Fallback-Text oder der Schlüssel selbst zurückgegeben.
 *
 * @param string $key Der Schlüssel der Übersetzung.
 * @param string|null $default Fallback-Text, falls keine Übersetzung gefunden wird.
 * @param array $placeholders Platzhalter für dynamische Werte.
 * @return string Die übersetzte Zeichenkette oder der Fallback-Text.
 */
public static function translate($key, $default = null, $placeholders = []) {
    // Zugriff auf die Übersetzungen und Überprüfung, ob ein Eintrag existiert
    if (isset(self::$translations[$key])) {
        $translation = self::$translations[$key];
    } else {
        // Wenn kein Übersetzungseintrag existiert, prüfe, ob ein Default-Wert angegeben wurde
        if ($default !== null) {
            $translation = $default;
        } else {
            // Wenn kein Default-Wert angegeben wurde, verwende den Key als Fallback
            $translation = $key;
        }
    }

    // Überprüfung, ob der zurückgegebene Wert ein String ist
    if (!is_string($translation)) {
        error_log("⚠ Fehler: Übersetzung für '{$key}' ist kein String! Fallback wird genutzt.");
        $translation = is_string($default) ? $default : (is_string($key) ? $key : '[Übersetzungsfehler]');
    }

    // Stelle sicher, dass $placeholders ein Array ist
    if (!is_array($placeholders)) {
        error_log("⚠ Fehler: Platzhalter für '{$key}' sind kein Array.");
        $placeholders = [];
    }

    // Ersetze Platzhalter, falls vorhanden
    foreach ($placeholders as $placeholder => $value) {
        $translation = str_replace("{{$placeholder}}", $value, $translation);
    }

    return $translation;
}










    /**
     * Fügt ein neues TODO-Element hinzu.
     *
     * @param string $title Der Titel des TODOs.
     * @param string $priority Die Priorität (z.B. 'Low', 'Medium', 'High').
     * @param string $description Eine kurze Beschreibung des TODOs.
     */
    public static function addTodo($title, $priority = 'Low', $description = '') {
        // TODO-Array mit dem neuen Eintrag ergänzen
        self::$todoArray[] = [
            'title' => $title,
            'priority' => $priority,
            'timestamp' => time(),  // Aktueller Zeitstempel
            'description' => $description,
        ];
    }

    /**
     * Gibt das TODO-Array aus.
     *
     * @return array Das TODO-Array.
     */
    public static function getTodos() {
        return self::$todoArray;
    }

    /**
     * Gibt die TODO-Liste als formatierte Ausgabe zurück.
     *
     * @return string Formatierte Ausgabe der TODO-Liste.
     */
    public static function showTodos() {

        if (empty(self::$todoArray)) {
            return 'Keine offenen TODOs vorhanden.';
        }

        $output = '<ul>';
        foreach (self::$todoArray as $todo) {
            $output .= 

            '<h3 style="margin-bottom:0;padding-bottom:0">'. htmlspecialchars($todo['title']) . '</h3>'.
            '<p style="margin: 5px 0px 5px 0;padding-bottom:0">'.
            '<small><strong>Prio: </strong>'. htmlspecialchars($todo['priority']) .'<br>'. 
            '<strong>'._t('Timestamp').': </strong>'. htmlspecialchars($todo['timestamp']) .'</small></p><p style="margin: 5px 0px 5px 0;padding-bottom:0">'.  
            htmlspecialchars($todo['description']) . '</p>';          


        }
        $output .= '</ul>';

        return $output;
    }



    /**
     * Holt die Basis-URL für das Plugin
     * Wenn keine Basis-URL in den Optionen gesetzt ist, wird sie dynamisch ermittelt
     *
     * @return string Die Basis-URL
     */
    public static function getBaseUrl() {
        // Hole die Basis-URL aus den Optionen, falls gesetzt
        $baseUrl = self::getOption('pluginBaseUrl');

        // Falls keine URL in den Optionen gesetzt ist, ermitteln wir sie dynamisch
        if (!$baseUrl) {
            // Überprüfe, ob HTTPS verwendet wird
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    
            // Erhalte den Hostnamen und den Pfad zum Root-Verzeichnis
            $host = $_SERVER['HTTP_HOST'];
            $rootPath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');  // Entfernt das abschließende / von SCRIPT_NAME
    
            // Erstelle die Basis-URL und speichere sie in den Optionen, falls sie nicht gesetzt wurde
            $baseUrl = $protocol . '://' . $host . $rootPath;
            self::setOption('pluginBaseUrl', $baseUrl);
        }

        return $baseUrl;
    }



	/**
	 * Berechnet die Größe einer Variablen in Bytes (serialisiert)
	 * Verhindert Fehler, die durch nicht serialisierbare oder anonyme Variablen entstehen.
	 *
	 * @param mixed $var Die zu überprüfende und zu serialisierende Variable.
	 * @return int Die Größe der serialisierten Variable in Bytes.
	 */
	public static function calculateSize($var) {
		try {
			// Wenn es sich um ein Objekt handelt, prüfe, ob es serialisierbar ist
			if (is_object($var)) {
				// Überprüfe, ob das Objekt eine __serialize-Methode hat
				if (method_exists($var, '__serialize')) {
					// Benutze die benutzerdefinierte Serialize-Methode
					$serialized = serialize($var);
				} else {
					// Benutze den Standard-Serialisierungsmechanismus, falls __serialize nicht existiert
					$serialized = serialize($var);
				}
				return strlen($serialized);  // Gibt die Größe der serialisierten Variable in Bytes zurück
			}

			// Wenn es kein Objekt ist, versuche die Variable zu serialisieren
			$serialized = serialize($var);
			return strlen($serialized);  // Gibt die Größe der serialisierten Variable in Bytes zurück
		} catch (Exception $e) {
			// Falls ein Fehler beim Serialisieren auftritt, gib eine Fehlermeldung aus
			error_log("Fehler beim Serialisieren der Variable: " . $e->getMessage());
			return -1; // Setze die Größe auf 0, wenn sie nicht serialisierbar ist
		}
	}









    /**
     * Konstruktor zur Initialisierung von Sortierung, Überschreiben und Zeitinfo-Anzeige.
     *
     * @param bool $sort Flag, ob sortiert werden soll.
     * @param bool $overwrite Flag, ob existierende Variablen überschrieben werden sollen.
     * @param bool $showtime Flag, ob Zeitinformationen angezeigt werden sollen.
     */
    public function __construct() {
   
        // Stoppuhr instanziieren, wenn sie noch nicht existiert
        if (self::$stopwatch === null) {
            self::$stopwatch = new Stopwatch();
        }    
        
        // Wenn wir uns in einer WordPress-Umgebung befinden, Assets nicht direkt ausgeben.
        if ( defined('ABSPATH') && function_exists('add_action') ) {
            self::$assetsLoaded = true;
        }        
    }


    /**
     * Getter für die Stoppuhr-Instanz
     *
     * @return Stopwatch Gibt die Stoppuhr-Instanz zurück
     */
    public static function getStopwatch() {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        return self::$stopwatch;
    }

    /**
     * Setter für die Stoppuhr-Instanz
     * 
     * Diese Methode ermöglicht das Setzen einer neuen Stoppuhr-Instanz, falls notwendig.
     *
     * @param Stopwatch $stopwatch Die zu setzende Stoppuhr-Instanz
     */
    public static function setStopwatch(Stopwatch $stopwatch) {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        self::$stopwatch = $stopwatch;
    }

    /**
     * Startet die Stoppuhr
     */
    public static function startStopwatch() {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        self::$stopwatch->start();
    }

    /**
     * Pausiert die Stoppuhr
     */
    public static function pauseStopwatch() {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        self::$stopwatch->pause();
    }

    /**
     * Setzt die Stoppuhr fort
     */
    public static function resumeStopwatch() {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        self::$stopwatch->resume();
    }

    /**
     * Stoppt die Stoppuhr und gibt die verstrichene Zeit zurück
     * 
     * @return float Die verstrichene Zeit in Sekunden
     */
    public static function stopStopwatch() {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        return self::$stopwatch->stop();
    }

    /**
     * Setzt die Stoppuhr zurück
     */
    public static function clearStopwatch() {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        self::$stopwatch->clear();
    }

    /**
     * Gibt die aktuelle verstrichene Zeit zurück
     * 
     * @return float Die verstrichene Zeit in Sekunden
     */
    public static function getElapsedTime() {
        self::initStopwatch();  // Stelle sicher, dass die Stoppuhr initialisiert ist        
        return self::$stopwatch->getElapsedTime();
    }

    
    /**
     * Sucht im aktuellen Rekursions-Stack nach einem Eintrag mit der angegebenen Objekt-ID.
     *
     * @param int $objectId Die Objekt-ID, nach der im aktuellen Rekursions-Stack gesucht wird.
     * @return array|null Gibt ein Array mit den Schlüsseln 'id' und 'class' zurück, wenn ein entsprechender Eintrag gefunden wurde; andernfalls null.
     */
    public static function findInStack($objectId) {
        foreach (self::$currentStack as $info) {
            if ($info['id'] === $objectId) {
                return $info;
            }
        }
        return null;
    }




    /**
     * Gibt die Anzahl der im Stack befindlichen Variablen zurück.
     *
     * @return int
     */
    public static function getStackCount(): int {
        return count(self::$stack);
    }    


    /**
     * Schreibt den gesamten Stack in das PHP Errorlog.
     */
    public static function logStackToErrorLog() {
        // Wir konvertieren den Stack in ein lesbares Format
        $stackLog = '';

        foreach (self::$stack as $entry) {
            $stackLog .= "Name: " . $entry['name'] . "\n";
            $stackLog .= "Timestamp: " . date('Y-m-d H:i:s', $entry['timestamp']) . "\n";
            $stackLog .= "Elapsed Time: " . $entry['elapsedTime'] . " seconds\n";
            $stackLog .= "Size: " . $entry['size'] . " bytes\n";
            $stackLog .= "Element Count: " . $entry['elementCount'] . "\n";
            $stackLog .= "Type: " . ($entry['type'] ? $entry['type'] : 'N/A') . "\n";
            $stackLog .= "------------------------------------\n";
        }

        // Schreibe den Stack ins PHP Errorlog
        error_log($stackLog);
    }


    /**
     * Zählt die Anzahl der Elemente in einem Array oder Objekt,
     * auch in verschachtelten Strukturen.
     *
     * @param mixed $var Die zu zählende Variable (Array oder Objekt).
     * @param int $depth Die aktuelle Rekursionstiefe (für Rekursionstiefe-Limitierung).
     * @param float $startTime Der Startzeitpunkt (für Timeout-Überprüfung).
     * @return int Die Anzahl der Elemente.
     */
    public static function countElements($var, $depth = 0, $startTime = null) {
        if ($startTime === null) {
            $startTime = microtime(true);  // Speichert den Startzeitpunkt
        }

        $maxExecutionTime = self::getOption('maxExecutionTime');

        // Überprüfen, ob die Ausführungszeit das Limit überschreitet
        if (microtime(true) - $startTime > $maxExecutionTime ) {
            return -2;  // Timeout erreicht, gebe 0 zurück oder eine Fehlerzahl
        } 

        // Wenn die Rekursionstiefe das Limit erreicht, abbrechen
        $maxRecursionDepth = self::getOption('maxRecursionDepth');
        if ($depth > $maxRecursionDepth) {
            return -1;  // Rückgabe einer Standardanzahl oder Fehler, wenn die maximale Tiefe überschritten wird
        }

        // Wenn es ein Array ist, zählen wir die Elemente und rekursiv die verschachtelten
        if (is_array($var)) {
            $count = count($var);  // Zählt die Elemente des Arrays
            foreach ($var as $element) {
                // Rekursive Zählung für jedes Element (mit inkrementierter Tiefe)
                $count += self::countElements($element, $depth + 1, $startTime);
            }
            return $count;
        }
        
        // Wenn es ein Objekt ist, zählen wir die Eigenschaften des Objekts
        if (is_object($var)) {
            return count(get_object_vars($var));  // Zählt die öffentlichen Eigenschaften des Objekts
        }

        return 1;  // Für einfache Typen (z. B. Strings, Integer) wird 1 gezählt
    }



	/**
	 * Überprüft den Variablennamen und stellt sicher, dass er korrekt ist.
	 * Wenn der Name mit '$' beginnt, wird er unverändert zurückgegeben,
	 * andernfalls wird ein alternativer Name generiert.
	 *
	 * @param string $varName Der Variablenname
	 * @return string Der bereinigte und überprüfte Variablenname
	 */
	protected static function checkVarName($varName) {
		// Prüfen, ob der Variablenname mit einem '$' beginnt
		if (strpos($varName, '$') === 0) {
			// Wenn der Name mit '$' beginnt, gebe ihn unverändert zurück
			return $varName;
		}


		// Prüfen, ob der Name eine Konstante ist
		if (defined($varName)) {
			return $varName; // Wenn es eine Konstante ist, wird der Name unverändert zurückgegeben
		}    

		// Wenn der Name nicht mit '$' beginnt, erstelle einen alternativen Namen
		return 'Wert_' . (count(self::$stack) + 1); // Fallback-Name
	}


    /**
     * Ermittelt eine eindeutige Identifikationsnummer für verschiedene Datentypen.
     *
     * @param mixed $var Die Variable, für die eine Identifikationsnummer benötigt wird.
     * @return string Die eindeutige Identifikationsnummer basierend auf dem Typ und Inhalt der Variable.
     *                Für Objekte wird die spl_object_id verwendet, für Arrays ein md5-Hash ihrer Serialisierung,
     *                für Ressourcen ihre numerische ID und für primitive Typen die ID eines temporären Objekts.
     */
	public static function getMemoryIdentifier($var) {
		if (is_object($var)) {
			return 'OBJ-' . spl_object_id($var);  // Eindeutige ID für Objekte
		} elseif (is_array($var)) {
			return 'ARR-' . md5(serialize($var)); // Hash für Arrays, um Veränderungen zu erkennen
		} elseif (is_resource($var)) {
			return 'RES-' . intval($var); // Ressourcen haben eine numerische ID
		} else {
			// Trick für Primitive: Ein Objekt erstellen, die ID davon nehmen
			return 'PRIM-' . spl_object_id((object)$var);
		}
	}


    /**
     * Fügt Variablen zum Stack hinzu und überschreibt sie ggf.
     *
     * @param mixed ...$vars Beliebige Anzahl an Variablen
     */
    public static function dump(...$vars) {

        // Holt die tatsächlichen Variablennamen aus dem aufrufenden Code
        $varNames = self::getVariableNames();

        foreach ($vars as $index => $var) {
            // Bereinige den Variablennamen

       //     echo trim($varNames[$index]).'<br>';

            $varName = isset($varNames[$index]) ? trim($varNames[$index]) : 'Variable_' . (count(self::$stack) + 1);
        //    $varName = preg_replace('/[^a-zA-Z0-9_]/', '_', $varName); // Entferne Sonderzeichen

            $varName = self::checkVarName($varName);
			
			// Prüft, ob es sich um ein Objekt handelt und holt eine eindeutige ID bzw. "Speicheradresse"
			$memoryAddress = self::getMemoryIdentifier($var);		
        
            // Rest des Codes bleibt gleich
            $updated = false;
        
            $elapsedTime = self::getElapsedTime();

            $varType = gettype($var);         
       //     $varType = '';
			
            $currentVersion = 1;  // Standard-Version für neue Variablen

			// Überprüfe, ob es bereits Versionen dieser Variable gibt
			$existingVersions = array_filter(self::$stack, function ($entry) use ($varName) {
				return $entry['name'] === $varName;
			});

			if (!empty($existingVersions)) {
				// Die höchste vorhandene Version suchen
                $existingVersionNumbers = array_column($existingVersions, 'version');
				$maxVersion = !empty($existingVersionNumbers) ? max($existingVersionNumbers) : 0;
				$currentVersion = $maxVersion + 1; // Neue Version +1
			}			
        
			$history = [];  // Speichert alte Werte nur für Variablen
			foreach (self::$stack as &$entry) {
				if ($entry['name'] === $varName) {
					$currentVersion = max($currentVersion, $entry['version'] + 1);
					// Falls es sich um eine Variable handelt (kein Objekt), speichere alten Wert
					if (!is_object($var) && isset($entry['data'])) {
						$history = $entry['history'] ?? [];
						$history[] = [
							'version' => $entry['version'],
							'data' => $entry['data'],
							'timestamp' => $entry['timestamp'],
						];
					}
				}
			}		
		
		
            // Logik für das Hinzufügen der Variablen zum Stack
            if ( self::getOption('OverwriteStackVars') ) {
                foreach (self::$stack as &$entry) {
                    if ($entry['name'] === $varName) {
                        $entry['data'] = $var;
                        $entry['timestamp'] = microtime(true);
                        $entry['elapsedTime'] = $elapsedTime;
                        $entry['varType'] = $varType;                        
                        $entry['type'] = '';
                        $entry['size'] = self::calculateSize($var);
                        $entry['elementCount'] = self::countElements($var);
                        $entry['memoryAddress'] = $memoryAddress;	
						$entry['version'] += 1; // Version um 1 erhöhen                                      
                        $entry['history'] = $history; // Setze die aktualisierte Historie						
                        $updated = true;
                        break;
                    }
                }
            }
        
            if (!$updated) {
                self::$stack[] = [
                    'name' => $varName,
                    'data' => $var,
                    'timestamp' => microtime(true),
                    'elapsedTime' => $elapsedTime,
                    'varType' => $varType,
                    'type' => '',                    
                    'size' => self::calculateSize($var),
                    'elementCount' => self::countElements($var),
                    'memoryAddress' => $memoryAddress,
                    'version' => $currentVersion, // Korrekte Version setzen                                                              
					'history' => $history, // Nur für Variablen
                ];
            }
        }
        

    }
    
	
	
	
public static function dump_(...$vars) {
    $varNames = self::getVariableNames();

    foreach ($vars as $index => $var) {
        $varName = isset($varNames[$index]) ? trim($varNames[$index]) : 'Variable_' . (count(self::$stack) + 1);
        $varName = self::checkVarName($varName);

        $memoryAddress = is_object($var) ? spl_object_id($var) : null;

        // Zusätzliche Infos für Objekte
        $varType = gettype($var);
        $varSize = self::calculateSize($var);
        $elementCount = self::countElements($var);
        $isObject = is_object($var);
        $isArray = is_array($var);
        $isCallable = is_callable($var);
        $isResource = is_resource($var);
        $resourceType = $isResource ? get_resource_type($var) : null;
        $varClass = $isObject ? get_class($var) : null;
        $varMethods = $isObject ? get_class_methods($var) : null;
        $varProperties = $isObject ? get_object_vars($var) : null;
        $varParents = $isObject ? class_parents($var) : null;
        $varInterfaces = $isObject ? class_implements($var) : null;
        $varTraits = $isObject ? class_uses($var) : null;
        $declaringFile = $isObject ? (new \ReflectionClass($var))->getFileName() : null;

        $updated = false;
        $elapsedTime = self::getElapsedTime();

        if (self::getOption('OverwriteStackVar')) {
            foreach (self::$stack as &$entry) {
                if ($entry['name'] === $varName) {
                    $entry['data'] = $var;
                    $entry['timestamp'] = time();
                    $entry['elapsedTime'] = $elapsedTime;
                    $entry['type'] = $varType;
                    $entry['size'] = $varSize;
                    $entry['elementCount'] = $elementCount;
                    $entry['memoryAddress'] = $memoryAddress;
                    $entry['isObject'] = $isObject;
                    $entry['isArray'] = $isArray;
                    $entry['isCallable'] = $isCallable;
                    $entry['isResource'] = $isResource;
                    $entry['resourceType'] = $resourceType;
                    $entry['varClass'] = $varClass;
                    $entry['varMethods'] = $varMethods;
                    $entry['varProperties'] = $varProperties;
                    $entry['varParents'] = $varParents;
                    $entry['varInterfaces'] = $varInterfaces;
                    $entry['varTraits'] = $varTraits;
                    $entry['declaringFile'] = $declaringFile;
                    $updated = true;
                    break;
                }
            }
        }

        if (!$updated) {
            self::$stack[] = [
                'name' => $varName,
                'data' => $var,
                'timestamp' => time(),
                'elapsedTime' => $elapsedTime,
                'type' => $varType,
                'size' => $varSize,
                'elementCount' => $elementCount,
                'memoryAddress' => $memoryAddress,
                'isObject' => $isObject,
                'isArray' => $isArray,
                'isCallable' => $isCallable,
                'isResource' => $isResource,
                'resourceType' => $resourceType,
                'varClass' => $varClass,
                'varMethods' => $varMethods,
                'varProperties' => $varProperties,
                'varParents' => $varParents,
                'varInterfaces' => $varInterfaces,
                'varTraits' => $varTraits,
                'declaringFile' => $declaringFile,
            ];
        }
    }
}
	
	
	
	

    /**
     * Fügt einen Infotext zum Stack hinzu.
     *
     * @param string $text Der Infotext, der hinzugefügt werden soll.
     * @param string $class Optional: Die Klasse des Infotexts (z.B. 'InfoMessage', 'ErrorMessage', etc.).
     */
    public static function addInfoText($text, $class = 'InfoMessage') {
        // Erstelle den Infotext-Stack-Eintrag
        self::$stack[] = [
            'name' => 'Info_' . count(self::$stack),  // Einzigartiger Name für den Infotext
            'data' => $text,  // Der eigentliche Text des Infotexts
            'timestamp' => time(),  // Aktueller Zeitstempel
            'type' => 'infotext',  // Typ setzen auf 'infotext'
            'class' => $class,  // Klasse für den Infotext
        ];
    }


    /**
     * Fügt Zeitinformationen zum Stack hinzu.
     * Diese Methode gibt die gestoppte Zeit (verstrichene Zeit) an, die durch die Stoppuhr erfasst wurde.
     */
    public static function addTimeInfo() {
        // Holen der aktuellen gestoppten Zeit
        $elapsedTime = self::$stopwatch->getElapsedTime();

        // Erstelle den Zeit-Stack-Eintrag
        self::$stack[] = [
            'name' => 'TimeInfo_' . count(self::$stack),  // Einzigartiger Name für die Zeitinfo
            'data' => "Elapsed Time: " . number_format($elapsedTime, 4) . " seconds",  // Formatiertes Zeitformat
            'timestamp' => time(),  // Aktueller Zeitstempel
            'type' => 'timeinfo',  // Typ setzen auf 'timeinfo'
            'elapsedTime' => $elapsedTime,  // Die gestoppte Zeit
        ];
    }


    /**
     * Leert den gesamten Stack.
     * Diese Methode entfernt alle Variablen aus dem Stack, um den Speicher freizugeben.
     */
    public static function clearStack() {
        self::$stack = [];  // Leert den Stack
        self::$currentStack = [];  // Leert den Rekursions-Stack (für rekursive Strukturen)
        self::$processedObjects = [];  // Leert das Array für das Objekt-Tracking
        self::$dumpCounter = 0;  // Setzt den Zähler für die Dumps zurück
    }


	// Berechnet die Versionsnummer basierend auf der Option `assetsNoCache`
	protected static function getAssetVersion($filePath) {
		// Überprüfe die `assetsNoCache`-Option
		if (self::getOption('assetsNoCache')) {
			return round(microtime(true) * 1000); // Aktuelle Zeit in Millisekunden als Version
		}

		// Fallback: Wenn `assetsNoCache` nicht aktiviert ist, verwenden wir die `filemtime()`
		return file_exists($filePath) ? filemtime($filePath) : round(microtime(true) * 1000);
	}    


    /**
     * Gibt die gedumpten Variablen formatiert aus.
     * Optional können bestimmte Variablen ausgewählt werden.
     *
     * @param mixed ...$selectedVars
     */
    public static function dumpOut(...$selectedVars) {
		
		// Prüfe, ob die Übersetzungen bereits geladen wurden
		if (empty(self::$translations)) {
			self::loadTranslations(); // Lade die Sprachdateien nur, wenn sie nicht schon geladen sind
		}		

        // Erhöhe den Dump-Zähler und erzeuge eindeutige IDs für diesen Dump
        self::$dumpCounter++; // Zähler hochzählen

        // Erzeuge eine eindeutige ID basierend auf der aktuellen Zeit in Millisekunden
        $currentTimeInMilliseconds = round(microtime(true) * 1000);  // Aktuelle Zeit in Millisekunden
        $uniqueId = $currentTimeInMilliseconds . mt_rand(1000, 9999); // Füge einen Zufallswert hinzu, um Kollisionen zu vermeiden        

        $ID = self::$dumpCounter; // Eindeutige ID für jeden Dump      
        $ID = $uniqueId;  
        $dumpId = 'dump-' .$ID; // Eindeutige ID für jeden Dump
        $wrapperId = 'dump-wrapper-' . $ID; // Eindeutige ID für jeden Wrapper
        // Setzt das Objekt-Tracking zurück, um rekursive Strukturen erneut zu verarbeiten
        self::$processedObjects = []; // Objekt-Tracking zurücksetzen


        // Basis-URL dynamisch ermitteln
        $baseUrl = self::getBaseUrl();  // Hier verwenden wir self:: statt FancyDumpVar::        


        // CSS & JS nur einmal laden
        if (!self::$assetsLoaded) {

            // Hole den benutzerdefinierten CSS-Dateinamen aus den Optionen
            $cssFile = self::getOption('customCssFile');  // Holen der benutzerdefinierten CSS-Datei aus den Optionen

            // Überprüfe, ob eine benutzerdefinierte Datei gesetzt wurde
            if ($cssFile) {
                // Erstelle den absoluten Pfad zur benutzerdefinierten CSS-Datei
                $cssFilePath = $_SERVER['DOCUMENT_ROOT'] . '/src/assets/css/' . $cssFile;  // Benutze den angegebenen Dateinamen
            } else {
                // Wenn keine benutzerdefinierte CSS-Datei gesetzt wurde, benutze die Standarddatei
                $cssFilePath = $_SERVER['DOCUMENT_ROOT'] . '/src/assets/css/FancyDumpVar.css';  // Standarddatei
            }

            // Erstelle die absoluten Pfade zu den JS-Dateien
            $jsFile1Path = $_SERVER['DOCUMENT_ROOT'] . '/src/assets/js/FancyDumpVar.js';
            $jsFile2Path = $_SERVER['DOCUMENT_ROOT'] . '/src/assets/js/mark.js';

            // Fallback für filemtime, falls die Datei nicht existiert
            $cssFileVersion = self::getAssetVersion($cssFilePath);
            $jsFile1Version = self::getAssetVersion($jsFile1Path);
            $jsFile2Version = self::getAssetVersion($jsFile2Path);

            // Erstelle die Basis-URL
            $baseUrl = self::getBaseUrl();

            // Erstelle die URLs zu den Ressourcen und füge Versionierung hinzu, falls vorhanden
            $cssFileUrl = $baseUrl . '/src/assets/css/' . basename($cssFilePath) . ($cssFileVersion ? '?v=' . $cssFileVersion : '');
            $jsFile1Url = $baseUrl . '/src/assets/js/FancyDumpVar.js' . ($jsFile1Version ? '?v=' . $jsFile1Version : '');
            $jsFile2Url = $baseUrl . '/src/assets/js/mark.js' . ($jsFile2Version ? '?v=' . $jsFile2Version : '');

            // Ressourcen mit Versionierung einbinden
            echo '<link rel="stylesheet" href="' . $cssFileUrl . '">' . "\n";
            echo '<script src="' . $jsFile1Url . '" defer></script>' . "\n";
            echo '<script src="' . $jsFile2Url . '" defer></script>' . "\n";

            self::$assetsLoaded = true;
        }


		// Prüfen, ob die Option 'dumpWrapper' aktiviert ist
		$dumpWrapper = self::getOption('dumpWrapper');
		$dumpWrapperStyle = self::getOption('dumpWrapperStyle');


		// Wenn Custom 'dumpWrapper' aktiviert ist, dann füge einen Wrapper mit dem angegebenen Stil hinzu
		if ($dumpWrapper) {
			echo '<div class="fdv-wrapper" style="' . htmlspecialchars($dumpWrapperStyle) . '">';  // Wrapper mit Stil öffnen
		}
   
    
        // Beginn der Wrapper-DIV für den Dump
        echo '<div class="fds-dump-wrapper" id="' . $wrapperId . '">';



        // Globale Zeile mit Toggle-Symbol [+] / [-] für die gesamte Dump-Ausgabe
/*        
        echo '<div class="dump-title-bar" id="title-bar-dump-'.$ID.'" onclick="toggleDump(\'' . $dumpId . '\')">';
            echo '<span class="dump-toggler-symbol" id="toggle-' . $dumpId . '">+</span>';
            echo '<span class="dump-title">'.self::getOption('Title').'</span>'; // Optionaler Titel
        echo '</div>';
*/

    // Berechne die Anzahl der Variablen und Elemente
    $variableCount = count(self::$stack);
    $elementCount = 0;
    foreach (self::$stack as $entry) {
        $elementCount += self::countElements($entry['data']);
    }

// Globale Zeile mit Toggle-Symbol [+] / [-] für die gesamte Dump-Ausgabe
echo '<div class="dump-title-bar" id="title-bar-dump-' . $ID . '" onclick="toggleDump(\'' . $dumpId . '\')">';
    echo '<span class="dump-toggler-symbol" id="toggle-' . $dumpId . '">+</span>';  // Pluszeichen ganz links
    echo '<div class="dump-info">';  // Container für die Infos
        echo '<span class="variable-count">Variablen: ' . $variableCount . '</span>';
        echo '<span class="element-count">Elemente: ' . $elementCount . '</span>';
    echo '</div>';
    echo '<span class="dump-title">' . self::getOption('Title') . '</span>'; // Optionaler Titel, rechtsbündig
echo '</div>';
      

     

        // Beginn des Debug-Ausgabebereichs
        echo '<div class="dump-container hidden" id="container-' . $dumpId . '">';

        // Steuerungsbereich: Suchleiste und Buttons zum Expand/Close der Tree-Struktur
        echo '<div class="dump-controls">';


        // Generiere den Pfad zu den Bildern
        if (defined('ABSPATH')) {
            // Wir befinden uns in einer WordPress-Umgebung
            $imagePath = plugin_dir_url(__FILE__) . 'assets/images/';
        } else {
            // Wir befinden uns in einer normalen PHP-Anwendung
            $imagePath = $baseUrl . '/src/assets/images/'; // Oder mit $_SERVER['DOCUMENT_ROOT'] für absoluten Pfad
        }


        echo '<div class="dump-header">';

        // Suchfeld bleibt links
        echo '<div class="dump-search-wrapper">';  
            echo '<input type="text" id="search-' . $dumpId . '" class="dump-input" placeholder="' . _t('search_icon','🔍') . ' ' . _t('search_text','Search...') . '" oninput="highlightSearch(\'' . $dumpId . '\')">';
    
            echo '<div class="toggle-buttons">
                        <button class="toggle-btn" id="whole-word-toggle-' . $dumpId . '" onclick="toggleWholeWord(\'' . $dumpId . '\')">
                            <img width="18px" src="' . $imagePath . 'button-whole-word.svg" alt="Whole Word" title="Nur ganzes Wort suchen">
                        </button>
                        <button class="toggle-btn" id="case-sensitive-toggle-' . $dumpId . '" onclick="toggleCaseSensitive(\'' . $dumpId . '\')">
                            <img width="18px" src="' . $imagePath . 'button-case.svg" alt="Case Sensitive" title="Groß- und Kleinschreibung beachten">
                        </button>
                    </div>';           
            echo '<button  class="toggle-btn fdv-clear" onclick="clearSearch(\'' . $dumpId . '\')">
                        <img width="18px" src="' . $imagePath . 'button-clear.svg" alt="Clear" title="Suche zurücksetzen">
                    </button>';
        echo '</div>'; // Ende Suchfeld-Wrapper
    
        // Container für Expand/Close & Help-Buttons (Bleibt rechts)
        echo '<div class="dump-control-buttons">'; 
            echo '<div class="dump-tree">';           
                echo '<button id="toggle-btn-' . $dumpId . '" class="dump-btn toggle" onclick="toggleExpandAll(\'' . $dumpId . '\')">
                        <img id="toggle-icon-' . $dumpId . '" src="' . $imagePath . 'folder_expand.svg" alt="Expand All">
                    </button>';    
            echo '</div>';
    
            // Help-Button
            $helpUrl = self::getOption('helpUrl');
            echo '<div class="dump-help">';
            echo '<a href="' . htmlspecialchars($helpUrl) . '" target="_blank" class="dump-btn help">
                    <img src="' . $imagePath . 'help.svg" alt="Help">
                </a>';
            echo '</div>';
        echo '</div>'; // Ende der dump-controls
    
    echo '</div>'; // Ende des übergeordneten Containers `dump-header`

echo '</div>'; // Ende des übergeordneten Containers `dump-header`
    
   
        
        



        // Falls keine spezifischen Variablen ausgewählt wurden, werden alle Variablen ausgegeben
        if (empty($selectedVars)) {
            // Alle Einträge (egal ob mit oder ohne 'type') werden in den Stack übernommen
            $filteredStack = self::$stack;
        } else {
            // Filtere den Stack anhand der angegebenen Variablen
            $filteredStack = [];
            foreach ($selectedVars as $var) {
                $found = false;
                foreach (self::$stack as $entry) {
                    // Überprüfe, ob der Eintrag die Daten enthält und keinen 'type' hat
                    if ($entry['data'] === $var) {
                        $filteredStack[] = $entry;
                        $found = true;
                        break;
                    }
                }
                // Gibt einen Fehler aus, falls die Variable nicht gefunden wurde
                if (!$found) {
                    echo '<div class="varNotFound">⚠ Variable "' . htmlspecialchars(var_export($var, true)) . '" wurde nicht gefunden!</div>';
                }
            }
        }

    
        // Ausgabe der gefilterten Variablen
        foreach ($filteredStack as $index => $entry) {
            // Setze den Rekursions-Stack für jede Variable zurück,
            // damit jede Variable unabhängig verarbeitet wird.
            self::$currentStack = [];

            // Erzeuge eine einzigartige ID für jede Variable basierend auf $dumpId und dem aktuellen Schleifenindex
            $infoID = $dumpId . '-' . $index;            

            // Überprüfe den Typ des Eintrags
            if (empty($entry['type'])) {  
                // Reguläre Variable (ohne Typ)
                $varName = htmlspecialchars($entry['name']);
                $formattedTime = self::getOption('ShowTimeInfo') ? ' <div class="varTimeInfo">(' . date(self::getOption('TimeInfoFormat'), $entry['timestamp']) . ')</div>' : '';
                
                echo '<div class="var-wrapper">';

                echo '<div class="varName">' . $varName . ': ';
                echo '<button title="'._t('more_infos','More Information').'" class="info-icon-btn" onclick="toggleVarInfo(\'' . $infoID. '\')">';
                echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="info-icon" >
                          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                          <text x="12" y="18.5" font-size="17" text-anchor="middle" fill="currentColor" font-family="Georgia, serif" font-weight="bold">i</text>
                      </svg>';
                echo '</button>';

                // Überprüfe, ob eine Historie vorhanden ist und zeige den Button nur dann an
                if (!empty($entry['history'])) {
                    echo '<button title="'._t('show_history','History').'" class="history-icon-btn" onclick="toggleVarHistory(\'' . $infoID. '\')">';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="history-icon" >
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none"/>
                            <text x="12" y="18.5" font-size="17" text-anchor="middle" fill="currentColor" font-family="Georgia, serif" font-weight="bold">H</text>
                        </svg>';
                    echo '</button>';
                }                
             
                echo '</div>';


                echo self::formatVar($entry['data'], $dumpId . '-var' . $index, 1);     
                
                    // Zähler für den Index der Schleife
                    $date_format = self::getOption('DateInfoFormat');
                    $time_format = self::getOption('TimeInfoFormat');

                $timestamp = $entry['timestamp']; // Gespeicherter Wert von microtime(true)
                $seconds = floor($timestamp); // Ganze Sekunden
                $milliseconds = sprintf("%03d", ($timestamp - $seconds) * 1000); // Millisekunden (3 Stellen)                        

                // Zeige Timestamp, ElapsedTime und Size
                echo '<div class="varInfo" id="' . $infoID . '-varInfo" style="display:none;">';  // Initial versteckt
                echo '<div class="varInfo-head">'._t('more_infos','More Information').'</div>';                
                echo '<div class="varInfoItem varTimestamp">'._t('timestamp','Timestamp').': <span class="varInfoItemValue">' . date($date_format, $seconds) . ' ' .date($time_format, $seconds) . ' ' . $milliseconds . 'ms'. '</span></div>';
                echo '<div class="varInfoItem varElapsedTime">'._t('elapsed_time','Elapsed Time').': <span class="varInfoItemValue">' . number_format($entry['elapsedTime'], 5) . 's</span></div>';
                echo '<div class="varInfoItem varSize">'._t('size','Size').': <span class="varInfoItemValue">' . number_format($entry['size']) . ' bytes</span></div>';
                echo '<div class="varInfoItem varCount">'._t('element_count','Element Count').': <span class="varInfoItemValue">' . $entry['elementCount'] . '</span></div>';    
                echo '<div class="varInfoItem varCount">'._t('var_type','Var Type').': <span class="varInfoItemValue">' . $entry['varType'] . '</span></div>';                    
    
                echo '</div>';   
                
                if (!empty($entry['history'])) {
                    echo '<div class="varHistory" id="' . $infoID . '-varHistory" style="display:none;">';  // Initial versteckt
                
                    // Beginne die Liste der historischen Einträge
                    echo '<div class="history-list">';
                    echo '<div class="history-head">'._t('show_history','History').'</div>';


                    // Tabellenkopf
                    echo '<div class="history-table">';
                    echo '<div class="history-row history-header">';
                    echo '<div class="history-cell history-version">Version</div>';
                    echo '<div class="history-cell history-timestamp">Zeitpunkt</div>';                    
                    echo '<div class="history-cell history-data">Wert</div>';

                    echo '</div>'; // Ende Tabellenkopf



             

                    $index = 0;
                    foreach ($entry['history'] as $historyEntry) {
                        // Bestimme die Klasse basierend auf dem Index: gerade Zahlen bekommen 'even', ungerade 'odd'
                        $class = ($index % 2 == 0) ? 'even' : 'odd';

                        $timestamp = $historyEntry['timestamp']; // Gespeicherter Wert von microtime(true)
                        $seconds = floor($timestamp); // Ganze Sekunden
                        $milliseconds = sprintf("%03d", ($timestamp - $seconds) * 1000); // Millisekunden (3 Stellen)                               

                        echo '<div class="history-row ' . $class . '">';
                        echo '<div class="history-cell history-version">' . htmlspecialchars($historyEntry['version']) . '</div>';
                        echo '<div class="history-cell history-timestamp">';
                        echo '📅 ' . date($date_format, $seconds) . '<br>';
                        echo '🕒 ' . date($time_format, $seconds) . ' ' . $milliseconds . 'ms';
                        echo '</div>';                 
                        echo '<div class="history-cell history-data">' . self::formatVar($historyEntry['data'], $infoID . '-history-' . $historyEntry['version'], 0) . '</div>';

                        echo '</div>'; // Ende der Zeile

                        $index++; // Inkrementiere den Index nach jeder Iteration
                    }
                    echo '</div></div></div>';  


/*
                        foreach ($entry['history'] as $historyEntry) {                        

                            // Array oder Objekt

                            echo '<div class="history-version">Version: ' . htmlspecialchars($historyEntry['version']) . '</div> ';
                            echo '<div class="history-timestamp">Zeitpunkt: ' . date('Y-m-d H:i:s', $historyEntry['timestamp']) . '</div>';
                            echo '<div class="history-data">Wert: </div>';
                            echo self::formatVar($historyEntry['data'], $infoID . '-history-' . $historyEntry['version'], 0);                            
                            echo 'Wert:'. self::formatVar($historyEntry['data'], $infoID . '-history-' . $historyEntry['version'], 0).'<br>';  
                        }    
*/


                 //   echo '</div></div>';

       
                }
                
                                         


                
                echo '</div>';

            } elseif ($entry['type'] === 'infotext') {
                // Infotext
                $formattedTime = self::getOption('ShowTimeInfo') ? ' <div class="varTimeInfo">(' . date(self::getOption('TimeInfoFormat'), $entry['timestamp']) . ')</div>' : '';
                $infoClass = isset($entry['class']) ? $entry['class'] : 'InfoMessage'; // Falls keine Klasse gesetzt, Standard 'InfoMessage'
                
                echo '<div class="infotext-wrapper ' . $infoClass . '">';
                echo '<span class="infoName">Info: </span>' . htmlspecialchars($entry['data']);
                echo $formattedTime;
                echo '</div>';
            } elseif ($entry['type'] === 'timeinfo') {
                // Zeitinformationen
                $formattedTime = self::getOption('ShowTimeInfo') ? ' <span class="varTimeInfo">(' . date(self::getOption('TimeInfoFormat'), $entry['timestamp']) . ')</span>' : '';
                $elapsedTimeFormatted = number_format($entry['elapsedTime'], 5); // Formatierte Zeit

                echo '<div class="timeinfo-wrapper">';
                echo '<span class="infoName">Runtime: </span>' . $elapsedTimeFormatted . ' Sekunden';
                echo $formattedTime;
                echo '</div>';
            }
        }
    
        echo '</div></div>';
		
		// Wenn der Wrapper aktiviert wurde, dann schließe den Wrapper
		if ($dumpWrapper) {
			echo '</div>';  // Wrapper schließen
		}		
    
        // Optional: Leert den Stack, falls alle Variablen ausgegeben wurden
        // self::$stack = [];
    }
    

    /**
     * Ermittelt die tatsächlichen Variablennamen aus dem aufrufenden Code.
     *
     * @return array Enthält die Variablennamen als Strings.
     */
    protected static function getVariableNames_old() {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $line = file($backtrace[1]['file'])[$backtrace[1]['line'] - 1];

        preg_match('/dump\((.*?)\);/', $line, $matches);
        return isset($matches[1]) ? array_map('trim', explode(',', $matches[1])) : [];
    }


    /**
     * Ermittelt die tatsächlichen Variablennamen aus dem aufrufenden Code.
     *
     * @return array Enthält die Variablennamen als Strings.
     */
    protected static function getVariableNames() {
        // Hole den Stacktrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        
        // Hole den Dateinamen und die Zeilennummer des Aufrufs
        $file = $backtrace[1]['file'];
        $lineNumber = $backtrace[1]['line'];

        // Lese die Datei ein und finde den Funktionsaufruf
        $fileContents = file_get_contents($file);
        
        // Extrahiere die Zeile mit dem Funktionsaufruf, ggf. mehrere Zeilen zusammenfügen
        $lines = explode("\n", $fileContents);
        $line = $lines[$lineNumber - 1];  // Hole die betreffende Zeile
        
        // Wenn der Funktionsaufruf über mehrere Zeilen geht, setzen wir die Zeilen zusammen
        while (substr(trim($line), -1) !== ')' && $lineNumber < count($lines)) {
            $lineNumber++;
            $line .= ' ' . trim($lines[$lineNumber - 1]);
        }

        // Entferne alle Zeilenumbrüche und extra Leerzeichen
        $line = str_replace(["\r", "\n", "\r\n"], ' ', $line); // Entfernt alle Arten von Zeilenumbrüchen
        $line = preg_replace('/\s+/', ' ', $line); // Reduziere alle Leerzeichen auf ein einzelnes
        $line = trim($line); // Entferne führende und nachfolgende Leerzeichen

        // Extrahiere den Funktionsaufruf mit den Variablen
        if (preg_match('/dump\((.*?)\);/', $line, $matches)) {
            // Hole den Inhalt der Variablen im Funktionsaufruf
            $vars = $matches[1];

            // Teile die Variablen auf und entferne unnötige Leerzeichen
            return array_map('trim', explode(',', $vars));
        }

        return [];  // Falls keine Variablen gefunden wurden
    }




    



    /**
     * Formatiert eine Variable zur Ausgabe.
     * Unterstützt verschiedene Datentypen, rekursive Strukturen und Objekte.
     *
     * @param mixed $var Die zu formatierende Variable.
     * @param string $id Eindeutige ID für das HTML-Element.
     * @param int $level Rekursionstiefe.
     * @return string HTML-formatierte Darstellung der Variable.
     */
    protected static function formatVar($var, $id, $level = 1) {

        $indent = str_repeat('', $level);

        // Begrenzung der Rekursionstiefe (vertikale Begrenzung)
        if ( $level > self::getOption('maxDepth') ) {
            return '<span class="dump-null">[...] (max depth (' . self::getOption('maxDepth') . ') reached)</span>';
        }   

        // Falls ein Array zu viele Elemente hat (horizontale Begrenzung)
        /* elemente im vertikale begrenzen
        1.
        2.
        3. 
        Ende 
        Funktioniert aber noch nicht, muss bei dumpVar in 
        */
      /*
        if (is_array($var) && count($var) > self::getOption('maxElementsPerLevel') ) {
            return '<span class="dump-null">[...] (max ' . self::getOption('maxElementsPerLevel') . ' elements shown, total: ' . count($var) . ')</span>';
        }        
      */

        // Formatierung für boolesche Werte
        if (is_bool($var)) {
            return '<span class="dump-value-caption">'._t('value','Wert').':</span> <span class="'.'dump-line-'.$level. ' dump-bool '. ($var ? 'bool-true' : 'bool-false') .'">' . ($var ? '✔ true' : '✖ false') . '</span>';
        }
        // Formatierung für Ganzzahlen
        if (is_int($var)) {
            return '<span class="dump-value-caption">'._t('value','Wert').':</span> <span class="dump-int">' . $var . '</span>';
        }
        // Formatierung für Fließkommazahlen
        if (is_float($var)) {
            return '<span class="dump-value-caption">'._t('value','Wert').':</span> <span class="dump-float">' . $var . '</span>';
        }
        // Formatierung für Strings
        if (is_string($var)) {
            return strpos($var, "\n") !== false 
                ? '<span class="dump-value-caption">'._t('value','Wert').':</span><br> <pre class="dump-string">' . htmlspecialchars($var) . '</pre>' 
                : '<span class="dump-value-caption">'._t('value','Wert').':</span> <span class="dump-string">"' . htmlspecialchars($var) . '"</span>';
        }
        // Formatierung für null
        if (is_null($var)) {
            return '<span class="dump-value-caption">'._t('value','Wert').':</span> <span class="dump-null">null</span>';
        }

        // Formatierung für Arrays
        if (is_array($var)) {
            $sortPropertiesAndMethods = self::getOption('sortPropertiesAndMethods');
            if ($sortPropertiesAndMethods) {
                ksort($var);
            }
            $output = '<div class="dump-toggler-wrapper" >' . $indent . '<span class="dump-toggler" id="btn-' . $id . '" onclick="toggleElement(\'' . $id . '\')">[+]</span>';
            $output .= '<span class="dump-array" onclick="toggleElement(\'' . $id . '\')">Array (' . count($var) . ')</span>';

            $output .= '<div class="dump-content-level-'.$level.' dump-content hidden" id="' . $id . '">';
            foreach ($var as $key => $value) {
                $output .= '<div class="dump-array-item">' . $indent . '<span class="dump-key"><span class="dumpbracket">[</span>' . htmlspecialchars($key) . '<span class="dumpbracket">]</span></span><span class="class-arrow"> => </span>' . self::formatVar($value, $id . '-' . $key, $level + 1) . '</div>';
            }
            $output .= '</div></div>';
            return $output;
        }


        // Formatierung für Objekte
        if (is_object($var)) {
            $objectId = spl_object_id($var);
            $refID =  'objid-'.$objectId.' ';

            // Erstelle ein Array, das nur die Objekt-IDs enthält.
            $ids = array_column(self::$currentStack, 'id');        
            

        // Debug-Ausgabe des gesamten Stacks
        /*
        echo "<pre>📌 Stack-Debugging:\n";
        print_r(self::$currentStack);
        echo "</pre>";
        */

            // Prüfe, ob das Objekt bereits in der aktuellen Rekursionskette vorhanden ist
            $ids = array_column(self::$currentStack, 'id');            
            if (in_array($objectId, $ids)) {
                // Objekt bereits in der Rekursionskette gefunden
                $key = array_search($objectId, $ids);
                $existing = self::$currentStack[$key];
                return '<span class="dump-null">[...] (🔄 Recursive Object Reference to ' . htmlspecialchars($existing['class']) . ')</span>';
            }



            try {
                // Versuche, mittels Reflection die Klasseninformationen zu holen
                $reflection = new \ReflectionClass($var);
                $className = $reflection->getName();

                // Füge das aktuelle Objekt der Rekursionskette hinzu
                // self::$currentStack[] = $objectId;
                // Statt nur die Objekt-ID zu speichern, speichern wir ein Array mit 'id' und 'class'.
                self::$currentStack[] = ['id' => $objectId, 'class' => $className];


                $output = '<div class="dump-toggler-wrapper">';
                $output .= '<span class="dump-toggler" id="btn-' . $id . '" onclick="toggleElement(\'' . $id . '\')">[+]</span>';
                $output .= '<span class="dump-object" onclick="toggleElement(\'' . $id . '\')">Object of <span class="fds_classname">' . htmlspecialchars($className) . '</span></span>';
                $output .= '<div class="dump-content hidden" id="' . $id . '">';

                // Array zur Speicherung der bereits verarbeiteten Property-Namen, um doppelte Einträge zu vermeiden
                $declaredProperties = [];

                // 1️⃣ Standard-Properties der Klasse abrufen
                try {
                    $properties = $reflection->getProperties();
                    if (!empty($properties)) {
                        $output .= '<div class="object-methods">Properties:</div>';
                        foreach ($properties as $prop) {
                            $prop->setAccessible(true);
                            $visibility = $prop->isPublic() ? 'public' : ($prop->isProtected() ? 'protected' : 'private');
                            $propName = $prop->getName();

                            // Speichern, um doppelte Einträge zu vermeiden
                            $declaredProperties[$propName] = true;

                            $output .= '<div class="dump-object-item"><span class="obj-dumpbracket">[</span><span class="fds_propname_visibility ' . $visibility . '">' . $visibility . '</span><span class="obj-dumpbracket">]</span> ';
                            $output .= '<span class="fds_propname">$' . htmlspecialchars($propName) . '</span></span><span class="class-arrow"> => </span>';

                            try {
                                $output .= self::formatVar($prop->getValue($var), $id . '-prop-' . $propName, $level + 1);
                            } catch (Throwable $propError) {
                                $output .= '<span class="dump-error">⚠ Fehler beim Zugriff auf Eigenschaft: ' . htmlspecialchars($propError->getMessage()) . '</span>';
                            }
                            $output .= '</div>';
                        }
                    }
                } catch (Throwable $propertyError) {
                    $output .= '<div class="dump-error">⚠ Fehler beim Lesen der Eigenschaften: ' . htmlspecialchars($propertyError->getMessage()) . '</div>';
                }

                // 2️⃣ Dynamische (nachträglich hinzugefügte) Eigenschaften abrufen
                try {
                    $dynamicProperties = get_object_vars($var);
                    if (!empty($dynamicProperties)) {
                        $output .= '<div class="object-methods">Dynamic Properties:</div>';
                        foreach ($dynamicProperties as $key => $value) {
                            // Überspringe, falls die Property bereits als Standard-Property existiert
                            if (isset($declaredProperties[$key])) {
                                continue;
                            }
                            $output .= '<div class="dump-object-item"><span class="obj-dumpbracket">[</span><span class="fds_propname_visibility public">dynamic</span><span class="obj-dumpbracket">]</span> ';
                            $output .= '<span class="fds_propname">$' . htmlspecialchars($key) . '</span></span><span class="class-arrow"> => </span>';
                            try {
                                $output .= self::formatVar($value, $id . '-prop-' . $key, $level + 1);
                            } catch (Throwable $dynPropError) {
                                $output .= '<span class="dump-error">⚠ Fehler beim Zugriff auf dynamische Eigenschaft: ' . htmlspecialchars($dynPropError->getMessage()) . '</span>';
                            }
                            $output .= '</div>';
                        }
                    }
                } catch (Throwable $dynPropCatchError) {
                    $output .= '<div class="dump-error">⚠ Fehler beim Lesen dynamischer Eigenschaften: ' . htmlspecialchars($dynPropCatchError->getMessage()) . '</div>';
                }

                // 3️⃣ Methoden des Objekts abrufen
                try {
                    $methods = $reflection->getMethods();
                    if (!empty($methods)) {
                        $output .= '<div class="object-methods">Methods:</div>';
                        foreach ($methods as $method) {
                            $visibility = $method->isPublic() ? 'public' : ($method->isProtected() ? 'protected' : 'private');
                            $output .= '<div>' 
                                . '[<span class="fds_method_visibility ' . $visibility . '">' . $visibility . '</span>] '
                                . '<span class="fds_methodname">' . htmlspecialchars($method->getName()) . '</span>';

                            // Parameter der Methode abrufen und formatieren
                            try {
                                $params = [];
                                foreach ($method->getParameters() as $param) {
                                    $paramStr = '<span class="fds_param_name">$' . $param->getName() . '</span>';
                                    
                                    // Typ ermitteln und korrekt darstellen
                                    if ($param->hasType()) {
                                        $type = $param->getType();
                                        if ($type instanceof \ReflectionUnionType) {
                                            $unionTypes = [];
                                            foreach ($type->getTypes() as $ut) {
                                                $unionTypes[] = $ut->getName();
                                            }
                                            $typeStr = implode('|', $unionTypes);
                                        } elseif ($type instanceof \ReflectionIntersectionType) {
                                            $intersectionTypes = [];
                                            foreach ($type->getTypes() as $it) {
                                                $intersectionTypes[] = $it->getName();
                                            }
                                            $typeStr = implode('&', $intersectionTypes);
                                        } else {
                                            $typeStr = $type->getName();
                                        }
                                        $paramStr = '<span class="fds_param_type">' . htmlspecialchars($typeStr) . '</span> ' . $paramStr;
                                    }
                                    
                                    // Default-Wert anzeigen, falls vorhanden
                                    if ($param->isDefaultValueAvailable()) {
                                        try {
                                            $defaultValue = $param->getDefaultValue();
                                            $paramStr .= ' = <span class="fds_param_default">' . var_export($defaultValue, true) . '</span>';
                                        } catch (\Throwable $e) {
                                            $paramStr .= ' = <span class="fds_param_default">[default value error]</span>';
                                        }
                                    }
                                    
                                    $params[] = $paramStr;
                                }
                                $output .= ' (' . implode(", ", $params) . ')';
                            } catch (Throwable $paramError) {
                                $output .= '<span class="dump-error">⚠ Fehler beim Lesen der Methodenparameter: ' . htmlspecialchars($paramError->getMessage()) . '</span>';
                            }
                            $output .= '</div>';
                        }
                    }
                } catch (Throwable $methodError) {
                    $output .= '<div class="dump-error">⚠ Fehler beim Lesen der Methoden: ' . htmlspecialchars($methodError->getMessage()) . '</div>';
                }

                // Falls keine Properties, dynamische Properties oder Methoden vorhanden sind, Hinweis ausgeben
                if (empty($properties) && empty($methods) && empty($dynamicProperties)) {
                    $output .= '<div class="object-empty">🚫 Kein Inhalt!</div>';
                }

                $output .= '</div></div>';
                return $output;
            } catch (Throwable $reflectionError) {
                return '<span class="dump-error">⚠ Fehler beim Reflektieren des Objekts: ' . htmlspecialchars($reflectionError->getMessage()) . '</span>';
            }

            // Entferne das aktuelle Objekt aus dem Rekursions-Stack,
            // damit es bei einer neuen, unabhängigen Rekursion wieder voll angezeigt wird.
            array_pop(self::$currentStack);
        }


        
        // Fallback: Nutze print_r und htmlspecialchars, falls kein spezifisches Format gefunden wurde
        return htmlspecialchars(print_r($var, true));
    }
}
?>
