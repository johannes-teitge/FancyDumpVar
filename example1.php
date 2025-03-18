<?php

// Sicherstellen, dass die Datei existiert und geladen wird
if (!file_exists('src/FancyDumpVar.php')) {
    die("Fehler: FancyDumpVar.php nicht gefunden!");  // Stoppt die Ausführung, falls die Datei nicht vorhanden ist
}
require_once 'src/FancyDumpVar.php';  // Einbinden der FancyDumpVar-Klasse

use FancyDumpVar\FancyDumpVar;  // FancyDumpVar importieren
// Alias für FancyDumpVar als FDV
use FancyDumpVar as FDV;        

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FancyDumpVar Debugger</title>

    <style>
        /* Basis CSS für das Layout */
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
            margin-bottom: 10px;
        }

        .description {
            font-size: 1rem;
            color: #555;
            max-width: 600px;
            margin: 0 auto 20px;
            line-height: 1.4;
        }

        .debug-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 20px auto;
            text-align: left;
        }
    </style>
</head>

<body>

    <h1>FancyDumpVar Debugger</h1>
    <p class="description">
        Dieses Tool hilft Entwicklern, **PHP-Variablen zu debuggen**.  
        Es stellt **Arrays, Objekte und primitive Datentypen** in einer strukturierten,  
        interaktiven Ansicht dar.  
    </p>

    <div class="debug-container">
        <?php

            // Rekursive Testdatenstruktur mit einer Tiefe von 12
            function generateRecursiveData($depth = 12) {
                if ($depth === 0) {
                    return "Ende der Rekursion";  // Basisfall für die Rekursion
                }
                
                return [
                    "level" => $depth,
                    "next" => generateRecursiveData($depth - 1)  // Rekursive Struktur
                ];
            }

            // Beispiel einer rekursiven Testdatenstruktur mit Tiefe 12
            $recursiveData = generateRecursiveData(12);            

            // Testdaten für Arrays und Objekte
            $testArray = ["name" => "John", "age" => 30, "size" => 1.93, "job" => "Developer", "male" => true, "female" => false ];
            $testArray2 = ["name" => "John", "age" => 30, "job" => "Developer"];            

            // Testklasse mit verschiedenen Sichtbarkeiten für Eigenschaften
            class TestClass {
                public string $message;
                public int $number;
                public $test;
            
                private string $password;
                protected string $password_key;
            
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
            
                // Optional: Getter für private password
                public function getPassword(): string {
                    return $this->password;
                }
            
                // Optional: Setter für private password
                public function setPassword(string $password): void {
                    $this->password = $password;
                }
            }

            // Instanziierung eines Testobjekts
            $testObj = new TestClass();
            $testObj->test = 'Hallo';  // Zusätzliche Eigenschaft für Testobjekt

            // Anonymes leeres Objekt
            class emptyClass {}

            $emptyObject = new emptyClass;

            // FancyDumpVar Instanz und Beispiel-Dump
            $debugger = new  \FancyDumpVar\FancyDumpVar();
            $debugger->dump($recursiveData, $testArray, $testArray2, $emptyObject, $testObj, $debugger);

			// Sprache setzen
            $debugger->setOption('language', 'de');			

			// Daten dumpen
            $debugger->dumpOut();  // Ausgabe der gedumpten Daten

            // Optionen ausgeben
            $options = $debugger->getOptions();
            echo '<h3>Optionen</h3>';
            echo '<pre>';
            print_r($options); // Gibt das gesamte Options-Array aus
            echo '</pre>';     

            // TODO-Liste anzeigen
            echo '<h3>ToDo</h3>';         
            echo $debugger->showTodos();                  // Zeigt die ToDo-Liste an

        ?>
    </div>

</body>
</html>
