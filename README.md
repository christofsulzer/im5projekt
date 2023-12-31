# Newsdata Fetcher und Sentimentanalyse
Meine Absicht in diesem Projekt war, auf Social Media und Newsplattformen liegende Artikel per API abzugreifen, in einer lokalen Datenbank abzulegen und einer Fake-News Analyse zu unterziehen. Der letzte Teil war ein etwas hoher Anspruch - ein System, um automatisiert Desinformation zu identifizieren, lässt sich  - wenn überhaupt - nicht im Rahmen dieses Projekts realisieren. Stattdessen habe ich eine Funktion implementiert, die Nutzer*innen für jeden in der Datenbank abgelegten Text einen Sentimentsanalyse duchführen lässt.

Die Sentimentsanalyse ist eine computergestützte Technik, die dazu dient, emotionale Stimmungen und subjektive Meinungen in Texten zu erkennen und zu interpretieren, um das Verständnis von Gefühlen in geschriebener Sprache zu ermöglichen. Die daraus resultierende Bewertung ("Score") bezieht sich auf die allgemeine emotionale Ausrichtung eines Textes (positiv, negativ oder neutral), während das Ausmass ("Magnitude") die emotionale Stärke oder Intensität dieser Stimmung misst, unabhängig von ihrem positiven oder negativen Charakter.

Die finale Version arbeitet mit Texten von Newsdata.io. Diese Plattform bietet eine API zum Beziehen von News-Artikeln von weltweiten Online-Publikationen. Zur Sentimentsanalyse wird die API von Google verwendet.

Funktionen von newsdata.html:
- Beim Aufrufen werden alle in der DB abgelegten News-Artikel gezeigt
- Die Langtexte werden abgekürzt angezeigt, der komplette Nachrichtentext ist mit "show more" anzeigbar 
- Möglichkeit, mit den Buttons Next/Previous durch die Einträge zu paginieren
- Möglichkeit, mittels Eingabe eines Suchbegriffs nach Artikeln auf Newsdata zu suchen, diese werden in der DB abgelegt und oben an der Liste angezeigt
- Möglichkeit, für jeden Text einzeln eine Sentimentsanalyse ausführen zu lassen

Vorab wurden Tests aufgrund Daten von Mastodon durchgeführt, sowie mit der Plattform Claimbuster (https://idir.uta.edu/claimbuster/api/).

Umgesetzt sind alle Funktionalitäten basierend auf den jeweiligen öffentlich zugänglichen API's, die Code-Erstellung wurde unterstützt von ChatGPT bzw. CoPilot in VS Code.

Newdata.io Update vom 15.12.23:
Die folgenden Änderungen werden die Funktionalität meines Codes beeinflussen:

Effective from January 15, 2024, the following adjustments will be made to the free plan:

- Latest News Delay: Latest news updates will be delayed by 12 hours for free plan users
- Content and Image URLs: Full content and image URLs will no longer be included in the response results for free plan users
- Timeframe Parameter: The "timeframe" parameter will be removed from the free plan.

  
