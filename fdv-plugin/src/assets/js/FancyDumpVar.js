/**
 * FancyDumpVar.js
 *
 * Eine JavaScript-Utility-Datei zur interaktiven Darstellung, Verwaltung und
 * Analyse von Variablen innerhalb von FancyDumpVar.
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
 * Enthält Funktionen zur:
 * - Interaktiven Ein-/Ausklappung von Variablen
 * - Hervorhebung von Suchbegriffen innerhalb der Ausgabe
 * - Verwaltung von Historien- und Detailansichten von Variablen
 * - Steuerung der UI-Komponenten innerhalb von FancyDumpVar
 *
 * @package      FancyDumpVar
 * @author       Johannes Teitge
 * @email        johannes@teitge.de
 * @website      https://teitge.de
 * @license      GPL-3.0-or-later
 */








/**
 * toggleElement
 * 
 * Beschreibung:
 * Diese Funktion schaltet die Sichtbarkeit eines HTML-Elements um. 
 * Falls das Element sichtbar ist, wird es versteckt und das zugehörige 
 * Toggle-Symbol ändert sich entsprechend zwischen "[+]" (eingeklappt) und "[-]" (ausgeklappt).
 * 
 * Parameter:
 * @param {string} id - Die ID des Elements, das ein- oder ausgeblendet werden soll.
 * 
 * Beispiel:
 * toggleElement("myElement"); // Versteckt oder zeigt das Element mit der ID "myElement".
 */
function toggleElement(id) {
    // Hole das Element basierend auf der übergebenen ID
    var element = document.getElementById(id);

    // Überprüfen, ob das Element existiert
    if (element) {
        // Schaltet die Klasse "hidden" um (zeigt oder versteckt das Element)
        element.classList.toggle("hidden");

        // Hole das zugehörige Toggle-Button-Element
        var toggleButton = document.getElementById("btn-" + id);

        // Falls der Button existiert, aktualisiere den Text je nach Sichtbarkeitsstatus
        if (toggleButton) {
            toggleButton.innerText = element.classList.contains("hidden") ? "[+]" : "[-]";
        }
    }
}


/**
 * expandAll
 * 
 * Beschreibung:
 * Diese Funktion erweitert alle ausgeblendeten Inhalte innerhalb eines Containers
 * mit der angegebenen ID und setzt den Toggle-Button sowie die zugehörigen Symbole
 * entsprechend auf den "geöffnet"-Status.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, dessen Inhalte erweitert werden sollen.
 * 
 * Ablauf:
 * 1. Alle Elemente mit der Klasse `.dump-content` innerhalb des Containers sichtbar machen.
 * 2. Das Icon des Expand/Collapse-Buttons auf "geschlossen" setzen (folder_close.svg).
 * 3. Alle Toggle-Buttons im Container von `[+]` auf `[-]` ändern.
 * 4. Den Haupt-Toggle-Button als "aktiv" markieren.
 * 
 * Beispiel:
 * expandAll("myDump"); // Erweitert alle Inhalte innerhalb des Containers mit der ID "myDump".
 */
function expandAll(dumpId) {
    // 1. Alle `.dump-content`-Elemente im Container sichtbar machen
    document.querySelectorAll("#container-" + dumpId + " .dump-content").forEach(el => el.classList.remove("hidden"));

    // 2. Das Haupt-Toggle-Icon auf "geschlossen" setzen
    let toggleIcon = document.querySelector("#toggle-icon-" + dumpId);
    if (toggleIcon) {
        toggleIcon.src = "src/assets/images/folder_close.svg";
    }

    // 3. Alle [+] Symbole in [-] ändern (für einzelne Toggle-Buttons)
    document.querySelectorAll("#container-" + dumpId + " .dump-toggler").forEach(btn => {
        btn.innerText = "[-]";
    });

    // 4. Den Haupt-Toggle-Button als aktiv markieren
    let button = document.querySelector("#toggle-btn-" + dumpId);
    if (button) {
        button.classList.add("active");
    }
}


/**
 * closeAll
 * 
 * Beschreibung:
 * Diese Funktion versteckt alle erweiterten Inhalte innerhalb eines Containers 
 * mit der angegebenen ID und setzt den Toggle-Button sowie die zugehörigen Symbole 
 * entsprechend auf den "eingeklappt"-Status.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, dessen Inhalte eingeklappt werden sollen.
 * 
 * Ablauf:
 * 1. Alle `.dump-content`-Elemente innerhalb des Containers verstecken.
 * 2. Das Icon des Expand/Collapse-Buttons auf "geöffnet" setzen (folder_expand.svg).
 * 3. Alle Toggle-Buttons im Container von `[-]` auf `[+]` ändern.
 * 4. Den Haupt-Toggle-Button als "inaktiv" markieren.
 * 
 * Beispiel:
 * closeAll("myDump"); // Klappt alle Inhalte innerhalb des Containers mit der ID "myDump" ein.
 */
function closeAll(dumpId) {
    // 1. Alle `.dump-content`-Elemente im Container verstecken
    document.querySelectorAll("#container-" + dumpId + " .dump-content").forEach(el => el.classList.add("hidden"));

    // 2. Das Haupt-Toggle-Icon auf "geöffnet" setzen
    let toggleIcon = document.querySelector("#toggle-icon-" + dumpId);
    if (toggleIcon) {
        toggleIcon.src = "src/assets/images/folder_expand.svg";
    }

    // 3. Alle `[-]` Symbole in `[+]` ändern (für einzelne Toggle-Buttons)
    document.querySelectorAll("#container-" + dumpId + " .dump-toggler").forEach(btn => {
        btn.innerText = "[+]";
    });

    // 4. Den Haupt-Toggle-Button als inaktiv markieren
    let button = document.querySelector("#toggle-btn-" + dumpId);
    if (button) {
        button.classList.remove("active");
    }
}


/**
 * toggleExpandAll
 * 
 * Beschreibung:
 * Diese Funktion wechselt zwischen dem Ein- und Ausklappen aller Inhalte innerhalb 
 * eines Containers mit der angegebenen ID. Sie prüft, ob aktuell mindestens ein 
 * `.dump-content`-Element sichtbar ist, und entscheidet daraufhin, ob `expandAll()` 
 * oder `closeAll()` aufgerufen wird.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, dessen Inhalte getoggelt werden sollen.
 * 
 * Ablauf:
 * 1. Überprüfen, ob mindestens ein `.dump-content`-Element innerhalb des Containers sichtbar ist.
 * 2. Falls ja: `closeAll()` aufrufen, um alle Inhalte einzuklappen.
 * 3. Falls nein: `expandAll()` aufrufen, um alle Inhalte auszuklappen.
 * 
 * Beispiel:
 * toggleExpandAll("myDump"); // Klappt alle Inhalte innerhalb von "myDump" ein oder aus.
 */
function toggleExpandAll(dumpId) {
    // 1. Prüfen, ob mindestens ein `.dump-content`-Element NICHT `hidden` ist (d.h. sichtbar)
    let isExpanded = document.querySelector("#container-" + dumpId + " .dump-content:not(.hidden)") !== null;

    // 2. Falls Inhalte sichtbar sind → Alle einklappen
    if (isExpanded) {
        closeAll(dumpId);
    } 
    // 3. Falls keine Inhalte sichtbar sind → Alle ausklappen
    else {
        expandAll(dumpId);
    }
}


/**
 * highlightSearch
 * 
 * Beschreibung:
 * Diese Funktion durchsucht den angegebenen Container nach einem Suchbegriff und hebt
 * alle Übereinstimmungen hervor. Sie nutzt `mark.js`, um die Markierung durchzuführen. 
 * Zusätzlich berücksichtigt sie die Suchoptionen "Groß-/Kleinschreibung beachten" 
 * und "Nur ganzes Wort suchen".
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, in dem gesucht werden soll.
 * 
 * Ablauf:
 * 1. Den Suchbegriff aus dem entsprechenden Eingabefeld abrufen.
 * 2. Den Suchkontext (Container) bestimmen.
 * 3. Den Status der Toggle-Buttons für Suchoptionen prüfen (Ganzes Wort & Groß-/Kleinschreibung).
 * 4. Falls aktiv, die Optionen `caseSensitive` und `wholeWord` setzen.
 * 5. Alle bisherigen Markierungen entfernen.
 * 6. Falls ein Suchbegriff vorhanden ist, neue Markierungen mit `mark.js` setzen.
 * 
 * Beispiel:
 * highlightSearch("myDump"); // Sucht im Container mit der ID "myDump" und hebt Treffer hervor.
 */
function highlightSearch(dumpId) {
    // 1. Suchbegriff aus dem Eingabefeld holen
    var keyword = document.getElementById("search-" + dumpId).value;

    // 2. Suchkontext: Container, in dem gesucht werden soll
    var context = document.querySelector("#container-" + dumpId);

    // 3. Status der Toggle-Buttons abrufen (ob sie aktiv sind oder nicht)
    var isWholeWord = document.getElementById('whole-word-toggle-' + dumpId).classList.contains('active');
    var isCaseSensitive = document.getElementById('case-sensitive-toggle-' + dumpId).classList.contains('active');

    // 4. Typecasting zu Boolean (sicherstellen, dass Werte als echte Booleans behandelt werden)
    isWholeWord = !!isWholeWord;
    isCaseSensitive = !!isCaseSensitive;

    // 5. Neues Markierungs-Objekt erstellen (mark.js nutzen)
    var instance = new Mark(context);

    // 6. Entferne alle bisherigen Markierungen
    instance.unmark({
        done: function() {
            // 7. Falls ein Suchbegriff vorhanden ist, markiere ihn mit den angegebenen Optionen
            if (keyword) {
                var options = {
                    caseSensitive: isCaseSensitive, // Groß-/Kleinschreibung beachten
                    wholeWord: isWholeWord,         // Nur ganzes Wort markieren
                    done: function() {
                        console.log('Markierung abgeschlossen');
                    }
                };

                // 8. Suche mit den definierten Optionen durchführen
                instance.mark(keyword, options);
            }
        }
    });
}


/**
 * toggleWholeWord
 * 
 * Beschreibung:
 * Diese Funktion toggelt den Zustand der "Ganzes Wort suchen"-Option für die Suchfunktion.
 * Falls die Option aktiviert wird, werden nur exakte Wortübereinstimmungen hervorgehoben.
 * Falls deaktiviert, werden auch Teilausdrücke gefunden. Anschließend wird die 
 * `highlightSearch()`-Funktion aufgerufen, um die Suche entsprechend zu aktualisieren.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, dessen Suchoption geändert werden soll.
 * 
 * Ablauf:
 * 1. Den Button mit der ID `whole-word-toggle-{dumpId}` holen.
 * 2. Prüfen, ob die "active"-Klasse vorhanden ist (bedeutet, dass die Option aktiv ist).
 * 3. Falls aktiv → "active"-Klasse entfernen (Option deaktivieren).
 * 4. Falls inaktiv → "active"-Klasse hinzufügen (Option aktivieren).
 * 5. Die Suche mit `highlightSearch(dumpId)` aktualisieren.
 * 
 * Beispiel:
 * toggleWholeWord("myDump"); // Aktiviert oder deaktiviert die Ganzes-Wort-Suche und aktualisiert die Markierung.
 */
function toggleWholeWord(dumpId) {
    // 1. Den Button für die "Ganzes Wort"-Option holen
    var button = document.getElementById('whole-word-toggle-' + dumpId);

    // 2. Prüfen, ob der Button aktuell aktiv ist
    var isWholeWord = button.classList.contains('active');

    // 3. Falls aktiv, die Option deaktivieren
    if (isWholeWord) {
        button.classList.remove('active');
    } 
    // 4. Falls inaktiv, die Option aktivieren
    else {
        button.classList.add('active');
    }

    // 5. Suche aktualisieren, um die neue Option anzuwenden
    highlightSearch(dumpId);
}


/**
 * toggleCaseSensitive
 * 
 * Beschreibung:
 * Diese Funktion toggelt den Zustand der "Groß-/Kleinschreibung beachten"-Option für die Suchfunktion.
 * Falls die Option aktiviert wird, wird zwischen Groß- und Kleinschreibung unterschieden.
 * Falls deaktiviert, wird die Suche ohne Beachtung der Groß-/Kleinschreibung durchgeführt.
 * Anschließend wird die `highlightSearch()`-Funktion aufgerufen, um die Suche zu aktualisieren.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, dessen Suchoption geändert werden soll.
 * 
 * Ablauf:
 * 1. Den Button mit der ID `case-sensitive-toggle-{dumpId}` holen.
 * 2. Prüfen, ob die "active"-Klasse vorhanden ist (bedeutet, dass die Option aktiv ist).
 * 3. Falls aktiv → "active"-Klasse entfernen (Option deaktivieren).
 * 4. Falls inaktiv → "active"-Klasse hinzufügen (Option aktivieren).
 * 5. Die Suche mit `highlightSearch(dumpId)` aktualisieren.
 * 
 * Beispiel:
 * toggleCaseSensitive("myDump"); // Aktiviert oder deaktiviert die Groß-/Kleinschreibung in der Suche und aktualisiert die Markierung.
 */
function toggleCaseSensitive(dumpId) {
    // 1. Den Button für die "Groß-/Kleinschreibung"-Option holen
    var button = document.getElementById('case-sensitive-toggle-' + dumpId);

    // 2. Prüfen, ob der Button aktuell aktiv ist
    var isCaseSensitive = button.classList.contains('active');

    // 3. Falls aktiv, die Option deaktivieren
    if (isCaseSensitive) {
        button.classList.remove('active');
    } 
    // 4. Falls inaktiv, die Option aktivieren
    else {
        button.classList.add('active');
    }

    // 5. Suche aktualisieren, um die neue Option anzuwenden
    highlightSearch(dumpId);
}


/**
 * clearSearch
 * 
 * Beschreibung:
 * Diese Funktion löscht das Suchfeld, entfernt alle Markierungen aus dem Suchbereich
 * und setzt die Suchoptionen ("Ganzes Wort suchen" und "Groß-/Kleinschreibung beachten") zurück.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, dessen Suchstatus zurückgesetzt werden soll.
 * 
 * Ablauf:
 * 1. Das Eingabefeld für die Suche leeren.
 * 2. Alle markierten Elemente (`.highlight`) innerhalb des Containers zurücksetzen.
 * 3. Die Suchoptionen für "Ganzes Wort suchen" und "Groß-/Kleinschreibung beachten" deaktivieren.
 * 
 * Beispiel:
 * clearSearch("myDump"); // Setzt die Suche in "myDump" zurück und entfernt Markierungen.
 */
function clearSearch(dumpId) {
    // 1. Löscht den Inhalt des Suchfeldes
    var searchInput = document.getElementById('search-' + dumpId);
    if (searchInput) {
        searchInput.value = '';
    }

    // 2. Entfernt alle vorhandenen Highlights aus den Suchergebnissen
    var elementsToSearch = document.querySelectorAll("#container-" + dumpId + " .dump-item");  
    elementsToSearch.forEach(function(element) {
        element.classList.remove('highlight');
    });

    // 3. Setzt die Suchoptionen zurück (deaktiviert "Ganzes Wort suchen" und "Groß-/Kleinschreibung beachten")
    var wholeWordToggle = document.getElementById('whole-word-toggle-' + dumpId);
    var caseSensitiveToggle = document.getElementById('case-sensitive-toggle-' + dumpId);

    if (wholeWordToggle) {
        wholeWordToggle.classList.remove('active');
    }
    if (caseSensitiveToggle) {
        caseSensitiveToggle.classList.remove('active');
    }
}



/**
 * toggleDump
 * 
 * Beschreibung:
 * Diese Funktion schaltet die Sichtbarkeit eines Containers mit der angegebenen ID um.
 * Zusätzlich wird das Symbol (`+` oder `-`) des zugehörigen Toggle-Buttons aktualisiert
 * und die Titel-Leiste (`title-bar`) entsprechend angepasst.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des Containers, dessen Sichtbarkeit umgeschaltet werden soll.
 * 
 * Ablauf:
 * 1. Container (`#container-{dumpId}`), Toggle-Symbol (`#toggle-{dumpId}`) 
 *    und Titel-Leiste (`#title-bar-{dumpId}`) abrufen.
 * 2. Falls der Container aktuell versteckt ist:
 *    - Sichtbar machen
 *    - Toggle-Symbol auf `"-"` setzen
 *    - Die `open`-Klasse für Symbol & Titel-Leiste hinzufügen
 * 3. Falls der Container sichtbar ist:
 *    - Verstecken
 *    - Toggle-Symbol auf `"+"` setzen
 *    - Die `open`-Klasse für Symbol & Titel-Leiste entfernen
 * 
 * Beispiel:
 * toggleDump("myDump"); // Zeigt oder versteckt den Container mit der ID "myDump".
 */
function toggleDump(dumpId) {
    // 1. Die relevanten Elemente abrufen
    let container = document.getElementById("container-" + dumpId);
    let symbol = document.getElementById("toggle-" + dumpId);
    let titleBar = document.getElementById("title-bar-" + dumpId); // Titel-Bar auswählen

    // Prüfen, ob der Container existiert (Fehlertoleranz)
    if (!container || !symbol || !titleBar) return;

    // 2. Falls der Container versteckt ist, ihn anzeigen
    if (container.classList.contains("hidden")) {
        container.classList.remove("hidden"); // Sichtbar machen
        symbol.innerHTML = "-"; // Symbol auf Minus setzen
        symbol.classList.add("open"); // Symbol bekommt die "open"-Klasse
        titleBar.classList.add("open"); // Titel-Bar bekommt die "open"-Klasse
    } 
    // 3. Falls der Container sichtbar ist, ihn verstecken
    else {
        container.classList.add("hidden"); // Verstecken
        symbol.innerHTML = "+"; // Symbol auf Plus setzen
        symbol.classList.remove("open"); // "open"-Klasse vom Symbol entfernen
        titleBar.classList.remove("open"); // "open"-Klasse von der Titel-Bar entfernen
    }
}


/**
 * Tooltip-Handling für Buttons
 * 
 * Beschreibung:
 * Diese Funktion fügt allen Elementen mit der Klasse `.tooltip-btn` einen 
 * Mouseover-Effekt hinzu, um Tooltips anzuzeigen bzw. zu verstecken.
 * 
 * Ablauf:
 * 1. Sobald das DOM vollständig geladen ist, werden alle `.tooltip-btn`-Elemente abgerufen.
 * 2. Für jedes gefundene Tooltip-Element:
 *    - Wird bei `mouseover` ein `data-tooltip-visible="true"` Attribut gesetzt (zeigt den Tooltip an).
 *    - Wird bei `mouseout` das `data-tooltip-visible` Attribut entfernt (versteckt den Tooltip).
 * 
 * Beispiel:
 * <button class="tooltip-btn" data-tooltip="Hier ist mein Tooltip">Hover mich</button>
 * 
 * Optional kann die Anzeige über CSS mit `[data-tooltip-visible="true"]` gesteuert werden.
 */
document.addEventListener('DOMContentLoaded', () => {
    // 1. Alle Elemente mit der Klasse `.tooltip-btn` abrufen
    const tooltipButtons = document.querySelectorAll('.tooltip-btn');

    // 2. Event Listener für Mouseover & Mouseout hinzufügen
    tooltipButtons.forEach(button => {
        // Tooltip anzeigen, wenn der Mauszeiger über den Button fährt
        button.addEventListener('mouseover', () => {
            button.setAttribute('data-tooltip-visible', 'true');
        });

        // Tooltip verstecken, wenn der Mauszeiger den Button verlässt
        button.addEventListener('mouseout', () => {
            button.removeAttribute('data-tooltip-visible');
        });
    });
});


/**
 * toggleVarInfo
 * 
 * Beschreibung:
 * Diese Funktion zeigt oder versteckt die Detailinformationen (`varInfo`) einer Variable.
 * Zusätzlich wird der zugehörige Button aktualisiert, indem er eine `active` oder `inactive` 
 * Klasse erhält, um den aktuellen Status visuell darzustellen.
 * 
 * Parameter:
 * @param {string} dumpId - Die eindeutige ID des `varInfo`-Containers, dessen Sichtbarkeit geändert werden soll.
 * 
 * Ablauf:
 * 1. Das `varInfo`-Element anhand der ID `{dumpId}-varInfo` abrufen.
 * 2. Den Button ermitteln, der die Funktion `toggleVarInfo()` für diesen `dumpId` aufruft.
 * 3. Falls das `varInfo`-Element versteckt ist:
 *    - Es sichtbar machen (`display: block`)
 *    - Den Button als `active` markieren
 * 4. Falls das `varInfo`-Element sichtbar ist:
 *    - Es verstecken (`display: none`)
 *    - Den Button als `inactive` markieren
 * 
 * Beispiel:
 * toggleVarInfo("myDump"); // Zeigt oder versteckt die Detailinformationen von "myDump".
 */
function toggleVarInfo(dumpId) {
    // 1. Den `varInfo`-Container anhand der übergebenen ID abrufen
    var varInfo = document.getElementById(dumpId + '-varInfo');

    // 2. Den zugehörigen Button abrufen, der diese Funktion aufruft
    var button = document.querySelector('[onclick="toggleVarInfo(\'' + dumpId + '\')"]');

    // Sicherstellen, dass `varInfo` und `button` existieren, um Fehler zu vermeiden
    if (!varInfo || !button) return;

    // 3. Falls das `varInfo`-Element aktuell versteckt ist, es anzeigen
    if (varInfo.style.display === 'none' || varInfo.style.display === '') {
        varInfo.style.display = 'block';  // Container sichtbar machen
        button.classList.remove('inactive');  // Inaktiven Status entfernen
        button.classList.add('active');  // Aktiven Status setzen
    } 
    // 4. Falls das `varInfo`-Element sichtbar ist, es verstecken
    else {
        varInfo.style.display = 'none';  // Container ausblenden
        button.classList.remove('active');  // Aktiven Status entfernen
        button.classList.add('inactive');  // Inaktiven Status setzen
    }
}


/**
 * toggleVarHistory
 * 
 * Beschreibung:
 * Diese Funktion zeigt oder versteckt die Historie (`varHistory`) einer Variable.
 * Zusätzlich wird der zugehörige Button aktualisiert, indem er eine `active`-Klasse 
 * erhält oder entfernt wird, um den aktuellen Status visuell darzustellen.
 * 
 * Parameter:
 * @param {string} historyId - Die eindeutige ID des `varHistory`-Containers, dessen Sichtbarkeit geändert werden soll.
 * 
 * Ablauf:
 * 1. Das `varHistory`-Element anhand der ID `{historyId}-varHistory` abrufen.
 * 2. Den Button ermitteln, der die Funktion `toggleVarHistory()` für diesen `historyId` aufruft.
 * 3. Falls das `varHistory`-Element versteckt ist:
 *    - Es sichtbar machen (`display: block`)
 *    - Den Button als `active` markieren
 * 4. Falls das `varHistory`-Element sichtbar ist:
 *    - Es verstecken (`display: none`)
 *    - Den Button als `inactive` markieren
 * 
 * Beispiel:
 * toggleVarHistory("myHistory"); // Zeigt oder versteckt die Historie von "myHistory".
 */
function toggleVarHistory(historyId) {
    // 1. Den `varHistory`-Container anhand der übergebenen ID abrufen
    var varHistory = document.getElementById(historyId + '-varHistory');

    // 2. Den zugehörigen Button abrufen, der diese Funktion aufruft
    var button = document.querySelector('[onclick="toggleVarHistory(\'' + historyId + '\')"]');

    // Sicherstellen, dass `varHistory` und `button` existieren, um Fehler zu vermeiden
    if (!varHistory || !button) return;

    // 3. Falls das `varHistory`-Element aktuell versteckt ist, es anzeigen
    if (varHistory.style.display === 'none' || varHistory.style.display === '') {
        varHistory.style.display = 'block';  // Container sichtbar machen
        button.classList.add('active');      // Aktiven Status setzen
    } 
    // 4. Falls das `varHistory`-Element sichtbar ist, es verstecken
    else {
        varHistory.style.display = 'none';   // Container ausblenden
        button.classList.remove('active');   // Aktiven Status entfernen
    }
}


 


/**
 * toggleVarHistory
 * 
 * Beschreibung:
 * Diese Funktion zeigt oder versteckt die Historie (`varHistory`) einer Variablen.
 * Zusätzlich wird der zugehörige Button aktualisiert, indem er eine `active`-Klasse 
 * erhält oder entfernt wird, um den aktuellen Status visuell darzustellen.
 * 
 * Parameter:
 * @param {string} historyId - Die eindeutige ID des `varHistory`-Containers, dessen Sichtbarkeit geändert werden soll.
 * 
 * Ablauf:
 * 1. Das `varHistory`-Element anhand der ID `{historyId}-varHistory` abrufen.
 * 2. Den Button ermitteln, der die Funktion `toggleVarHistory()` für diesen `historyId` aufruft.
 * 3. Falls das `varHistory`-Element versteckt ist:
 *    - Es sichtbar machen (`display: block`).
 *    - Den Button als `active` markieren.
 * 4. Falls das `varHistory`-Element sichtbar ist:
 *    - Es verstecken (`display: none`).
 *    - Den Button als `inactive` markieren.
 * 
 * Beispiel:
 * toggleVarHistory("myHistory"); // Zeigt oder versteckt die Historie von "myHistory".
 */
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.fdv-wrapper'); // Wrapper anhand der Klasse auswählen

    let isDragging = false;
    let offsetX, offsetY;

    // Beim Start des Ziehens
    wrapper.addEventListener('mousedown', function(e) {
        // Verhindern, dass das Event auf den inneren Elementen ausgelöst wird
        if (e.target === wrapper) {
            isDragging = true;
            offsetX = e.clientX - wrapper.getBoundingClientRect().left;
            offsetY = e.clientY - wrapper.getBoundingClientRect().top;
            document.body.style.cursor = 'move'; // Cursor ändern
        }
    });

    // Während des Ziehens
    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            const newX = e.clientX - offsetX;
            const newY = e.clientY - offsetY;
            wrapper.style.left = newX + 'px';
            wrapper.style.top = newY + 'px';
        }
    });

    // Beim Loslassen des Ziehens
    document.addEventListener('mouseup', function() {
        isDragging = false;
        document.body.style.cursor = 'default'; // Cursor zurücksetzen
    });
});