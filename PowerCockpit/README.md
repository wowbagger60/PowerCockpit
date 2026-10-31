# PowerCockpit

[Deutsch](README.de.md) | English

Module for the IP-Symcon tile visualization by Ueli Schweizer / Cassanis Automation.
Prefix: `PWRCP`.

PowerCockpit shows power, current and voltage of a single- to three-phase
connection as gauges in a tile. Several power panels, for example household
consumption, PV or battery, can be switched by swiping or with the arrows. The
module only reads assigned variables: no device communication, no archive
queries, no energy balance.

> **Beta version.** Positive and negative power (feed-in, battery) has so far
> only been tested with sample data, not with real installations. Feedback is
> very welcome; a test guide (in German) is in [TESTING.md](../TESTING.md).

## Table of contents

1. [Requirements](#1-requirements)
2. [Installation and first test](#2-installation-and-first-test)
3. [Module settings](#3-module-settings)
4. [Voltages and mains frequency](#4-voltages-and-mains-frequency)
5. [Power panels](#5-power-panels)
6. [Positive and negative power](#6-positive-and-negative-power)
7. [Display and operation](#7-display-and-operation)
8. [Freshness and missing values](#8-freshness-and-missing-values)
9. [Support](#9-support)
10. [Changelog](#10-changelog)

## 1. Requirements

- IP-Symcon 8.2 or later.
- PowerCockpit is displayed as a regular tile. From version 9.1, it can also be
  shown in fullscreen mode.
- Measurements as integer or float variables, for example from an energy meter
  instance.

## 2. Installation and first test

1. Install the library via the Module Store or Module Control and create a
   PowerCockpit instance.
2. Under «Module settings», choose the number of phases and the unit of the
   power variables (W or kW).
3. Under «Voltages», assign the voltage variable for each phase.
4. The first power panel already exists. Use the edit icon to assign total
   power, phase power and optionally currents, then apply.
5. Show the instance as a tile in the visualization.

## 3. Module settings

| Setting | Default | Meaning |
|---|---|---|
| Freshness limit | 30 s | Older values are marked as not current; 0 disables the check. |
| Unit of power variables | W | W or kW, applies to all power and statistics variables of all panels. |
| Number of phases | 3 | 1, 2 or 3 (phase A, A/B or A/B/C). |

Voltages are always expected in V, currents in A. Assignments of hidden phases
are kept.

The configuration form, tile texts and error messages follow the IP-Symcon
language setting (German or English, English for other languages). Panel names
are not translated.

## 4. Voltages and mains frequency

The voltage group is shared by all panels and stays in place when switching
panels. «Show voltages» hides it; the assignments are kept.

Each phase has a measurement variable, an optional navigation target and
optional statistics variables for minimum, average and maximum.

Optionally, a variable for the **mains frequency** in Hz can be assigned. The
value appears small in the «Voltages» heading, with two decimals and the same
freshness check as the other measurements. It is only visible while the voltage
group is shown.

The scale zones start at the «From» value. Each row defines «To» and a color;
each zone starts where the previous one ends, and the last «To» is the end of
the scale. Default for new instances:

| Range | Color |
|---|---|
| 160–207 V | yellow |
| 207–253 V | green (230 V ±10 %) |
| 253–280 V | red |

For 220 V grids, set the green zone to 198–242 V, for example.

## 5. Power panels

The «Power panels» list shows the panel names. The edit icon opens the panel
form. Panels can be added, deleted and reordered by drag & drop. Changes are
saved with «Apply».

Per panel:

- **Name** and **fuse rating per phase** in A. With a single panel, the name may
  be left empty; the name row is then omitted.
- **Total power:** can be shown or hidden; measurement source, navigation
  target, optional statistics and its own scale zones.
- **Phase power:** can be shown or hidden, together with the currents;
  variables per phase and shared scale zones.
- **Currents** (optional) per phase. With a fuse rating above 0, the load in
  percent is shown as well.

Power scale zones are **always entered in W**, regardless of the variables'
unit. The first zone starts at 0 W, the last «To» is the end of the scale. The
scale is linear up to 100 W and logarithmic above.

Default zones for new panels:

| Gauge | green up to | yellow up to | red up to |
|---|---|---|---|
| Total power | 22.08 kW | 27.6 kW | 30 kW |
| Phase power | 7.36 kW | 9.2 kW | 10 kW |

This corresponds to 80 % and 100 % of 230 V × 40 A per phase at a power factor
of 1. The zones do not adapt automatically to the fuse rating or the number of
phases.

Statistics are only displayed, not calculated. Variables from PeriodicStatistics
are a suitable source, for example.

## 6. Positive and negative power

Convention: **positive = import, negative = export**, from the point of view of
the displayed system. For the house, battery discharge is positive and battery
charging is negative.

Total power and each phase support two measurement sources:

- **One variable (signed):** the value is displayed directly.
- **Two variables (import minus export):** both variables must be assigned and
  deliver values ≥ 0; the difference is displayed. If a variable is missing or
  a value is negative, «–» is shown without a needle. An unassigned direction
  does not count as zero.

«Invert sign» inverts the value or the difference, for devices that use the
opposite convention.

The **bidirectional scale** is enabled per panel, separately for total and phase
power. Zero is then in the center, negative values on the left. The scale zones
are mirrored to the negative side with the same colors. Without a bidirectional
scale, negative power is shown correctly as a number and the needle stays at
the left end.

Statistics variables contain the signed net power **before** sign inversion.
When inverted, the values are negated and minimum and maximum swap roles.

With two variables, the older timestamp determines freshness. Total power can
hide opposing phase powers; the phase values are therefore displayed
independently and not derived from the total.

## 7. Display and operation

Order from the top: panel name, total power, phase power, voltages. Depending on
the format, total power is placed to the left of or above the phase power. The
gauges are fitted into the available area; very low tiles become vertically
scrollable.

With several panels, switch with the arrows or by swiping horizontally. The
selection is local to each client and is kept when new values arrive. Panels
with the same structure keep gauges, headings and voltages in the same position.

Clicking a gauge opens its navigation target. Empty areas have no target, and a
swipe does not trigger a click.

Numbers and lines use the text color of the Symcon theme; the background stays
transparent. Only the scale zones are colored.

Statistics appear as a row below the gauge (▽ minimum, ◇ average, △ maximum) and
with the same symbols on the color band. Markers close to each other are moved
inwards. Values outside the scale are shown correctly as numbers; needle and
markers stop at the end of the scale, and «Below scale» or «Above scale» is
shown.

## 8. Freshness and missing values

The tile is pushed again on every update of an assigned variable. Updates in
quick succession are combined within 250 ms; in addition, the tile is pushed
once a minute.

The freshness check uses the time of the last update, not of the last change.
Outdated values remain readable and are marked with a paler needle, a dotted
underline and the note «Not current». Statistics variables have no freshness
check of their own.

Unassigned measurements are hidden. Deleted or non-numeric variables are shown
as «–» without a needle.

## 9. Support

Please report questions and issues in the
[PowerCockpit forum thread](https://community.symcon.de/t/tile-powercockpit-kachel-fuer-leistung-strom-und-spannung-beta-tester-mit-pv-oder-batterie-gesucht/144828) (German) or as a
[GitHub issue](https://github.com/wowbagger60/PowerCockpit/issues).

This module is free of charge. Donations to support the author are gratefully
accepted: PayPal to ueli.schweizer@cassanis.fr

## 10. Changelog

**1.1 Build 2:** First public beta version.
