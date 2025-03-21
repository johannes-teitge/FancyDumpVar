<?php

// Fehleranzeigen aktivieren für das Debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sicherstellen, dass die Datei existiert und geladen wird
if (!file_exists('src/FancyDumpVar.php')) {
    die("Fehler: FancyDumpVar.php nicht gefunden!");
}
require_once 'src/FancyDumpVar.php';  // Einbinden der FancyDumpVar-Klasse

use FancyDumpVar\FancyDumpVar as FDV; // Alias setzen, um die Klasse als FDV zu verwenden



// Testdaten und Beispielklassen
$testArray = ["name" => "John", "age" => 30, "size" => 1.93, "job" => "Developer", "male" => true, "female" => false ];
$testArray2 = ["name" => "Jane", "age" => 28, "job" => "Designer"];
$simpleINT = 10;
$simpleFLOAT = 34.67;

// Beispielklasse mit öffentlichen, privaten und geschützten Eigenschaften
class TestClass {
    public string $message;  // Öffentliche Eigenschaft für eine Nachricht
    public int $number;      // Öffentliche Eigenschaft für eine Zahl   

    private string $password;      // Private Eigenschaft für ein Passwort
    protected string $password_key;  // Geschützte Eigenschaft für einen Passwortschlüssel

    // Konstruktor mit optionalen Parametern
    public function __construct(string $message = "Hello World", int $number = 42, string $password = "", string $password_key = "") {
        $this->message = $message;
        $this->number = $number;
        $this->password = $password;  // Initialisierung des privaten Passworts
        $this->password_key = $password_key;  // Initialisierung des geschützten Passwortschlüssels
    }

    // Öffentliche Methode zum Abrufen von Informationen
    public function getInfo(): string {
        return "Message: {$this->message}, Number: {$this->number}";
    }

    // Optional: Getter für das private Passwort
    public function getPassword(): string {
        return $this->password;
    }

    // Optional: Setter für das private Passwort
    public function setPassword(string $password): void {
        $this->password = $password;
    }
}

// Erstellen eines Testobjekts der Klasse TestClass
$testObj = new TestClass();

// Leeres anonymes Objekt
$emptyObject = new class {};  

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FancyDumpVar Debugger</title>
    <style>
        /* CSS für die Benutzeroberfläche */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            text-align: center;
            margin: 0;
            padding: 20px;
        }

        h1 {
            color: #0055a4;
        }

        .debug-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            text-align: left;
        }

        .debug-container pre {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
    </style>
</head>

<body>
    <h1>FancyDumpVar Debugger</h1>
    <div class="debug-container">
        <!-- Die dumpOut()-Methode gibt den Inhalt aus -->
    <?php     
        // Ausgabe der Dump-Daten
        $debugger = new \FancyDumpVar\FancyDumpVar();  // Instanziierung der FancyDumpVar-Klasse

        FDV::setOption('assetsNoCache', true);  // Caching umgehen, um sicherzustellen, dass immer die neueste Version geladen wird
        // FDV::setOption('customCssFile', 'VisualStudioStyle.css');  // Alternativ eine benutzerdefinierte CSS-Datei laden        
        // FDV::setOption('customCssFile', 'monocrom.css');  // Ein weiteres Beispiel für benutzerdefiniertes CSS    
        // FDV::setOption('customCssFile', 'monolight.css');  // Ein weiteres Beispiel für benutzerdefiniertes CSS                
        FDV::setOption('Title','Debug Ausgabe1'); // Statische Methode aufrufen, um den Titel der Ausgabe zu setzen
        // FDV::setmaxDepth(10); // Rekursionstiefe anpassen, um tiefere Strukturen zu unterstützen


        // FancyDumpVar Instanz und Dump-Aufruf
        FDV::dump($debugger, $testArray, 45.56, 45, true, 'Hallo', ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'], $testObj);
        FDV::dumpOut();  // Gibt alle gedumpten Variablen aus
        
        FDV::clearStack();  // Löscht den Stack, um den Speicher zu leeren
        
        // FancyDumpVar Instanz und Dump-Aufruf mit einem Array direkt im Funktionsaufruf
        FDV::dump($debugger,
            $testArray, 
            45.56, 
            45, 
            true, 
            'Hallo', 
            ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3'],  // Array direkt im Funktionsaufruf
            $testObj
        );
        FDV::setOption('Title','Debug Ausgabe2'); // Statische Methode aufrufen, um den Titel der Ausgabe zu setzen        
        FDV::dumpOut();  // Gibt alle gedumpten Variablen aus


        // Initiale Werte setzen
        $simpleINT = 10;
        $simpleFLOAT = 34.67;
        $testArray2 = ["name" => "Jane", "age" => 28, "job" => "Designer"];

        // Stack leeren
        FDV::clearStack();

        for ($i = 0; $i < 10; $i++) {
            // Zufällige Werte für jede Iteration generieren
            $simpleINT = rand(1, 100);                 // Zufällige Zahl zwischen 1 und 100
            $simpleFLOAT = rand(10, 100) + (rand(0, 99) / 100);  // Zufällige Float-Zahl zwischen 10 und 100 mit Nachkommastellen

            // Array-Werte zufällig ändern
            $testArray2["name"] = ["Jane", "John", "Alice", "Bob"][array_rand(["Jane", "John", "Alice", "Bob"])];
            $testArray2["age"] = rand(20, 60);
            $testArray2["job"] = ["Designer", "Developer", "Manager", "Artist"][array_rand(["Designer", "Developer", "Manager", "Artist"])];

            // Werte in FDV::dump() speichern
            FDV::dump($simpleINT, $simpleFLOAT, $testArray2, $debugger);         

            // Kleiner Delay für bessere Nachverfolgbarkeit
            usleep(50000); // 50ms Pause
        }     
        FDV::setOption('Title','Debug Ausgabe2'); // Statische Methode aufrufen, um den Titel der Ausgabe zu setzen        
        FDV::dumpOut();  // Gibt alle gedumpten Variablen aus        








    ?>    
    </div>
</body>
</html>
