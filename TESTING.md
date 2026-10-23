# PowerCockpit 1.0 Build 12: Test mit echten Messwerten

Dieser Build ist mit nachgebildeter Symcon-API und Browser-Testdaten geprüft. Reale bidirektionale Quellen sowie der native Konfigurationsdialog sind noch nicht validiert.

## Konvention und Einrichtung

Positiv = Bezug, negativ = Abgabe aus Sicht des Hauses. Batterieentladung liefert ans Haus und ist positiv; Laden bezieht vom Haus und ist negativ. Geräteseitige Vorzeichen bei Bedarf pro Leistungszuordnung umkehren.

1. Eine Instanz mit dem bisherigen Build aktualisieren. Zuordnungen und Darstellung sollten unverändert bleiben. Ein neues Panel für den bidirektionalen Test anlegen.
2. Globale Leistungseinheit prüfen (W oder kW, auch für alle Statistikvariablen).
3. Gesamtleistung bzw. gewünschte Phasen zuweisen. Für die jeweiligen Gruppen „Bidirektionale Skala“ aktivieren. Die positiven Skalenzonen werden automatisch auf negative Werte gespiegelt.
4. Bei getrennten Richtungsvariablen „Zwei Variablen“ wählen: Bezug und Abgabe zuweisen. Beide müssen positive Beträge oder Null liefern. Eine stillstehende Richtung benötigt ebenfalls einen gültigen Wert.
5. Wenden die Quellen unterschiedliche Vorzeichenkonventionen an, „Vorzeichen umkehren“ je Total/Phase verwenden.

## Erwartete Werte

| Quelle | Messwerte in W | Erwartete Anzeige |
|---|---|---|
| Einzelvariable | +2400 | 2.40 kW rechts von Null |
| Einzelvariable | -2400 | -2.40 kW links von Null |
| Zwei Variablen | Bezug 300, Abgabe 2700 | -2.40 kW links von Null |
| Zwei Variablen, umgekehrt | Bezug 300, Abgabe 2700 | 2.40 kW rechts von Null |
| Zwei Variablen | Beide 0 | 0 W, Pfeil senkrecht in der Mitte |
| Zwei Variablen | Eine fehlt/ist gelöscht | „–“, kein Pfeil |
| Zwei Variablen | Ein Richtungswert negativ | „–“, kein Pfeil |

Für kW-Quellen entsprechend 2.4 bzw. 0.3 und 2.7 verwenden. Die Skalenzonen bleiben in W.

## Im Betrieb prüfen

- Netzbezug/-abgabe und Batterieladen/-entladen mit der Geräteanzeige vergleichen. Bei Richtungswechseln muss die Skala fest stehen bleiben, nur Pfeil und Zahlen ändern sich.
- Gesamtwert und einzelne Phasen vergleichen, besonders bei gleichzeitig gegenläufigen Phasen. Das Modul summiert oder verteilt keine Phasenwerte.
- Nullnähe und kleine Werte (z.B. ±10 W, ±100 W) beobachten. Endwerte und Werte ausserhalb der Skala bleiben als Zahl korrekt; der Pfeil wird begrenzt.
- Beide Richtungsvariablen müssen regelmässig aktualisiert werden. Stoppt eine, muss nach Ablauf der Aktualitätsgrenze die Nettoanzeige als veraltet erscheinen. Asynchrone Updates können kurzfristige Zwischenwerte verursachen.
- Nur Netto-Statistikwerte zuordnen. Bei Min -5 kW, Max +3 kW und Avg -1 kW muss die Vorzeichenumkehr Min -3 kW, Max +5 kW und Avg +1 kW anzeigen.
- Gesamtleistung und Phasenleistungen pro Panel ausschalten, Spannungen global ausschalten. Überschriften verschwinden, Wertezuordnungen bleiben nach Wiedereinschalten erhalten. Phasenleistungen aus blendet auch Stromstärken aus.
- Ein zweites Panel mit Gesamtleistung einrichten und im ersten Gesamtleistung ausblenden: Zwischen Phasenleistung und Spannung darf kein grosser Leerraum für die ausgeblendete Gesamtleistung bleiben. Bei unterschiedlich aufgebauten Panels darf die gemeinsame Spannung beim Wechsel rücken; bei gleicher Gruppenkombination bleibt ihre Position stabil.
- Zugewiesene Min/Max-Statistiken erscheinen zusätzlich als leere Dreiecke auf dem Farbband, Avg als Raute. Werte mit den Statistikzahlen vergleichen, insbesondere bei negativen Leistungen, Null, gleichen/nahen Werten und ausserhalb der Skala. Fehlende/ungültige Werte dürfen keinen Marker haben.
- Panelwechsel mit Chevrons, Finger und Maus sowie Navigationsziele prüfen. Gemeinsame Spannungen sollen beim Panelwechsel stehen bleiben.
- Formular nach Speichern neu öffnen: Anzeigeoptionen, Quellenmodus, zweite Variable, Vorzeichenumkehr und Skalen müssen erhalten bleiben. Auch 1-/2-/3-phasige Einstellungen und Fullscreen prüfen.

Für Rückmeldungen hilfreich: Symcon-Version, Browser/App und Gerät, Quellenmodus, Leistungseinheit, betroffene Variablen samt Rohwerten und Zeitstempeln, Erwartung und Screenshot. Variable IDs allein erklären die Ursache nicht.
