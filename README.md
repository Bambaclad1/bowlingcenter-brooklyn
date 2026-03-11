# Bowling Center Brooklyn Documentatie
<p>
  <img src="./afbeeldingen/bowling_logo.png" alt="BowlingBaan Ouleh" width="200"/>

<!-- vscode-markdown-toc -->
* 1. [Project Uitleg](#ProjectUitleg)
* 2. [Gebruiker rollen](#Gebruikerrollen)
* 3. [Database Structuur](#DatabaseStructuur)
	* 3.1. [Users](#Users)
	* 3.2. [Reservations](#Reservations)
	* 3.3. [Player](#Player)
	* 3.4. [Lane](#Lane)
	* 3.5. [Rounds](#Rounds)
	* 3.6. [Score](#Score)
	* 3.7. [Payments](#Payments)
	* 3.8. [Relaties tussen tabellen](#Relatiestussentabellen)
* 4. [Setup guide](#Setupguide)
* 5. [Features](#Features)
* 6. [Tech stack](#Techstack)
* 7. [Security](#Security)
* 8. [Screenshots](#Screenshots)
* 9. [Known Issues](#KnownIssues)
* 10. [Verbetervoorstellen](#Verbetervoorstellen)
* 11. [Andere linkjes](#Anderelinkjes)
	* 11.1. [Documentatie](#Documentatie)
	* 11.2. [Kanban Board](#KanbanBoard)
	* 11.3. [Main Issues](#MainIssues)
	* 11.4. [Sub Issues](#SubIssues)

<!-- vscode-markdown-toc-config
	numbering=true
	autoSave=true
	/vscode-markdown-toc-config -->
<!-- /vscode-markdown-toc -->


##  1. <a name='ProjectUitleg'></a>Project Uitleg

Bowling Center Brooklyn is een web applicatie die gemaakt is om bowling baan reservaties te kunnen beheren. Het systeem zorgt voor minder handmatige boekingen door gebruikers en medewerkers reservaties te kunnen laten maken door een digitale platform.  
Daarnaast helpt het systeem om dubbele bookingen aan te maken en verbetert productiviteit voor de medewerkers.

```mermaid
flowchart LR

subgraph Zonder_Digitaal_Systeem
A[Klant belt bowlingcenter] --> B[Medewerker schrijft reservering op papier]
B --> C[Andere klant belt]
C --> D[Medewerker checkt papieren lijst]
D --> E[Menselijke fouten mogelijk]
E --> F[Dubbele boekingen]
F --> G[Chaos bij balie]
end

subgraph Met_Bowling_Reserveringssysteem
H[Klant opent website] --> I[Selecteert datum en tijd]
I --> J[Systeem controleert database]
J --> K{Baan beschikbaar?}

K -- Ja --> L[Reservering opslaan in database]
L --> M[Bevestiging tonen aan gebruiker]

K -- Nee --> N[Alternatieve tijd tonen]

M --> O[Medewerker kan reserveringen bekijken]
end
```

##  2. <a name='Gebruikerrollen'></a>Gebruiker rollen
Het systeem heeft drie soorten rollen:

Klant
- Kan een account aanmaken
- Kan reservaties aanmaken
- Kan eigen reservaties inzien

Medewerker
- Kan een reservatie aanmaken
- Kan alle reservaties inzien 

Owner
- Kan alles wat een medewerker kan doen 
- Kan gebruikers met elke rol aanmaken 

##  3. <a name='DatabaseStructuur'></a>Database Structuur

De applicatie maakt gebruik van een **MySQL-database genaamd `bowlingdb`**.  
Deze database beheert alle gegevens van gebruikers, reserveringen, spelers, bowlingbanen, scores en betalingen.

Het systeem zorgt ervoor dat:
- reserveringen gekoppeld zijn aan gebruikers
- spelers gekoppeld zijn aan reserveringen
- scores gekoppeld zijn aan spelers en rondes
- betalingen gekoppeld zijn aan reserveringen

---

###  3.1. <a name='Users'></a>Users

De tabel **users** bevat alle geregistreerde gebruikers van het systeem.

| Kolom | Type | Beschrijving |
|------|------|-------------|
| id | int | Unieke ID van de gebruiker |
| name | varchar | Naam van de gebruiker |
| email | varchar | Emailadres (uniek) |
| password_hash | varchar | Versleuteld wachtwoord |
| role | varchar | Rol van de gebruiker (bijv. klant of medewerker) |
| created_at | datetime | Datum waarop de gebruiker is aangemaakt |

---

###  3.2. <a name='Reservations'></a>Reservations

De tabel **reservations** bevat alle reserveringen van bowlingbanen.

| Kolom | Type | Beschrijving |
|------|------|-------------|
| id | int | Unieke reservering ID |
| user_id | int | Gebruiker die de reservering heeft gemaakt |
| reservation_date | date | Datum van de reservering |
| timeslot_start | time | Starttijd |
| timeslot_end | time | Eindtijd |
| adults | int | Aantal volwassenen |
| children | int | Aantal kinderen |
| snackbox_basic | int | Aantal basis snackboxen |
| snackbox_luxury | int | Aantal luxe snackboxen |
| subtotal | decimal | Bedrag exclusief BTW |
| vat | decimal | BTW bedrag |
| total_amount | decimal | Totaalbedrag |
| is_paid | boolean | Geeft aan of de reservering betaald is |
| created_at | datetime | Datum van aanmaak |

Elke reservering is gekoppeld aan een gebruiker via **user_id**.

---

###  3.3. <a name='Player'></a>Player

De tabel **player** bevat de spelers die deelnemen aan een reservering.

| Kolom | Type | Beschrijving |
|------|------|-------------|
| player_id | int | Unieke speler ID |
| player_name | varchar | Naam van de speler |
| age | int | Leeftijd |
| reservation_id | int | Reservering waaraan de speler gekoppeld is |
| total_score | int | Totale score van de speler |
| created_at | datetime | Datum van aanmaak |

---

###  3.4. <a name='Lane'></a>Lane

De tabel **lane** bevat de bowlingbanen.

| Kolom | Type | Beschrijving |
|------|------|-------------|
| lane_id | int | Unieke baan ID |
| lane_number | int | Nummer van de bowlingbaan |
| reservation_id | int | Reservering gekoppeld aan de baan |
| max_players | int | Maximum aantal spelers |

---

###  3.5. <a name='Rounds'></a>Rounds

De tabel **rounds** registreert de verschillende speelrondes.

| Kolom | Type | Beschrijving |
|------|------|-------------|
| id | int | Unieke ronde ID |
| player_id | int | Speler die de ronde speelt |
| score_id | int | Score gekoppeld aan de ronde |
| lane_id | int | Baan waarop gespeeld wordt |
| created_at | datetime | Datum van aanmaak |
| status | varchar | Status van de ronde |

---

###  3.6. <a name='Score'></a>Score

De tabel **score** bevat de scores van spelers.

| Kolom | Type | Beschrijving |
|------|------|-------------|
| score_id | int | Unieke score ID |
| player_id | int | Speler die de score heeft behaald |
| round_id | int | Ronde waarin de score is behaald |
| total_score | int | Totale score |
| high_score | boolean | Geeft aan of dit een hoogste score is |

---

###  3.7. <a name='Payments'></a>Payments

De tabel **payments** bevat de betalingen voor reserveringen.

| Kolom | Type | Beschrijving |
|------|------|-------------|
| id | int | Betaling ID |
| reservation_id | int | Reservering waarvoor betaald is |
| stripe_session_id | varchar | Stripe sessie ID |
| stripe_payment_intent | varchar | Stripe payment intent |
| payment_status | varchar | Status van betaling |
| amount | decimal | Betaald bedrag |
| created_at | datetime | Datum van betaling |

De applicatie maakt gebruik van **Stripe** voor het verwerken van online betalingen.

---

###  3.8. <a name='Relatiestussentabellen'></a>Relaties tussen tabellen

De belangrijkste relaties in de database zijn:

- Een **user** kan meerdere **reservations** hebben  
- Een **reservation** kan meerdere **players** bevatten  
- Een **player** kan meerdere **rounds** spelen  
- Een **round** hoort bij een **lane**  
- Scores worden gekoppeld aan **players** en **rounds**  
- Een **reservation** kan een **payment** hebben  

##  4. <a name='Setupguide'></a>Setup guide

> [!tip] Deze gedeelte is in het engels omdat het makkelijker te volgen is.

1. Download XAMPP on your machine. Make sure to set this up and for ease of use, ensure that the program always runs with administrator privilieges
2. Clone the project in your htdocs folder (For windows: C:/xampp/htdocs)
3. Start up the Apache service and the MySQL service. 
4. Get the SQL Database code and paste this in your local phpmyadmin environment
5. Run localhost/(gitfolderhere) on your browser and voila!

There are possibly more approaches to set this project up but this is how we got it working.

##  5. <a name='Features'></a>Features
De website heeft een reserveringsysteem, met CRUD acties voor het aanpassen van gebruikers, mogelijkeheid om gebruikers aan te maken. Daarnaast kan een gebruiker zowel als medewerker een reservatie zien en wordt er rekening gehouden met afscherming.

##  6. <a name='Techstack'></a>Tech stack
Voor het project gebruiken we een mix van HTML, CSS, PHP, en JavaScript in combinatie met MySQL voor de database in XAMPP
##  7. <a name='Security'></a>Security
Het systeem heeft basis security maatregelen zoals het voorkomen van
 
- SQL Injection
- Passworden zijn gehashed
- Rol gebaseerd toegang controle

##  8. <a name='Screenshots'></a>Screenshots
![s1](./afbeeldingen/bewijs1.png)
![s2](./afbeeldingen/bewijs2.png)
![s3](./afbeeldingen/bewijs3.png)

##  9. <a name='KnownIssues'></a>Known Issues

##  10. <a name='Verbetervoorstellen'></a>Verbetervoorstellen

##  11. <a name='Anderelinkjes'></a>Andere linkjes
###  11.1. <a name='Documentatie'></a>Documentatie
* [Documentatie](https://github.com/Bambaclad1/bowlingcenter-brooklyn/wiki)

###  11.2. <a name='KanbanBoard'></a>Kanban Board
* [Kanban Board](https://github.com/users/Bambaclad1/projects/1)

###  11.3. <a name='MainIssues'></a>Main Issues
- [User Stories](https://github.com/Bambaclad1/bowlingcenter-brooklyn/issues/15)

###  11.4. <a name='SubIssues'></a>Sub Issues
- [Eigenaar User Stories](https://github.com/Bambaclad1/bowlingcenter-brooklyn/issues/16)
- [Klant User Stories](https://github.com/Bambaclad1/bowlingcenter-brooklyn/issues/17)
- [Medewerker User Stories](https://github.com/Bambaclad1/bowlingcenter-brooklyn/issues/18)
