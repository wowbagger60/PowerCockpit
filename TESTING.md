# PowerCockpit Beta: Test mit echten Messwerten

Danke fürs Testen! Ich habe selbst keine Anlage mit Einspeisung; negative
Leistung ist deshalb nur mit Testdaten geprüft. Was fehlt, sind Erfahrungen
mit echten bidirektionalen Quellen: PV-Anlagen, Batteriespeicher, Wallboxen mit Rückspeisung oder Zähler,
die Bezug und Abgabe getrennt liefern.

Die Bedienung ist in der [Modulbeschreibung](PowerCockpit/README.de.md)
erklärt.

## Einrichtung

Konvention: positiv = Bezug, negativ = Abgabe, aus Sicht des Hauses.
Batterieentladung liefert ans Haus und ist positiv; Laden bezieht vom Haus und
ist negativ.

1. Instanz anlegen und die globale Leistungseinheit prüfen (W oder kW, gilt auch
   für alle Statistikvariablen).
2. Gesamtleistung bzw. Phasenleistungen zuweisen und für die jeweilige Gruppe
   «Bidirektionale Skala» aktivieren. Die Skalenzonen werden automatisch auf die
   negative Seite gespiegelt.
3. Liefert der Zähler Bezug und Abgabe getrennt, «Zwei Variablen» wählen und
   beide zuweisen. Beide müssen Werte ≥ 0 liefern, auch eine gerade ruhende
   Richtung.
4. Verwendet ein Gerät die umgekehrte Konvention, «Vorzeichen umkehren» für die
   betroffene Zuordnung aktivieren.

## Erwartete Werte

| Quelle | Messwerte in W | Erwartete Anzeige |
|---|---|---|
| Eine Variable | +2400 | 2.40 kW rechts von Null |
| Eine Variable | -2400 | -2.40 kW links von Null |
| Zwei Variablen | Bezug 300, Abgabe 2700 | -2.40 kW links von Null |
| Zwei Variablen, umgekehrt | Bezug 300, Abgabe 2700 | 2.40 kW rechts von Null |
| Zwei Variablen | beide 0 | 0 W, Zeiger senkrecht in der Mitte |
| Zwei Variablen | eine fehlt oder ist gelöscht | «–», kein Zeiger |
| Zwei Variablen | ein Wert negativ | «–», kein Zeiger |

Für kW-Quellen entsprechend 2.4 bzw. 0.3 und 2.7 verwenden. Die Skalenzonen
bleiben in W.

## Im Betrieb prüfen

- Netzbezug und Einspeisung sowie Laden und Entladen der Batterie mit der
  Anzeige des Geräts vergleichen. Bei Richtungswechseln bleibt die Skala stehen,
  nur Zeiger und Zahlen ändern sich.
- Gesamtwert und einzelne Phasen vergleichen, besonders bei gleichzeitig
  gegenläufigen Phasen. Das Modul summiert oder verteilt keine Phasenwerte.
- Kleine Werte um Null beobachten (etwa ±10 W, ±100 W).
- Die Anzeige soll Wertänderungen ohne spürbare Verzögerung folgen.
- Stoppt eine von zwei Richtungsvariablen, muss die Nettoanzeige nach Ablauf
  der Aktualitätsgrenze als «Nicht aktuell» erscheinen.
- Statistik: nur Netto-Werte zuordnen. Bei Min -5 kW, Avg -1 kW und Max +3 kW
  muss die Vorzeichenumkehr Min -3 kW, Avg +1 kW und Max +5 kW zeigen. Die
  Marker auf dem Farbband müssen zu den Zahlen passen.
- Wer eine Variable für die Netzfrequenz hat: Sie erscheint in der Überschrift
  «Spannungen».
- Mehrere Panels anlegen (zum Beispiel Haus, PV, Batterie) und mit Pfeilen,
  Finger und Maus wechseln. Gleich aufgebaute Panels sollen ihre Instrumente an
  derselben Stelle zeigen.
- Formular nach dem Speichern neu öffnen: Quellenmodus, zweite Variable,
  Vorzeichenumkehr und Skalenzonen müssen erhalten bleiben.
- Wenn möglich auch auf Smartphone oder Tablet und im Vollbild prüfen.

## Rückmeldung

Bitte als [GitHub-Issue](https://github.com/wowbagger60/PowerCockpit/issues)
oder im Symcon-Forum. Hilfreich sind:

- Symcon-Version, Browser oder App und Gerät
- Zählertyp, Quellenmodus und Leistungseinheit
- Rohwerte der betroffenen Variablen mit Zeitstempel
- Erwartete und tatsächliche Anzeige, am besten mit Screenshot
