# PowerCockpit

Deutsch | [English](README.md)

Modul für die IP-Symcon Kachel-Visualisierung von Ueli Schweizer / Cassanis Automation.
Präfix: `PWRCP`.

PowerCockpit zeigt Leistung, Stromstärke und Spannung eines ein- bis dreiphasigen
Anschlusses als Zeigerinstrumente in einer Kachel. Mehrere Leistungspanels, etwa
Hausverbrauch, PV oder Batterie, lassen sich per Wischen oder Pfeil wechseln. Das
Modul liest ausschliesslich zugewiesene Variablen: keine Gerätekommunikation,
keine Archivabfrage, keine Energiebilanz.

> **Beta-Version.** Positive und negative Leistung (Einspeisung, Batterie) ist
> bisher nur mit Testdaten geprüft, nicht mit echten Anlagen. Rückmeldungen sind
> sehr willkommen, eine Anleitung steht in [TESTING.md](../TESTING.md).

## Inhaltsverzeichnis

1. [Voraussetzungen](#1-voraussetzungen)
2. [Installation und erster Test](#2-installation-und-erster-test)
3. [Modul-Einstellungen](#3-modul-einstellungen)
4. [Spannungen und Netzfrequenz](#4-spannungen-und-netzfrequenz)
5. [Leistungspanels](#5-leistungspanels)
6. [Positive und negative Leistung](#6-positive-und-negative-leistung)
7. [Anzeige und Bedienung](#7-anzeige-und-bedienung)
8. [Aktualität und fehlende Werte](#8-aktualität-und-fehlende-werte)
9. [Unterstützung](#9-unterstützung)
10. [Änderungen](#10-änderungen)

## 1. Voraussetzungen

- IP-Symcon ab Version 8.2.
- PowerCockpit wird als normale Kachel angezeigt. Ab Version 9.1 kann es
  zusätzlich im Vollbild-Modus angezeigt werden.
- Messwerte als Integer- oder Float-Variablen, zum Beispiel aus der Instanz
  eines Energiezählers.

## 2. Installation und erster Test

1. Bibliothek über den Module Store oder das Module Control installieren und eine
   PowerCockpit-Instanz anlegen.
2. Unter «Modul-Einstellungen» die Anzahl Phasen und die Einheit der
   Leistungsvariablen (W oder kW) wählen.
3. Unter «Spannungen» je Phase die Spannungsvariable zuweisen.
4. Das erste Leistungspanel ist bereits angelegt. Über das Bearbeiten-Symbol
   Gesamtleistung, Phasenleistungen und optional Stromstärken zuweisen und
   übernehmen.
5. Instanz in der Visualisierung als Kachel anzeigen.

## 3. Modul-Einstellungen

| Einstellung | Standard | Bedeutung |
|---|---|---|
| Aktualitätsgrenze | 30 s | Ältere Werte gelten als nicht aktuell; 0 schaltet die Prüfung ab. |
| Einheit der Leistungsvariablen | W | W oder kW, gilt für alle Leistungs- und Statistikvariablen aller Panels. |
| Anzahl Phasen | 3 | 1, 2 oder 3 (Phase A, A/B oder A/B/C). |

Spannungen werden immer in V, Stromstärken in A erwartet. Zuordnungen von
ausgeblendeten Phasen bleiben gespeichert.

Konfigurationsformular, Kacheltexte und Fehlermeldungen folgen der
Spracheinstellung von IP-Symcon (Deutsch oder Englisch, für andere Sprachen
Englisch). Panelnamen werden nicht übersetzt.

## 4. Spannungen und Netzfrequenz

Die Spannungsgruppe gilt für alle Panels und bleibt beim Panelwechsel stehen.
«Spannungen anzeigen» blendet sie aus; die Zuordnungen bleiben erhalten.

Pro Phase gibt es eine Messwertvariable, ein optionales Navigationsziel und
optionale Statistikvariablen für Minimum, Durchschnitt und Maximum.

Optional lässt sich eine Variable für die **Netzfrequenz** in Hz zuweisen. Der
Wert erscheint klein in der Überschrift «Spannungen», mit zwei Nachkommastellen
und derselben Aktualitätsprüfung wie die übrigen Messwerte. Er ist nur sichtbar,
wenn die Spannungsgruppe angezeigt wird.

Die Skalenzonen beginnen beim Wert «Von». Jede Zeile legt «Bis» und eine Farbe
fest; jede Zone schliesst an die vorherige an, das letzte «Bis» ist das
Skalenende. Standard für neue Instanzen:

| Bereich | Farbe |
|---|---|
| 160–207 V | gelb |
| 207–253 V | grün (230 V ±10 %) |
| 253–280 V | rot |

Für 220-V-Netze die grüne Zone beispielsweise auf 198–242 V setzen.

## 5. Leistungspanels

Die Liste «Leistungspanels» zeigt die Panelnamen. Das Bearbeiten-Symbol öffnet
das Panel-Formular. Panels lassen sich hinzufügen, löschen und per Drag & Drop
sortieren. Änderungen werden mit «Übernehmen» gespeichert.

Pro Panel:

- **Name** und **Absicherung pro Phase** in A. Bei nur einem Panel darf der Name
  leer bleiben; die Namenszeile entfällt dann.
- **Gesamtleistung:** ein- und ausblendbar; Messwertquelle, Navigationsziel,
  optionale Statistik und eigene Skalenzonen.
- **Phasenleistungen:** ein- und ausblendbar, zusammen mit den Stromstärken;
  Variablen je Phase und gemeinsame Skalenzonen.
- **Stromstärken** (optional) je Phase. Bei einer Absicherung grösser 0
  erscheint zusätzlich die Auslastung in Prozent.

Die Skalenzonen der Leistung werden **immer in W** angegeben, unabhängig von der
Einheit der Variablen. Die erste Zone beginnt bei 0 W, das letzte «Bis» ist das
Skalenende. Die Skala ist bis 100 W linear, darüber logarithmisch.

Standardzonen für neue Panels:

| Instrument | grün bis | gelb bis | rot bis |
|---|---|---|---|
| Gesamtleistung | 22,08 kW | 27,6 kW | 30 kW |
| Phasenleistung | 7,36 kW | 9,2 kW | 10 kW |

Das entspricht 80 % und 100 % von 230 V × 40 A je Phase bei Leistungsfaktor 1.
Die Zonen passen sich nicht automatisch an Absicherung oder Phasenanzahl an.

Statistikwerte werden nur angezeigt, nicht berechnet. Geeignet sind
beispielsweise Variablen aus PeriodicStatistics.

## 6. Positive und negative Leistung

Konvention: **positiv = Bezug, negativ = Abgabe**, aus Sicht des dargestellten
Systems. Für das Haus ist Batterieentladung positiv, Batterieladung negativ.

Für die Gesamtleistung und jede Phase gibt es zwei Messwertquellen:

- **Eine Variable (mit Vorzeichen):** Der Wert wird direkt angezeigt.
- **Zwei Variablen (Bezug minus Abgabe):** Beide Variablen müssen zugewiesen
  sein und Werte ≥ 0 liefern; angezeigt wird die Differenz. Fehlt eine Variable
  oder ist ein Wert negativ, erscheint «–» ohne Zeiger. Eine nicht zugewiesene
  Richtung gilt nicht als Null.

«Vorzeichen umkehren» kehrt den Messwert bzw. die Differenz um, falls ein Gerät
die umgekehrte Konvention verwendet.

Die **bidirektionale Skala** wird pro Panel getrennt für Gesamt- und
Phasenleistung aktiviert. Null steht dann in der Mitte, negative Werte links.
Die Skalenzonen werden mit denselben Farben auf die negative Seite gespiegelt.
Ohne bidirektionale Skala wird eine negative Leistung korrekt als Zahl
angezeigt, der Zeiger bleibt am linken Anschlag.

Statistikvariablen enthalten die vorzeichenbehaftete Nettoleistung **vor** der
Vorzeichenumkehr. Bei Umkehr werden die Werte negiert, Minimum und Maximum
tauschen ihre Rollen.

Bei zwei Variablen bestimmt der ältere Zeitstempel die Aktualität. Die
Gesamtleistung kann gegenläufige Phasenleistungen verdecken; die Phasenwerte
werden deshalb unabhängig angezeigt und nicht aus der Gesamtleistung abgeleitet.

## 7. Anzeige und Bedienung

Reihenfolge von oben: Panelname, Gesamtleistung, Phasenleistungen, Spannungen.
Je nach Format steht die Gesamtleistung links oder oberhalb der Phasenleistungen.
Die Instrumente werden in die verfügbare Fläche eingepasst; bei sehr niedrigen
Kacheln wird die Fläche vertikal scrollbar.

Bei mehreren Panels wird über die Pfeile oder durch horizontales Wischen
gewechselt. Die Auswahl gilt pro Client und bleibt bei neuen Messwerten
erhalten. Gleich aufgebaute Panels halten Instrumente, Überschriften und
Spannungen an derselben Position.

Ein Klick auf ein Instrument öffnet dessen Navigationsziel. Freie Flächen haben
kein Ziel, und eine Wischbewegung löst keinen Klick aus.

Zahlen und Linien verwenden die Textfarbe des Symcon-Themes, der Hintergrund
bleibt transparent. Nur die Skalenzonen sind farbig.

Statistikwerte erscheinen als Zeile unter dem Instrument (▽ Minimum,
◇ Durchschnitt, △ Maximum) und mit denselben Symbolen auf dem Farbband. Nahe
beieinanderliegende Marker werden nach innen versetzt. Werte ausserhalb der
Skala werden als Zahl korrekt angezeigt; Zeiger und Marker stehen am Anschlag,
zusätzlich erscheint «Unter Skala» bzw. «Über Skala».

## 8. Aktualität und fehlende Werte

Die Kachel wird bei jeder Aktualisierung einer zugewiesenen Variable neu
übertragen. Kurz aufeinanderfolgende Aktualisierungen werden innerhalb von
250 ms zusammengefasst; zusätzlich erfolgt jede Minute eine Übertragung.

Die Aktualitätsprüfung verwendet den Zeitpunkt der letzten Aktualisierung, nicht
der letzten Änderung. Veraltete Werte bleiben lesbar und werden mit blasserem
Zeiger, gepunkteter Unterstreichung und dem Hinweis «Nicht aktuell» markiert.
Statistikvariablen haben keine eigene Aktualitätsprüfung.

Nicht zugewiesene Messwerte werden ausgeblendet. Gelöschte oder nicht numerische
Variablen erscheinen als «–» ohne Zeiger.

## 9. Unterstützung

Fragen und Fehlermeldungen bitte als
[GitHub-Issue](https://github.com/wowbagger60/PowerCockpit/issues) oder im
Symcon-Forum.

Dieses Modul ist kostenlos. Schenkungen als Unterstützung für den Autor werden
gerne angenommen: PayPal an ueli.schweizer@cassanis.fr

## 10. Änderungen

**1.1 Build 2:** Erste öffentliche Beta-Version.
