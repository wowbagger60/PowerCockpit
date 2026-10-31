# 1.1 Build 2 (31.10.2026)

- Aktualisierung ereignisgesteuert statt Abfrage alle 5 Sekunden: Jede Aktualisierung einer zugewiesenen Variable löst eine Übertragung aus, gebündelt innerhalb von 250 ms. Zusätzlich jede Minute eine Übertragung als Rückfallebene.
- Optionale Netzfrequenz (Hz) in der Überschrift der Spannungsgruppe, mit Aktualitätsprüfung.
- Nennspannung entfernt; sie hatte keine Wirkung auf die Anzeige.
- Navigationsziel pro Panel entfernt. Die Ziele der einzelnen Instrumente bleiben.
- Build-Anzeige im Formular aus library.json; ungenutzten Code entfernt.

# 1.1 Build 1 (27.10.2026)

- Bei Panels mit denselben sichtbaren Gruppen werden Statistik-, Hinweis- und Stromstärkeplätze pro Gruppe abgeglichen. Gauge-Grössen, Überschriften und Trenner bleiben auch bei fehlenden Zusatzwerten oder umgebrochenen Statistikzeilen ausgerichtet.
- Explizit deaktivierte Gruppen geben ihren Platz weiterhin frei. Gleich aufgebaute Panels reservieren bei Bedarf auch den allgemeinen Aktualitätshinweis.
- Statistikzeile mit leeren SVG-Dreiecken für Min/Max und Raute für Avg; neue Reihenfolge Min – Avg – Max. Zahlen und Schriftgrössen bleiben erhalten.
- Englische Quelltexte und locale.json mit deutschen Übersetzungen für Formular, Panel-Editor, Kachel und eigene Konfigurationsfehler. Sprache folgt Symcon; individuelle Panelnamen bleiben unverändert.
- Europäische Defaults für neue Instanzen: 230 V, grüne Spannungszone 207–253 V. Absicherung bleibt 40 A pro Phase; neue Leistungspanels erhalten passende Referenzen bis 9,2 kW pro Phase bzw. 27,6 kW total, grün bis 80 % dieser Referenz. Skalenenden bleiben 10/30 kW. Bestehende Konfigurationen bleiben erhalten.
- Vorschau und Testanleitung aktualisiert. PHP-Prüfungen und Browser-Geometrieprüfungen in 63 Kombinationen bestanden. Live-Rückmeldung zu bidirektionalen Quellen steht weiterhin aus.

# 1.0 Build 12 (23.10.2026)

- Layoutkorrektur beim Ausblenden von Gruppen: Panels mit anderer sichtbarer Gruppenkombination reservieren keine Höhe mehr für ausgeblendete Instrumente. Gleich aufgebaute Panels behalten eine gemeinsame Höhenberechnung für stabile Spannungspositionen.
- Platzbedarf, Layoutauswahl und Cache berücksichtigen die sichtbare Gruppenkombination; der Spannungstrenner entfällt bei reinem Spannungspanel.
- Minimum/Maximum zusätzlich als kleine leere Dreiecke und Durchschnitt als leere Raute auf dem Farbband. Zahlen und Schriftgrössen unverändert; keine neue Konfigurationsoption.
- Statistikmarker folgen der jeweiligen Skala einschliesslich negativer Werte und werden am Skalenende begrenzt. Dicht stehende Statistikmarker werden radial versetzt.
- Vorschau mit Statistikmarkern und reduziertem Panel ergänzt; Browserregression und 60 gezielte Layoutvergleiche bestanden. Backend gegenüber Build 11 unverändert.

# 1.0 Build 11 (23.10.2026)

- Gesamtleistung und Phasenleistungen je Panel sowie gemeinsame Spannungen separat ein-/ausblendbar; Zuordnungen bleiben erhalten. Der Phasenschalter umfasst die zugehörigen Stromstärken.
- Pro Leistungszuordnung eine vorzeichenbehaftete Variable oder zwei nicht negative Richtungsvariablen (Bezug minus Abgabe), mit optionaler Vorzeichenumkehr.
- Bidirektionale Skala separat für Gesamt- und Phasenleistungen: Null in der Mitte, negative Werte links; bestehende Skalenzonen und Farben werden gespiegelt. Positive Standardanzeigen bleiben unverändert.
- Externe Netto-Statistikwerte werden bei Vorzeichenumkehr korrekt negiert und Min/Max getauscht. Keine Berechnung von Netto-Extremwerten aus Richtungsstatistiken.
- Unvollständige oder ungültige Variablenpaare ergeben keinen scheinbar gültigen Nettowert. Der ältere Zeitstempel bestimmt die Aktualität.
- Vorschau um bidirektionales Batteriepanel und Schalter ergänzt. TESTING.md für den noch ausstehenden Live-Test.
- PHP-Backend, Migration und Browser mit Testdaten geprüft; reale bidirektionale Messgeräte und nativer Formulardialog sind noch zu testen.

# 0.1 Build 10 (23.10.2026)

- „Gesamtleistung“ als feste 24-px-Überschrift oberhalb des Instruments, konsistent zu den übrigen Gruppen.
- Gesamtleistungsinstrument auf höchstens die doppelte Breite eines zugewiesenen Detailinstruments begrenzt. Die Grenze berücksichtigt beide Detailgruppen und wirkt auch beim Layoutwechsel.
- Leeren SVG-Bereich der entfernten Instrumentenbeschriftungen gekürzt; Statistikwerte stehen näher am Instrument.
- Dezente horizontale Gruppentrenner in der Theme-Farbe, mit mehr Abstand vor als nach der Überschrift.
- Sehr niedrige Kacheln werden bei Bedarf vertikal scrollbar, damit feste Beschriftungen und Detailinstrumente lesbar bleiben.
- Numerische Schriftdefinitionen, Skalierung der Messwerte, Konfiguration und Variablenzuordnungen unverändert.

# 0.1 Build 9 (23.10.2026)

- Reihenfolge: Panelname, Gesamtleistung, Phasenleistungen, gemeinsame Spannungen.
- Panelname und Gruppenüberschriften mit festen 24 px; Phasenbezeichnungen mit festen 20 px, unabhängig von der Instrumentenskalierung.
- Gemeinsame Überschriften „Phasenleistungen“ und „Spannungen“ statt wiederholter kleiner Beschriftungen unter den Phaseninstrumenten.
- Schriftdefinitionen für Messwerte, Min/Max/Ø und Skalenmarkierungen unverändert; Gesamtleistungsbeschriftung bleibt erhalten.
- Alle Leistungspanels werden getrennt vorgehalten und nur das aktive Panel sichtbar und bedienbar geschaltet. Die gemeinsame Spannungsgruppe bleibt beim Wechsel stehen.
- Layout berücksichtigt feste Texthöhen und den grössten Platzbedarf aller Panels. Touch- und Mausbedienung bleiben auf den zusammenhängenden Leistungsbereich einschliesslich Panelkopf begrenzt.
- Konfigurationsschema, Variablenzuordnungen und Modul-IDs unverändert.

# 0.1 Build 8 (22.10.2026)

- Panelname, Panelzähler und Chevrons aus der skalierten Instrumentenfläche herausgelöst. Schrift- und Bediengrössen bleiben beim Vergrössern und Verkleinern der Kachel konstant.
- Die Layoutberechnung reserviert die tatsächliche Höhe der Panelnavigation, damit Instrumente und Titel nicht überlappen.
- Leerer Name bei einem Panel und eine leere Panelliste erzeugen weiterhin keine Titelzeile.
- Browserprüfung für drei stark unterschiedliche Kachelformate sowie Regressionstests für Maus, emulierte Touch-Gesten und gemeinsame Spannungen.

# 0.1 Build 7 (22.10.2026)

- Alle Zahlen verwenden die Symcon-Schriftfarbe. Farben bleiben auf Skalenzonen beschränkt.
- Explizite Unterstützung für eine, zwei oder drei Phasen.
- Gemeinsame Spannungsinstrumente und mehrere frei benannte Leistungspanels.
- Panelwechsel mit Touch-Swipe, linker Maustaste und Chevrons. Bei einem Panel entfallen Wechselbedienelemente; ein leerer Name beansprucht keinen Platz.
- Panelwechsel bleiben clientbezogen. Die Spannungsanzeigen bleiben beim Wechsel an derselben Position.
- Optionales Navigationsziel pro Panel ersetzt das Kachelziel. Instrumente behalten eigene Ziele. Wischbewegungen lösen keine Navigation aus.
- Konfiguration in Modul-Einstellungen, gemeinsame Spannungen und Panelliste mit gegliedertem Bearbeitungsdialog aufgeteilt.
- Pro Panel eigene Variablen für Gesamtleistung, Phasenleistung, Statistik und Stromstärken; eigene Absicherung und Leistungsskalenzonen. Die Leistungseinheit bleibt global.
- Bestehende Konfiguration wird einmalig als erstes Panel übernommen. Historische deaktivierte Ziele mit 0 werden dabei auf 1 umgestellt; danach ist Root-Ziel 0 ausdrücklich möglich.
- Gelöschte Panels werden nicht automatisch neu angelegt; eine reine Spannungsanzeige bleibt möglich.
- Browserprüfungen mit Maus, emuliertem Touch, Theme-Wechsel und verschiedenen Formaten; PHP-Prüfungen für Migration und Datenmodell. Nativer Formulartest und iOS/Android-Live-Test stehen aus.

# 0.1 Build 6 (22.10.2026)

- Leistungsreferenzwerte tangential zum Skalenbogen gedreht, mit gleichbleibendem Abstand ausserhalb der Farbskala.
- Abgewählte Variablen mit ID -1 werden wie ID 0 als nicht zugewiesen behandelt.
- Nicht zugewiesene Stromstärke wird vollständig ausgeblendet, einschliesslich des Abstands.

# 0.1 Build 5 (22.10.2026)

- Kompakte Halbkreisinstrumente mit kräftigen Farbbögen.
- Grosser Zahlenwert mit kleinerer Einheit im Innenraum.
- Kurzer Pfeil am Skalenbogen statt langem Zeiger.
- Spannung ohne Skalenstriche und -beschriftung; Leistung mit wenigen beschrifteten Referenzmarken ausserhalb des Bogens.
- Lineare Spannungsskala und bisherige logarithmische Leistungsskala unverändert.
- Statistikwerte, Variablenzuordnungen, Navigation, Transparenz und Theme-Farben bleiben erhalten.

# 0.1 Build 4 (22.10.2026)

- Statistikbeschriftung: ↓ für Minimum, ↑ für Maximum, Ø für Durchschnitt.
- Statistikschrift wächst mit dem jeweiligen Instrument; grössere Basisschrift für kleine Instrumente.
- Volle Bezeichnungen bleiben als Tooltip und für Screenreader verfügbar.
- Variablenzuordnung, IDs, Skalierung und Konfiguration bleiben erhalten.

# 0.1 Build 3 (22.10.2026)

- Feste Maximalbreiten der Gesamtleistungsanzeige entfernt.
- Anordnungsauswahl richtet sich bei zugewiesener Gesamtleistung nach der Grösse des Hauptinstruments; Grössensprünge beim Layoutwechsel korrigiert.
- Im Hochformat wächst die Gesamtleistung bis zur verfügbaren Breite, soweit die Höhe ausreicht.
- Repository-URL in library.json und module.json enthalten.
- Bestehende IDs und Konfiguration unverändert.

# 0.1 Build 2 (22.10.2026)

- Hintergrund immer transparent; Option „Weisser Hintergrund“ entfernt.
- Anordnung und Skalierung berücksichtigen Breite und Höhe; alle Instrumente bleiben im sichtbaren Kachelbereich.
- Native Titelzeile freigehalten. Panelname ist optional, neue Instanzen starten ohne Namen. Leerer Name erzeugt keine zusätzliche Zeile. Aktualitätswarnung bleibt unabhängig sichtbar.
- Skalenzonen kräftiger (7 statt 4 SVG-Einheiten), Mittelpunkt des Zeigers entfernt.
- Repository-URL in library.json eingetragen.
- Modul- und Bibliotheks-IDs unverändert.

# 0.1 Build 1 (22.10.2026)

Erster Testbuild: sieben SVG-Instrumente, frei zuweisbare Mess- und Statistikvariablen, drei unabhängige Skalenzonenlisten mit Farbpicker, logarithmische Leistungsskalen, Theme-Farben, optionale Navigation, Aktualitätsprüfung und natives Fullscreen ab Symcon 9.1.
