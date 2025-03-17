# 🚀 FancyDumpVar – Eleganter PHP-Variablen-Debugger

FancyDumpVar (FDV) ist eine leistungsstarke PHP-Bibliothek für Entwickler, die komplexe Variablen, Objekte und rekursive Datenstrukturen schnell und übersichtlich debuggen möchten. Ergänzend steht ein komfortables WordPress-Modul bereit, um das Debugging direkt innerhalb deiner WordPress-Umgebung zu vereinfachen.

---

## ✨ Features

- ✅ **Strukturierte Ausgabe**: Zeigt Arrays, Objekte, primitive Datentypen und rekursive Strukturen klar an.
- ✅ **Interaktive Ansicht**: Expand/Collapse-Funktion für übersichtliches Arbeiten.
- ✅ **Suchfunktion**: Integrierte Such- und Highlight-Funktion innerhalb der Variablendarstellung.
- ✅ **Performance-Tools**: Eingebaute Stopwatch für Laufzeitmessungen.
- ✅ **Flexible Anpassungen**: Eigene Templates, CSS-Styles und Mehrsprachigkeit.
- ✅ **WordPress-Modul**: Direkte Integration in WordPress inklusive Admin-Backend-Unterstützung.

---

## 🛠️ Installation

### Composer (folgt in Kürze)
*Composer-Unterstützung wird demnächst ergänzt.*

### Manuelle Installation
Klone das Repository und binde es direkt in dein Projekt ein:

```bash
git clone https://github.com/johannes-teitge/FancyDumpVar.git
```

---

## 📚 Verwendung

### Basisbeispiel (PHP)
```php
require_once 'vendor/autoload.php';

use FancyDumpVar\FancyDumpVar as FDV;

// Beispielvariable
$testArray = ['Name' => 'John', 'Alter' => 30, 'Job' => 'Entwickler'];

FDV::dump($testArray);
FDV::dumpOut();
```

### WordPress-Modul

Installiere und aktiviere das mitgelieferte WordPress-Plugin:

- Lade den Ordner `fdv-plugin` in den WordPress-Plugin-Ordner (`wp-content/plugins/`).
- Aktiviere das Plugin im WordPress-Backend.
- Nutze die FancyDumpVar-Methoden direkt in deinem Theme oder anderen Plugins:

```php
use FancyDumpVar\FancyDumpVar as FDV;

FDV::dump($wp_query);
FDV::dumpOut();
```

---

## ⚙️ Anpassung & Konfiguration

FancyDumpVar erlaubt umfangreiche Anpassungen:

- `maxRecursionDepth`: Maximale Tiefe rekursiver Strukturen
- `assetsNoCache`: Verhindert Caching beim Entwickeln
- `customCssFile`: Eigenes CSS für individuelle Designs
- `language`: Unterstützte Sprachen (z.B. Deutsch/Englisch)

```php
FDV::setOption('assetsNoCache', true);
FDV::setOption('customCssFile', 'monolight.css');
FDV::setOption('language', 'de');
```

---

## 🚧 Roadmap / TODOs

### 📝 Geplante Verbesserungen

- [ ] **Parsing verbessern** *(Low)*  
  _Wenn man Werte anstatt Variablen übergibt, funktioniert das Parsing noch nicht korrekt._
- [ ] **Template „VisualStudioStyle.css“ optimieren** *(Medium)*  
  _Das Template muss noch verbessert werden._
- [ ] **Template „Monocrom.css“ optimieren** *(Medium)*  
  _Das Template muss noch verbessert werden._
- [ ] **maxElementsPerLevel implementieren** *(Medium)*  
  _Bis zum Limit sollen alle Array-Elemente angezeigt werden._
- [ ] **WordPress-Modul Templates integrieren** *(High)*  
  _Feature für das WP-Modul und weitere Optionen fehlen noch._
- [ ] **Optionen für Imagefiles erweitern** *(High)*  
  _Optionen müssen noch für Imagefiles erweitert werden._
- [ ] **Optionen für Multilanguage ausbauen** *(High)*  
  _Mehrsprachigkeit weiter ausbauen._

---

## 📌 Lizenz

FancyDumpVar steht unter der [GNU GPL v3 Lizenz](https://www.gnu.org/licenses/gpl-3.0.html).

---

## 🤝 Autor & Support

**Johannes Teitge**  
📧 johannes@teitge.de  
🌐 [https://teitge.de](https://teitge.de)

Fragen, Feedback oder Vorschläge jederzeit willkommen!

---

### 🌟 Happy Debugging!

