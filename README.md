# PowerCockpit 1.0 Build 12

Minimalistisches Anzeigemodul für die Symcon-Kachelvisualisierung. Präfix: PWRCP. Eine bis drei Phasen, gemeinsame Spannungsanzeigen und frei benannte Leistungspanels. Keine Gerätekommunikation, Archivabfrage oder Energiebilanz; alle Mess- und Statistikwerte stammen aus zugewiesenen Variablen.

## Installation und Update

Das TAR ist ein vollständiges Quellpaket für eine eigene Modulbibliothek / ein Git-Repository. Über Symcons Module Control laden und eine Instanz „PowerCockpit“ erstellen. Voraussetzung: Symcon 8.2; natives Fullscreen wird ab 9.1 aktiviert.

Beim Update aus Build 1–6 wird die bisherige Konfiguration einmalig als erstes Panel übernommen. Variablen, Statistiken, Farben und Absicherung bleiben erhalten. Die alten Properties bleiben intern für die Migration erhalten, werden aber nicht mehr im Formular angeboten. Absichtlich geleerte Panellisten werden nicht wieder mit dem alten Panel gefüllt.

Alte deaktivierte Navigationsziele mit Wert 0 werden bei der Übernahme auf 1 umgestellt. Anschliessend gilt: **1 = kein Ziel, 0 = Rootkategorie**. Diese Umstellung verhindert, dass zuvor deaktivierte Instrumente plötzlich zur Rootkategorie navigieren.

## Konfiguration

### Modul-Einstellungen

- Aktualitätsgrenze in Sekunden; Standard 30, 0 deaktiviert die Prüfung.
- Einheit aller Leistungsvariablen einschliesslich Statistik: W oder kW. Die Einstellung gilt für alle Panels. Spannungen immer in V, Stromstärken in A.
- Nennspannung in V. Sie verändert die frei konfigurierten Skalenzonen nicht automatisch und wird nicht als zusätzliche Skalenmarke gezeichnet.
- Anzahl Phasen: 1, 2 oder 3. Anzeige A, A/B oder A/B/C. Nicht verwendete Phasen erscheinen nicht im Bearbeitungsformular; vorhandene Zuordnungen bleiben für eine spätere Umstellung gespeichert.

### Spannungen

„Spannungen anzeigen“ schaltet die gemeinsame Spannungsgruppe für alle Panels ein oder aus. Variablen, Statistikzuordnungen und Skalenzonen bleiben gespeichert.

Pro Phase Messwertvariable, optionales Navigationsziel sowie eine eingeklappte Statistikgruppe für Minimum (↓), Maximum (↑) und Durchschnitt (Ø). Die Spannungen sind für alle Panels gemeinsam und wechseln nicht mit dem Panel.

Die gemeinsamen Spannungsskalenzonen beginnen am einstellbaren Wert „Von“. In jeder Listenzeile werden „Bis“ und die Farbe über den nativen Farbpicker festgelegt. Jede Zone schliesst an die vorherige an; das letzte Ende ist der Skalenendwert. Mindestens eine Zone, streng aufsteigende Endwerte.

### Leistungspanels

Die Liste zeigt nur den Namen. Über das Bearbeiten-Symbol öffnet sich das gegliederte Panel-Formular. Panels lassen sich hinzufügen, löschen und per Ziehen sortieren. Änderungen werden erst mit „Übernehmen“ gespeichert.

Pro Panel:

- Name, optionales Navigationsziel und gemeinsame Absicherung pro Phase in A. Bei nur einem Panel darf der Name leer bleiben; die Namenszeile entfällt dann.
- Gesamtleistung: separat ein-/ausblendbar; Messwertquelle, Navigationsziel, optionale Min/Max/Ø-Variablen und eigene Skalenzonen.
- Phasenleistungen: separat ein-/ausblendbar; entsprechende Variablen je Phase und gemeinsame Skalenzonen innerhalb dieses Panels. Der Schalter blendet auch die zugehörigen Stromstärken aus.
- Optionale Stromstärken je Phase. Ohne Zuordnung verschwinden Werte und Abstand vollständig. Bei zugewiesener Stromstärke und Absicherung grösser 0 erscheint auch die prozentuale Auslastung.

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

Ausgeblendete Bereiche beanspruchen keinen eigenen Platz, keine Überschrift und erzeugen keine Aktualitätswarnung. Alle Zuordnungen bleiben gespeichert. Für eine stabile Position der gemeinsamen Spannungen wird die Höhe des grössten Leistungsinhalts unter Panels mit derselben sichtbaren Gruppenkombination berücksichtigt. Ein Panel mit ausgeblendeter Gesamtleistung reserviert keinen Platz für die Gesamtleistung anderer Panels. Bei unterschiedlicher Gruppenkombination darf die Spannungsgruppe beim Panelwechsel ihre Höhe ändern.

## Anzeige und Bedienung

Reihenfolge: Panelname, Gesamtleistung, Phasenleistungen, gemeinsame Spannungen. Die Spannungsgruppe steht unterhalb des wechselnden Leistungsbereichs. Die Gesamtleistung steht abhängig von Breite und Höhe links oder oberhalb der Phasenleistungen. Die Darstellung passt die Instrumente proportional in die verfügbare Fläche ein. Das Gesamtleistungsinstrument wird auf höchstens die doppelte Breite eines Detailinstruments begrenzt; bei unterschiedlichen Detailbreiten ist die kleinere massgebend. Ohne zugewiesenes Detailinstrument entfällt diese relative Grenze. Für kleine Kacheln bleibt Fullscreen hilfreich. Wenn die verfügbare Höhe für lesbare Überschriften und Instrumente nicht ausreicht, ist die Instrumentenfläche vertikal scrollbar; horizontale Wischgesten wechseln weiterhin die Panels.

Panelname und Gruppenüberschriften haben eine feste Bildschirm-Schriftgrösse von 24 px, Phasenbezeichnungen von 20 px. Die native Kacheltitelgrösse wird vom HTML-SDK nicht über eine hier verwendbare dokumentierte Schnittstelle geliefert; 24 px dient als fester Näherungswert. Chevrons werden ebenfalls nicht mit den Instrumenten verkleinert. Einzelbeschriftungen unter den kleinen Spannungs- und Leistungsinstrumenten entfallen; stattdessen stehen die gemeinsamen Überschriften „Phasenleistungen“ und „Spannungen“ über den Phasenbezeichnungen. „Gesamtleistung“ erscheint ebenfalls als feste 24-px-Gruppenüberschrift oberhalb des Instruments. Alle Beschriftungen innerhalb der Instrumente entfallen. Der SVG-Platzbedarf unter den Zahlen ist entsprechend reduziert. Dezente Trennlinien in der Theme-Farbe gliedern die Gruppen; vor den Überschriften steht mehr Abstand als danach. Schriftdefinitionen der Messwerte, Statistikwerte und Skalenmarkierungen bleiben unverändert. Lange Panelnamen werden bei Platzmangel gekürzt; der vollständige Text ist als Tooltip verfügbar.

Mehrere Panels: immer sichtbare Chevrons sowie Wechsel durch horizontales Wischen mit Finger oder gedrückter linker Maustaste. Am Ende geht es wieder zum ersten Panel. Der Wechsel gilt nur für den jeweiligen Client und wird durch Messwertaktualisierungen nicht zurückgesetzt. Bei nur einem Panel entfallen Chevrons, Zähler und Wechselgesten.

Die Spannungsanzeigen werden beim Wechsel nicht neu aufgebaut. Die Leistungspanels liegen getrennt im DOM; nur das aktuelle Panel ist sichtbar und bedienbar. Für den Leistungsbereich wird genügend Höhe für Panels mit derselben sichtbaren Gruppenkombination reserviert, damit unterschiedliche Statistikzeilen die Spannungsinstrumente dort nicht verschieben. Anders aufgebaute Panels bestimmen den Platzbedarf nicht mit.

Klick auf ein Instrument öffnet ausschliesslich sein eigenes optionales Ziel. Klick auf die freie Fläche des Leistungsbereichs öffnet das Ziel des aktuellen Panels. Freie Flächen im gemeinsamen Spannungsbereich haben kein Panelziel. Eine erkannte Wischbewegung löst beim Loslassen kein Navigationsziel aus. Scripts werden nicht direkt ausgeführt.

Alle Zahlen und neutralen Linien verwenden die Symcon-Theme-Farbe `--content-color`. Der Hintergrund bleibt transparent. Nur die Skalenzonen tragen ihre konfigurierten Farben. Ein kurzer Pfeil markiert den aktuellen Wert; die Zahl steht gross im Halbkreis. Leistungsreferenzwerte sind tangential ausserhalb des Bogens beschriftet; Spannung ohne Skalenbeschriftung.

Spannung wird linear dargestellt. Leistung verläuft bis 100 W linear, darüber logarithmisch. Bei aktivierter bidirektionaler Skala werden negative Leistungen links von Null dargestellt; sonst bleibt der Pfeil am linken Anschlag. Werte ausserhalb der Skala werden ebenfalls unverändert angezeigt; der Pfeil wird begrenzt und „Unter/Über Skala“ eingeblendet.

### Statistikmarker auf dem Farbband

Zugewiesene, gültige Minimum- und Maximumwerte erscheinen zusätzlich als kleine, leere Dreiecke am äusseren Rand des Farbbandes. Die Dreiecke zeigen in entgegengesetzte Richtungen; der Durchschnitt erscheint als leere Raute. Die bisherigen Statistikzahlen bleiben unverändert erhalten. Alle Marker folgen der Symcon-Schriftfarbe, und es gibt dafür keine zusätzliche Konfigurationsoption.

Die Marker verwenden exakt dieselbe lineare bzw. logarithmische Position wie der aktuelle Wert, auch bei negativen Leistungen. Nahe beieinanderliegende Statistikmarker werden radial versetzt, ohne ihre Winkelposition zu verändern. Bei gleicher Position kann der gefüllte Messwertpfeil einen Statistikmarker überdecken; die Statistikzahl bleibt verfügbar. Werte ausserhalb der Skala werden am Anschlag markiert. Ihre tatsächlichen Werte stehen weiterhin in der Statistikzeile. Nicht zugewiesene oder ungültige Statistikwerte erhalten keinen Marker.

## Aktualität und fehlende Werte

Abfrage alle 5 Sekunden. Die Aktualitätsprüfung verwendet `VariableUpdated`, nicht `VariableChanged`. Unveränderte, regelmässig aktualisierte Werte bleiben aktuell. Veraltete Messwerte bleiben lesbar und werden mit reduzierter Pfeilintensität, gepunkteter Unterstreichung und Hinweis markiert. Statistikvariablen haben keinen eigenen Timeout.

Nicht zugewiesene Messwerte werden ausgeblendet. Gelöschte oder nicht numerische Variablen bleiben als „–“ ohne Pfeil erkennbar. Bei mehreren konfigurierten Phasen bleibt die Phasenzuordnung auch dann erhalten, wenn ein einzelnes Instrument nicht zugewiesen ist. Ohne Leistungspanels bleiben die gemeinsamen Spannungen sichtbar.

## Vorschau und Beispielzuordnungen

`preview.html` direkt im Browser öffnen. Enthält drei Beispielpanels einschliesslich Batterie mit negativen und positiven Phasenwerten. Theme, Phasenanzahl, Sichtbarkeit der Gruppen und Leistungsrichtung lassen sich umschalten. Das zweite Panel blendet die Gesamtleistung aus; Statistikbeispiele zeigen die neuen Marker. Keine Live-Daten oder Verbindung zu Symcon.

Santo André, bestehende Shelly-Variablen:

| Messwert | Variablen-ID |
|---|---:|
| Gesamtwirkleistung | 55050 |
| Spannung A / B / C | 49028 / 55259 / 40735 |
| Wirkleistung A / B / C | 57531 / 54540 / 20221 |
| Stromstärke A / B / C | 20839 / 33178 / 29315 |

Standardwerte: 220 V, 40 A, drei Phasen; Spannung 160–280 V, Gesamtleistung 0–30 kW, Phasenleistung 0–10 kW. Für Cassanis beispielsweise Nennspannung 230 V und grüne Spannungsskalenzone 207–253 V einstellen; Leistungsskalenzonen und Absicherung ebenfalls anpassen.

## Prüfung

PHP-Syntax und Backend mit nachgebildeter Symcon-API geprüft: Migration, Referenzen, Panel-IDs, eine/zwei/drei Phasen, Einheitenumrechnung, leere Listen und ungültige Skalenzonen. Build 12: Chromium-Prüfungen in sechs Formaten (360×420 bis 1500×650), mit gemessenen Bildschirm-Schriftgrössen und Prüfung auf abgeschnittene Instrumente. Weitere Prüfungen für Chevrons, Maus- und emulierte Touch-Gesten, Klickunterdrückung, Instrumentennavigation, Theme-Farbe, Panelreihenfolge, gemeinsame Spannungen sowie Hoch- und Querformate.

Zusätzlich für Build 11 geprüft: vorzeichenbehaftete und getrennte Richtungswerte in W/kW, Vorzeichenumkehr inklusive Min/Max, fehlende und ungültige Richtungswerte, ältere Zeitstempel, Referenzen auf die zweite Variable, symmetrische Skalenzonen und alle 24 Kombinationen von drei Anzeigegruppen mit einer/zwei/drei Phasen. Ein aus dem PHP-Backend erzeugter Netto-Datensatz wurde auch im Browser geprüft.

**Noch offen:** nativer Konfigurationsdialog und Live-Betrieb einschliesslich realer bidirektionaler Messgeräte und iOS/Android. Version 1.0 Build 12 ist dafür ein Teststand, keine bereits mit echten bidirektionalen Quellen validierte Freigabe. Eine gezielte Anleitung steht in `TESTING.md`.

Build 12 zusätzlich: 60 Vergleiche zwischen einer reduzierten Einzelansicht und derselben Ansicht neben einem anders aufgebauten Panel, verteilt auf fünf Bildschirmformate, drei Phasenanzahlen und vier Leistungskombinationen. Markerpositionen, Formen, Clamping, ungültige Werte, Live-Änderungen und Theme-Farbe geprüft. Die PHP-Datenverarbeitung ist gegenüber Build 11 unverändert; nur die Build-Beschriftung wurde erhöht.
