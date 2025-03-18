function toggleElement(id) {
    var element = document.getElementById(id);
    if (element) {
        element.classList.toggle("hidden");
        var toggleButton = document.getElementById("btn-" + id);
        toggleButton.innerText = element.classList.contains("hidden") ? "[+]" : "[-]";
    }
}

function expandAll(dumpId) {
    document.querySelectorAll("#container-" + dumpId + " .dump-content").forEach(el => el.classList.remove("hidden"));
    document.querySelectorAll("#container-" + dumpId + " .dump-toggler").forEach(btn => btn.innerText = "[-]");
}

function closeAll(dumpId) {
    document.querySelectorAll("#container-" + dumpId + " .dump-content").forEach(el => el.classList.add("hidden"));
    document.querySelectorAll("#container-" + dumpId + " .dump-toggler").forEach(btn => btn.innerText = "[+]");
}


function highlightSearch(dumpId) {
    var keyword = document.getElementById("search-" + dumpId).value;
    var context = document.querySelector("#container-" + dumpId);

    // Holen der aktuellen Status der Toggle-Buttons durch Abfragen der `active`-Klasse an den IDs
    var isWholeWord = document.getElementById('whole-word-toggle-' + dumpId).classList.contains('active');
    var isCaseSensitive = document.getElementById('case-sensitive-toggle-' + dumpId).classList.contains('active');

    // Typecasting zu Boolean (sicherstellen, dass sie als Boolean interpretiert werden)
    isWholeWord = !!isWholeWord;  // Typecast zu Boolean
    isCaseSensitive = !!isCaseSensitive;  // Typecast zu Boolean

    var instance = new Mark(context);
    
    // Entfernen der bisherigen Markierungen
    instance.unmark({
        done: function() {
            if (keyword) {
                // Optionen an mark.js übergeben
                var options = {
                    caseSensitive: isCaseSensitive,  // Berücksichtige Groß-/Kleinschreibung
                    wholeWord: isWholeWord,          // Ganze-Wort-Suche aktivieren
                    done: function() {
                        console.log('Markierung abgeschlossen');
                    }
                };

                // Markieren der gefundenen Begriffe mit den Optionen
                instance.mark(keyword, options);
            }
        }
    });
}







function highlightSearch____(dumpId) {
    var keyword = document.getElementById("search-" + dumpId).value;
    var context = document.querySelector("#container-" + dumpId);
    var instance = new Mark(context);
    instance.unmark({
        done: function() {
            if (keyword) instance.mark(keyword);
        }
    });
}

function clearSearch_old(dumpId) {
    document.getElementById("search-" + dumpId).value = "";
    highlightSearch(dumpId);
}


function toggleWholeWord(dumpId) {
    var button = document.getElementById('whole-word-toggle-' + dumpId);
    var isWholeWord = button.classList.contains('active');

    // Schaltzustand umschalten
    if (isWholeWord) {
        button.classList.remove('active');
    } else {
        button.classList.add('active');
    }

    // Logik für die "Ganzes Wort"-Suche (z.B. per RegExp)
    highlightSearch(dumpId);
}

function toggleCaseSensitive(dumpId) {
    var button = document.getElementById('case-sensitive-toggle-' + dumpId);
    var isCaseSensitive = button.classList.contains('active');

    // Schaltzustand umschalten
    if (isCaseSensitive) {
        button.classList.remove('active');
    } else {
        button.classList.add('active');
    }

    // Logik für die "Groß-/Kleinschreibung"-Suche (z.B. per RegExp)
    highlightSearch(dumpId);
}

function clearSearch(dumpId) {
    // Löscht den Inhalt des Eingabefeldes
    var searchInput = document.getElementById('search-' + dumpId);
    searchInput.value = '';

    // Entfernt alle Highlights
    var elementsToSearch = document.querySelectorAll('.dump-item');  // Beispiel: alle zu durchsuchenden Elemente
    elementsToSearch.forEach(function(element) {
        element.classList.remove('highlight');
    });

    // Setzt die Filteroptionen zurück (z.B. für "Ganzes Wort" und "Groß-/Kleinschreibung")
    document.getElementById('whole-word-toggle-' + dumpId).classList.remove('active');
    document.getElementById('case-sensitive-toggle-' + dumpId).classList.remove('active');
}



function toggleDump(dumpId) {
    let container = document.getElementById("container-" + dumpId);
    let symbol = document.getElementById("toggle-" + dumpId);
    let titleBar = document.getElementById("title-bar-" + dumpId); // Titel-Bar auswählen

    if (container.classList.contains("hidden")) {
        container.classList.remove("hidden");
        symbol.innerHTML = "-"; // Symbol auf Minus setzen
        symbol.classList.add("open"); // Symbol bekommt die "open"-Klasse
        titleBar.classList.add("open"); // Titel-Bar bekommt die "open"-Klasse
    } else {
        container.classList.add("hidden");
        symbol.innerHTML = "+"; // Symbol auf Plus setzen
        symbol.classList.remove("open"); // "open"-Klasse vom Symbol entfernen
        titleBar.classList.remove("open"); // "open"-Klasse von der Titel-Bar entfernen
    }
}



document.addEventListener('DOMContentLoaded', () => {
    const tooltipButtons = document.querySelectorAll('.tooltip-btn');

    tooltipButtons.forEach(button => {
        button.addEventListener('mouseover', () => {
            button.setAttribute('data-tooltip-visible', 'true');
        });

        button.addEventListener('mouseout', () => {
            button.removeAttribute('data-tooltip-visible');
        });
    });
});




function toggleVarInfo(dumpId) {
    var varInfo = document.getElementById(dumpId + '-varInfo');  // Zugriff auf den varInfo-Container
    var button = document.querySelector('[onclick="toggleVarInfo(\'' + dumpId + '\')"]');  // Zugriff auf den Button
    
    // Überprüfen, ob varInfo derzeit angezeigt wird (active) oder nicht (inactive)
    if (varInfo.style.display === 'none') {
        // Setze auf sichtbar und aktiviere den Button
        varInfo.style.display = 'block';  // Zeigt den Container an
        button.classList.remove('inactive');  // Entfernt die 'inactive' Klasse
        button.classList.add('active');  // Fügt die 'active' Klasse hinzu
    } else {
        // Setze auf unsichtbar und deaktiviere den Button
        varInfo.style.display = 'none';  // Versteckt den Container
        button.classList.remove('active');  // Entfernt die 'active' Klasse
        button.classList.add('inactive');  // Fügt die 'inactive' Klasse hinzu
    }
}



function toggleVarHistory(historyId) {
    var varHistory = document.getElementById(historyId + '-varHistory');
    var button = document.querySelector('[onclick="toggleVarHistory(\'' + historyId + '\')"]');

    // Toggle Sichtbarkeit der History
    if (varHistory.style.display === 'none' || varHistory.style.display === '') {
        varHistory.style.display = 'block';  // Zeigt den Container an
        button.classList.add('active');      // Aktiviert das Icon
    } else {
        varHistory.style.display = 'none';   // Versteckt den Container
        button.classList.remove('active');   // Deaktiviert das Icon
    }
}


