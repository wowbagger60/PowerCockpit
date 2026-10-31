# PowerCockpit 1.1 Build 2

Minimalistisches Anzeigemodul für die Symcon-Kachelvisualisierung. Präfix: PWRCP. Eine bis drei Phasen, gemeinsame Spannungsanzeigen und frei benannte Leistungspanels. Keine Gerätekommunikation, Archivabfrage oder Energiebilanz; alle Mess- und Statistikwerte stammen aus zugewiesenen Variablen.

## Installation und Update

Das TAR ist ein vollständiges Quellpaket für eine eigene Modulbibliothek / ein Git-Repository. Über Symcons Module Control laden und eine Instanz „PowerCockpit“ erstellen. Voraussetzung: Symcon 8.2; natives Fullscreen wird ab 9.1 aktiviert.

Beim Update aus 0.1 Build 1–6 wird die bisherige Konfiguration einmalig als erstes Panel übernommen. Variablen, Statistiken, Farben und Absicherung bleiben erhalten. Die alten Properties bleiben intern für die Migration erhalten, werden aber nicht mehr im Formular angeboten. Absichtlich geleerte Panellisten werden nicht wieder mit dem alten Panel gefüllt.

Alte deaktivierte Navigationsziele mit Wert 0 werden bei der Übernahme auf 1 umgestellt. Anschliessend gilt: **1 = kein Ziel, 0 = Rootkategorie**. Diese Umstellung verhindert, dass zuvor deaktivierte Instrumente plötzlich zur Rootkategorie navigieren.

## Konfiguration

### Sprache

Englische Quelltexte und `PowerCockpit/locale.json` mit deutschen Übersetzungen. Formular, Panel-Editor, Kacheltexte und eigene Fehlermeldungen verwenden die Symcon-Systemsprache (Deutsch oder Englisch; für andere Sprachen englischer Fallback). Es gibt keinen zusätzlichen Sprachschalter im Modul. Individuelle Panelnamen und Variablenzuordnungen werden nicht übersetzt. Die Zahlenformatierung bleibt wie bisher.

### Modul-Einstellungen

- Aktualitätsgrenze in Sekunden; Standard 30, 0 deaktiviert die Prüfung.
- Einheit aller Leistungsvariablen einschliesslich Statistik: W oder kW. Die Einstellung gilt für alle Panels. Spannungen immer in V, Stromstärken in A.
- Anzahl Phasen: 1, 2 oder 3. Anzeige A, A/B oder A/B/C. Nicht verwendete Phasen erscheinen nicht im Bearbeitungsformular; vorhandene Zuordnungen bleiben für eine spätere Umstellung gespeichert.

### Spannungen

„Spannungen anzeigen“ schaltet die gemeinsame Spannungsgruppe für alle Panels ein oder aus. Variablen, Statistikzuordnungen und Skalenzonen bleiben gespeichert.

Pro Phase Messwertvariable, optionales Navigationsziel sowie eine eingeklappte Statistikgruppe für Minimum, Durchschnitt und Maximum. Die Spannungen sind für alle Panels gemeinsam und wechseln nicht mit dem Panel.

Optional lässt sich eine Variable für die Netzfrequenz in Hz zuweisen. Der Wert erscheint klein in der Überschrift „Spannungen“, mit zwei Nachkommastellen und derselben Aktualitätsprüfung wie die Messwerte. Er ist nur sichtbar, wenn die Spannungsgruppe angezeigt wird.

Die gemeinsamen Spannungsskalenzonen beginnen am einstellbaren Wert „Von“. In jeder Listenzeile werden „Bis“ und die Farbe über den nativen Farbpicker festgelegt. Jede Zone schliesst an die vorherige an; das letzte Ende ist der Skalenendwert. Mindestens eine Zone, streng aufsteigende Endwerte.

### Leistungspanels

Die Liste zeigt nur den Namen. Über das Bearbeiten-Symbol öffnet sich das gegliederte Panel-Formular. Panels lassen sich hinzufügen, löschen und per Ziehen sortieren. Änderungen werden erst mit „Übernehmen“ gespeichert.

Pro Panel:

- Name und gemeinsame Absicherung pro Phase in A. Bei nur einem Panel darf der Name leer bleiben; die Namenszeile entfällt dann.
- Gesamtleistung: separat ein-/ausblendbar; Messwertquelle, Navigationsziel, optionale Min/Avg/Max-Variablen und eigene Skalenzonen.
- Phasenleistungen: separat ein-/ausblendbar; entsprechende Variablen je Phase und gemeinsame Skalenzonen innerhalb dieses Panels. Der Schalter blendet auch die zugehörigen Stromstärken aus.
- Optionale Stromstärken je Phase. Ohne Zuordnung verschwinden die Werte. Bei gleich aufgebauten Panels wird ein von einem anderen Panel benötigter Stromstärkeplatz zur Ausrichtung freigehalten. Bei zugewiesener Stromstärke und Absicherung grösser 0 erscheint auch die prozentuale Auslastung.

Skalenzonen der Leistung werden **immer in W** angegeben, unabhängig von der Einheit der Quellvariablen. Gesamtleistung und Phasenleistung haben getrennte Zonenlisten. Erste Zone beginnt bei 0 W; alle weiteren schliessen an. Das letzte Ende bestimmt den Maximalwert, etwa 30000, 50000 oder 100000 W.

Statistikwerte werden nur angezeigt, nicht berechnet. Dafür können beispielsweise Variablen aus PeriodicStatistics zugewiesen werden. Mindestleistung bleibt optional; bei vorzeichenbehafteter Leistung kann sie die stärkste Abgabe im Statistikzeitraum darstellen.

## Positive und negative Leistung

Konvention: **positiv = Bezug, negativ = Abgabe**, immer aus Sicht des dargestellten Systems. Für das Haus ist Batterieentladung positiver Bezug, Batterieladung negative Abgabe. Das Modul kennt keine speziellen Quellenarten und berechnet keine Bilanz zwischen Panels.

Für Gesamtleistung und jede einzelne Phase sind unabhängig zwei Zuordnungen möglich:

- **Eine Variable (mit Vorzeichen):** Der Wert wird direkt angezeigt. „Abgabe“ wird in diesem Modus ignoriert.
- **Zwei Variablen (Bezug minus Abgabe):** Beide Richtungsvariablen müssen zugewiesen sein und nicht negative Leistungswerte liefern. Das Modul zeigt ihre Differenz. Fehlende, gelöschte, nicht numerische oder negative Richtungswerte ergeben „–“ ohne Pfeil. Ist keine der beiden Variablen zugewiesen, entfällt das Instrument. Eine nicht zugewiesene Richtung gilt ausdrücklich nicht als Null; bei Bedarf eine echte Nullvariable zuweisen.

„Vorzeichen umkehren“ gilt pro Zuordnung und kehrt den einzelnen Messwert bzw. die Differenz um. Die globale Einheit W/kW gilt für beide Richtungsvariablen und sämtliche Leistungsstatistiken. Es handelt sich um momentane Leistung, nicht um Energie in kWh.

Die **bidirektionale Skala** wird getrennt für Gesamtleistung und Phasenleistungen pro Panel aktiviert. Null steht dann fest in der Mitte, negative Werte links, positive rechts. Die konfigurierten positiven Skalenzonen werden mit denselben Farben auf die negative Seite gespiegelt. Die Grenzen beziehen sich damit auf den Betrag der Leistung. Die Skalierung bleibt auf beiden Seiten bis 100 W linear, darüber logarithmisch. Zur Vermeidung dicht stehender Texte erscheinen Referenzen bei Null und den kW-Dekaden. Eine einzelne Leistungsvariable darf auch bei deaktivierter bidirektionaler Skala negativ sein; die Zahl bleibt korrekt, der Pfeil wird dann wie bisher am linken Skalenende begrenzt.

Statistikvariablen enthalten immer die vorzeichenbehaftete **Nettoleistung vor der optionalen Vorzeichenumkehr**. Aus getrennten Richtungs-Minima, -Maxima oder -Durchschnitten berechnet das Modul keine Netto-Statistik. Bei Vorzeichenumkehr tauschen Minimum und Maximum ihre Rollen und werden negiert; der Durchschnitt wird negiert. Bei unvollständigen Statistikzuordnungen wandert auch die vorhandene Zuordnung zur jeweils passenden Extremwertanzeige.

Bei zwei Richtungsvariablen bestimmt der ältere Aktualisierungszeitpunkt die Aktualität der Nettoleistung. Das Modul kann unabhängig aktualisierte Sensorwerte nicht zeitlich synchronisieren. Die Gesamtleistung kann gleichzeitig gegenläufige Phasenleistungen verdecken; die Phasenwerte werden deshalb unabhängig angezeigt und nicht aus dem Total abgeleitet.

Ausgeblendete Bereiche beanspruchen keinen eigenen Platz, keine Überschrift und erzeugen keine Aktualitätswarnung. Alle Zuordnungen bleiben gespeichert. Bei Panels mit derselben sichtbaren Gruppenkombination werden die Plätze für Messinstrument, Hinweis, Statistik und Stromstärke pro Gruppe einzeln abgeglichen. Auch mehrzeilige Statistikwerte und einseitig vorhandene Aktualitätshinweise werden berücksichtigt. Dadurch bleiben Gauge-Grössen, Überschriften, Trenner und die gemeinsame Spannungsgruppe beim Panelwechsel stabil. Ein Panel mit ausgeblendeter Gesamtleistung reserviert keinen Platz für die Gesamtleistung anderer Panels. Bei unterschiedlicher Gruppenkombination darf die Spannungsgruppe beim Panelwechsel ihre Höhe ändern.

## Anzeige und Bedienung

Reihenfolge: Panelname, Gesamtleistung, Phasenleistungen, gemeinsame Spannungen. Die Spannungsgruppe steht unterhalb des wechselnden Leistungsbereichs. Die Gesamtleistung steht abhängig von Breite und Höhe links oder oberhalb der Phasenleistungen. Die Darstellung passt die Instrumente proportional in die verfügbare Fläche ein. Das Gesamtleistungsinstrument wird auf höchstens die doppelte Breite eines Detailinstruments begrenzt; bei unterschiedlichen Detailbreiten ist die kleinere massgebend. Ohne zugewiesenes Detailinstrument entfällt diese relative Grenze. Für kleine Kacheln bleibt Fullscreen hilfreich. Wenn die verfügbare Höhe für lesbare Überschriften und Instrumente nicht ausreicht, ist die Instrumentenfläche vertikal scrollbar; horizontale Wischgesten wechseln weiterhin die Panels.

Panelname und Gruppenüberschriften haben eine feste Bildschirm-Schriftgrösse von 24 px, Phasenbezeichnungen von 20 px. Die native Kacheltitelgrösse wird vom HTML-SDK nicht über eine hier verwendbare dokumentierte Schnittstelle geliefert; 24 px dient als fester Näherungswert. Chevrons werden ebenfalls nicht mit den Instrumenten verkleinert. Einzelbeschriftungen unter den kleinen Spannungs- und Leistungsinstrumenten entfallen; stattdessen stehen die gemeinsamen Überschriften „Phasenleistungen“ und „Spannungen“ über den Phasenbezeichnungen. „Gesamtleistung“ erscheint ebenfalls als feste 24-px-Gruppenüberschrift oberhalb des Instruments. Alle Beschriftungen innerhalb der Instrumente entfallen. Der SVG-Platzbedarf unter den Zahlen ist entsprechend reduziert. Dezente Trennlinien in der Theme-Farbe gliedern die Gruppen; vor den Überschriften steht mehr Abstand als danach. Schriftdefinitionen der Messwerte, Statistikwerte und Skalenmarkierungen bleiben unverändert. Lange Panelnamen werden bei Platzmangel gekürzt; der vollständige Text ist als Tooltip verfügbar.

Mehrere Panels: immer sichtbare Chevrons sowie Wechsel durch horizontales Wischen mit Finger oder gedrückter linker Maustaste. Am Ende geht es wieder zum ersten Panel. Der Wechsel gilt nur für den jeweiligen Client und wird durch Messwertaktualisierungen nicht zurückgesetzt. Bei nur einem Panel entfallen Chevrons, Zähler und Wechselgesten.

Die Spannungsanzeigen werden beim Wechsel nicht neu aufgebaut. Die Leistungspanels liegen getrennt im DOM; nur das aktuelle Panel ist sichtbar und bedienbar. Statistik-, Hinweis- und Stromstärkeplätze werden pro Gruppe zwischen Panels mit derselben sichtbaren Gruppenkombination abgeglichen. Fehlende Zusatzwerte lassen ihre Plätze leer, damit auch die nachfolgenden Gruppen ihre Position behalten. Anders aufgebaute Panels bestimmen den Platzbedarf nicht mit.

Klick auf ein Instrument öffnet ausschliesslich sein eigenes optionales Ziel. Freie Flächen haben kein Ziel; das frühere Navigationsziel pro Panel entfällt ab 1.1 Build 2. Eine erkannte Wischbewegung löst beim Loslassen kein Navigationsziel aus. Scripts werden nicht direkt ausgeführt.

Alle Zahlen und neutralen Linien verwenden die Symcon-Theme-Farbe `--content-color`. Der Hintergrund bleibt transparent. Nur die Skalenzonen tragen ihre konfigurierten Farben. Ein kurzer Pfeil markiert den aktuellen Wert; die Zahl steht gross im Halbkreis. Leistungsreferenzwerte sind tangential ausserhalb des Bogens beschriftet; Spannung ohne Skalenbeschriftung.

Spannung wird linear dargestellt. Leistung verläuft bis 100 W linear, darüber logarithmisch. Bei aktivierter bidirektionaler Skala werden negative Leistungen links von Null dargestellt; sonst bleibt der Pfeil am linken Anschlag. Werte ausserhalb der Skala werden ebenfalls unverändert angezeigt; der Pfeil wird begrenzt und „Unter/Über Skala“ eingeblendet.

### Statistikmarker auf dem Farbband

Zugewiesene, gültige Minimum- und Maximumwerte erscheinen zusätzlich als kleine, leere Dreiecke am äusseren Rand des Farbbandes. Die Dreiecke zeigen in entgegengesetzte Richtungen; der Durchschnitt erscheint als leere Raute. Die Statistikzeile verwendet dieselben leeren Symbole: ▽ Minimum, ◇ Durchschnitt, △ Maximum. Die Reihenfolge ist Min – Avg – Max; nicht zugewiesene Werte entfallen. Die Symbole werden als SVG dargestellt; Zahlen und Schriftgrössen bleiben erhalten. Alle Marker folgen der Symcon-Schriftfarbe, und es gibt dafür keine zusätzliche Konfigurationsoption.

Die Marker verwenden exakt dieselbe lineare bzw. logarithmische Position wie der aktuelle Wert, auch bei negativen Leistungen. Nahe beieinanderliegende Statistikmarker werden radial versetzt, ohne ihre Winkelposition zu verändern. Bei gleicher Position kann der gefüllte Messwertpfeil einen Statistikmarker überdecken; die Statistikzahl bleibt verfügbar. Werte ausserhalb der Skala werden am Anschlag markiert. Ihre tatsächlichen Werte stehen weiterhin in der Statistikzeile. Nicht zugewiesene oder ungültige Statistikwerte erhalten keinen Marker.

## Aktualität und fehlende Werte

Die Anzeige wird bei jeder Aktualisierung einer zugewiesenen Variable neu übertragen; kurz aufeinanderfolgende Aktualisierungen werden innerhalb von 250 ms zusammengefasst. Zusätzlich erfolgt jede Minute eine Übertragung, auch ohne neue Werte. Die Aktualitätsprüfung verwendet `VariableUpdated`, nicht `VariableChanged`. Unveränderte, regelmässig aktualisierte Werte bleiben aktuell. Veraltete Messwerte bleiben lesbar und werden mit reduzierter Pfeilintensität, gepunkteter Unterstreichung und Hinweis markiert. Statistikvariablen haben keinen eigenen Timeout.

Nicht zugewiesene Messwerte werden ausgeblendet. Gelöschte oder nicht numerische Variablen bleiben als „–“ ohne Pfeil erkennbar. Bei mehreren konfigurierten Phasen bleibt die Phasenzuordnung auch dann erhalten, wenn ein einzelnes Instrument nicht zugewiesen ist. Ohne Leistungspanels bleiben die gemeinsamen Spannungen sichtbar.

## Vorschau und Beispielzuordnungen

`preview.html` direkt im Browser öffnen. Enthält drei Beispielpanels einschliesslich Batterie mit negativen und positiven Phasenwerten. Theme, Phasenanzahl, Sichtbarkeit der Gruppen und Leistungsrichtung lassen sich umschalten. Die ersten beiden Panels zeigen dieselben Gruppen mit und ohne Statistiken/Stromstärken und demonstrieren deren feste Ausrichtung. Deutsch und Englisch können in der lokalen Vorschau umgeschaltet werden. Keine Live-Daten oder Verbindung zu Symcon.

Santo André, bestehende Shelly-Variablen:

| Messwert | Variablen-ID |
|---|---:|
| Gesamtwirkleistung | 55050 |
| Spannung A / B / C | 49028 / 55259 / 40735 |
| Wirkleistung A / B / C | 57531 / 54540 / 20221 |
| Stromstärke A / B / C | 20839 / 33178 / 29315 |

Standardwerte für neue Instanzen: 40 A pro Phase, drei Phasen; Spannung 160–280 V mit grünem Bereich 207–253 V. Neue Panels: Gesamtleistung 0–30 kW und Phasenleistung 0–10 kW. Die grünen Zonen reichen bis 22,08 bzw. 7,36 kW, gelb bis 27,6 bzw. 9,2 kW. Diese Leistungsreferenzen entsprechen 80 % und 100 % von 230 V × 40 A × Phasenanzahl für den dreiphasigen Standard bei Leistungsfaktor 1; sie sind keine automatisch berechnete Belastungsgrenze. Weder Nennspannung, Sicherung noch Phasenanzahl passen die frei konfigurierten Leistungszonen automatisch an.

Beim Update bleiben gespeicherte Sicherungen, Farbgrenzen, Variablen und Panelkonfigurationen erhalten. Für 220-V-Netze wie in Bahia die grüne Spannungszone beispielsweise auf 198–242 V setzen. Die frühere Einstellung „Nennspannung“ hatte keine Wirkung auf die Anzeige und entfällt ab 1.1 Build 2. Neue Panels erhalten die neuen Standardzonen auch in bestehenden Instanzen; vorhandene Panels behalten ihre gespeicherten Zonen.

## Prüfung

Version 1.1 Build 1:

- PHP-Syntax sowie Datenverarbeitung mit nachgebildeter Symcon-API geprüft: neue Defaults, Erhalt gespeicherter und historischer Konfigurationen, Referenzen, Vorzeichenumkehr und Netto-Statistikwerte.
- Deutsche und englische Konfigurationsformulare, Panel-Editoren, Kacheltexte und eigene Konfigurationsfehler geprüft. Aus dem PHP-Backend erzeugte DE/EN-Snapshots auch im Browser verwendet.
- 63 Kombinationen aus sieben Bildschirmformaten (360×420 bis 1500×650), einer/zwei/drei Phasen und drei sichtbaren Leistungskombinationen: gleiche Positionen und Grössen der Gruppen zwischen Panels mit/ohne Statistik, Hinweisen und Stromstärken; keine abgeschnittenen Instrumente.
- Explizit deaktivierte Gruppen geben ihren Platz frei. Live-Änderungen an Statistikwerten, SVG-Symbole und Reihenfolge, Theme-Farbe, emulierte Touch-Gesten ohne Navigationsfehler und Fehlerbehandlung geprüft.

Version 1.1 Build 2 ist nicht in Symcon geprüft, nur die Vorschau im Browser: ereignisgesteuerte Aktualisierung, Netzfrequenz, Entfernen von Panelziel und Nennspannung.

**Noch offen:** nativer Konfigurationsdialog und Live-Test der neuen Version in Symcon, insbesondere Rückmeldung zu realen bidirektionalen Quellen und mobilen Clients. Die Datenverarbeitung für bidirektionale Leistung bleibt gegenüber 1.0 Build 12 unverändert. Hinweise für den Tester stehen in `TESTING.md`.
