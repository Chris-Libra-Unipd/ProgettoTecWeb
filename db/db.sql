DROP TABLE IF EXISTS Prenotazione;
DROP TABLE IF EXISTS Recensione;
DROP TABLE IF EXISTS Immagini;
DROP TABLE IF EXISTS Viaggio;
DROP TABLE IF EXISTS Periodo_Itinerario;
DROP TABLE IF EXISTS Utente;
DROP TABLE IF EXISTS Tipo_Viaggio;

-- Creazione della tabella UTENTE
-- PK: Email (indicato dal pallino pieno)
CREATE TABLE Utente (
    email VARCHAR(255) PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    data_nascita DATE NOT NULL
);

-- Creazione della tabella TIPO_VIAGGIO
-- PK: Nome (indicato dal pallino pieno)
CREATE TABLE Tipo_Viaggio (
    nome VARCHAR(100) PRIMARY KEY,
    descrizione TEXT NOT NULL,
    durata_giorni INT NOT NULL
);

-- Creazione della tabella PERIODO_ITINERARIO
-- Relazione (1,N) con Tipo_Viaggio: Un tipo di viaggio ha molti periodi
CREATE TABLE Periodo_Itinerario (
    id INT PRIMARY KEY,
    tipo_viaggio_nome VARCHAR(100) NOT NULL,
    descrizione TEXT NOT NULL,
    FOREIGN KEY (tipo_viaggio_nome) REFERENCES Tipo_Viaggio(nome) 
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- Creazione della tabella IMMAGINI
-- Sembra collegata sia a Tipo_Viaggio che a Periodo_Itinerario con cardinalità (0,1)
CREATE TABLE Immagini (
    id INT PRIMARY KEY,
    alt_text VARCHAR(255),
    url_immagine VARCHAR(500) NOT NULL,
    tipo_viaggio_nome VARCHAR(100),
    periodo_itinerario_id INT,
    FOREIGN KEY (tipo_viaggio_nome) REFERENCES Tipo_Viaggio(nome)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (periodo_itinerario_id) REFERENCES Periodo_Itinerario(id)
        ON UPDATE CASCADE ON DELETE SET NULL
);

-- Creazione della tabella VIAGGIO (Istanza specifica di un viaggio)
-- Relazione "SPECIFICA" (1,1) con Tipo_Viaggio
CREATE TABLE Viaggio (
    id INT PRIMARY KEY,
    tipo_viaggio_nome VARCHAR(100) NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    prezzo_scontato DECIMAL(10, 2),
    data_inizio DATE NOT NULL,
    data_fine DATE NOT NULL,
    FOREIGN KEY (tipo_viaggio_nome) REFERENCES Tipo_Viaggio(nome)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- Creazione della tabella RECENSIONE
-- Relazione (1,1) con Utente e (1,1) con Tipo_Viaggio
CREATE TABLE Recensione (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utente_email VARCHAR(255) NOT NULL,
    tipo_viaggio_nome VARCHAR(100) NOT NULL,
    data_recensione DATE NOT NULL,
    testo TEXT NOT NULL,
    punteggio INT NOT NULL CHECK (punteggio BETWEEN 1 AND 5),
    FOREIGN KEY (utente_email) REFERENCES Utente(email)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (tipo_viaggio_nome) REFERENCES Tipo_Viaggio(nome)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- Creazione della tabella PRENOTAZIONE (Relazione Molti-a-Molti)
-- Collega UTENTE (0,N) e VIAGGIO (0,N)
CREATE TABLE Prenotazione (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utente_email VARCHAR(255) NOT NULL,
    viaggio_id INT NOT NULL,
    CONSTRAINT unique_prenotazione
        UNIQUE (utente_email, viaggio_id),
    FOREIGN KEY (utente_email) REFERENCES Utente(email)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (viaggio_id) REFERENCES Viaggio(id)
        ON UPDATE CASCADE ON DELETE CASCADE
);



-- =======================================================
-- VIAGGIO 1: NETTUNO (Cartella v1)
-- =======================================================


-- 1. Inserimento del TIPO DI VIAGGIO (Il "pacchetto" base)
INSERT INTO Tipo_Viaggio (nome, descrizione, durata_giorni)
VALUES (
    'Orizzonte cobalto su Nettuno',
    'Un viaggio ai confini del sistema solare per esplorare il gigante di ghiaccio. Include sorvoli ravvicinati, osservazione delle tempeste di diamanti e una sosta sulla luna Tritone.',
    20
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
-- Nota: Collego questi periodi al 'Orizzonte cobalto su Nettuno'
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(1, 'Orizzonte cobalto su Nettuno', 'Arrivo e Inserimento Orbitale: Avvicinamento al pianeta blu profondo e stabilizzazione nell''orbita alta per ammirare gli anelli scuri.'),
(2, 'Orizzonte cobalto su Nettuno', 'Discesa nella Termosfera: Un tuffo controllato negli strati esterni dell''atmosfera per osservare i venti supersonici.'),
(3, 'Orizzonte cobalto su Nettuno', 'Spedizione su Tritone: Atterraggio sulla luna ghiacciata per osservare i <span lang="en">geyser</span> di azoto liquido.');

-- 3. Inserimento delle IMMAGINI (Totale 6)

-- A. Tre immagini associate direttamente al TIPO_VIAGGIO (periodo_itinerario_id è NULL)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(101, 'Vista panoramica di Nettuno dallo spazio profondo', './assets/img/viaggi/v1/i1.jpg', 'Orizzonte cobalto su Nettuno', NULL),
(102, 'Interno della cabina di lusso con vista sugli anelli', './assets/img/viaggi/v1/i2.jpg', 'Orizzonte cobalto su Nettuno', NULL),
(103, 'Il pianeta blu con la Grande Macchia Scura visibile', './assets/img/viaggi/v1/i3.jpg', 'Orizzonte cobalto su Nettuno', NULL);

-- B. Una immagine per ogni PERIODO_ITINERARIO (tipo_viaggio_nome è NULL per evitare ridondanza, ci colleghiamo all'ID del periodo)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(104, 'La nave spaziale entra nell orbita di Nettuno', './assets/img/viaggi/v1/p1.jpg', NULL, 1),
(105, 'Venti supersonici nell atmosfera di Nettuno', './assets/img/viaggi/v1/p2.jpg', NULL, 2),
(106, 'Geyser di azoto sulla superficie di Tritone', './assets/img/viaggi/v1/p3.jpg', NULL, 3);

-- 4. Inserimento di una istanza di VIAGGIO (La data specifica di partenza)
INSERT INTO Viaggio (id, tipo_viaggio_nome, prezzo, prezzo_scontato, data_inizio, data_fine)
VALUES 
(10, 'Orizzonte cobalto su Nettuno',450000.00, 420000.00, '2150-06-01','2150-06-20'),
(11, 'Orizzonte cobalto su Nettuno', 500000.00,NULL,'2150-12-20','2151-01-10'),
(12, 'Orizzonte cobalto su Nettuno', 450000.00, 380000.00, '2151-05-15', '2151-06-05');


-- =======================================================
-- VIAGGIO 2: MARTE (Cartella v2)
-- =======================================================

-- 1. Inserimento del TIPO DI VIAGGIO
INSERT INTO Tipo_Viaggio (nome, descrizione, durata_giorni)
VALUES (
    'Esplorazione Rossa: Marte',
    'Un''avventura sul pianeta rosso. Visita le antiche valli fluviali, scala il Monte Olimpo (il vulcano più alto del sistema solare) e soggiorna nelle prime colonie umane terraformate.',
    15
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(4, 'Esplorazione Rossa: Marte', 'Atterraggio e Valles Marineris: Discesa nel <span lang="en">canyon</span> più grande del sistema solare, lungo 4000 <abbr title="chilometri">km</abbr> e profondo 7 <abbr title="chilometri">km</abbr>.'),
(5, 'Esplorazione Rossa: Marte', 'Scalata del Monte Olimpo: Escursione guidata con tute potenziate sulle pendici del vulcano scudo alto 21 <abbr title="chilometri">km</abbr>.'),
(6, 'Esplorazione Rossa: Marte', 'Vita nella Colonia Alpha: Esperienza di vita in una cupola biosfera con coltivazioni idroponiche e simulazione di gravità terrestre.');

-- 3. Inserimento delle IMMAGINI (Cartella v2)

-- A. Tre immagini associate al TIPO_VIAGGIO (v2/i1, v2/i2, v2/i3)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(201, 'Il pianeta rosso visto dall oblò della nave', './assets/img/viaggi/v2/i1.jpg', 'Esplorazione Rossa: Marte', NULL),
(202, 'Rover che attraversa le dune marziane', './assets/img/viaggi/v2/i2.jpg', 'Esplorazione Rossa: Marte', NULL),
(203, 'Tramonto blu su Marte', './assets/img/viaggi/v2/i3.jpg', 'Esplorazione Rossa: Marte', NULL);

-- B. Una immagine per ogni PERIODO_ITINERARIO (v2/p1, v2/p2, v2/p3 collegate agli ID 4, 5, 6)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(204, 'Vista aerea della Valles Marineris', './assets/img/viaggi/v2/p1.jpg', NULL, 4),
(205, 'La vetta del Monte Olimpo sopra le nuvole', './assets/img/viaggi/v2/p2.jpg', NULL, 5),
(206, 'Interno della cupola biosfera Alpha', './assets/img/viaggi/v2/p3.jpg', NULL, 6);

-- 4. Inserimento di 3 istanze di VIAGGIO (Date specifiche)
INSERT INTO Viaggio (id, tipo_viaggio_nome, prezzo, prezzo_scontato, data_inizio, data_fine)
VALUES 
(13, 'Esplorazione Rossa: Marte', 250000.00, 220000.00, '2150-08-10', '2150-08-25'),
(14, 'Esplorazione Rossa: Marte', 280000.00, NULL, '2150-12-05', '2150-12-20'),
(15, 'Esplorazione Rossa: Marte', 250000.00, 199000.00, '2151-03-01', '2151-03-16');



-- =======================================================
-- VIAGGIO 3: SATURNO (Cartella v3)
-- =======================================================

-- 1. Inserimento del TIPO DI VIAGGIO
INSERT INTO Tipo_Viaggio (nome, descrizione, durata_giorni)
VALUES (
    'Anelli di Saturno: Il Signore degli Anelli',
    'L''esperienza visiva più spettacolare del sistema solare. Naviga attraverso gli iconici anelli, visita la luna Titano con i suoi laghi di metano e osserva la tempesta esagonale al polo nord.',
    25
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(7, 'Anelli di Saturno: Il Signore degli Anelli', 'Slalom negli Anelli: Navigazione di precisione tra le particelle di ghiaccio degli anelli A e B, con vista mozzafiato sulla divisione di Cassini.'),
(8, 'Anelli di Saturno: Il Signore degli Anelli', 'Atterraggio su Titano: Esplorazione con sottomarino nei laghi di idrocarburi liquidi sotto la spessa atmosfera arancione.'),
(9, 'Anelli di Saturno: Il Signore degli Anelli', 'Sorvolo di Encelado: Passaggio attraverso i pennacchi di vapore acqueo emessi dai <span lang="en">geyser</span> del polo sud di questa luna ghiacciata.');

-- 3. Inserimento delle IMMAGINI (Cartella v3, path corretto: assets)

-- A. Tre immagini associate al TIPO_VIAGGIO (v3/i1, v3/i2, v3/i3)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(301, 'Saturno in controluce con gli anelli brillanti', './assets/img/viaggi/v3/i1.jpg', 'Anelli di Saturno: Il Signore degli Anelli', NULL),
(302, 'Lounge panoramica con vista sugli anelli', './assets/img/viaggi/v3/i2.jpg', 'Anelli di Saturno: Il Signore degli Anelli', NULL),
(303, 'La tempesta esagonale al polo nord di Saturno', './assets/img/viaggi/v3/i3.jpg', 'Anelli di Saturno: Il Signore degli Anelli', NULL);

-- B. Una immagine per ogni PERIODO_ITINERARIO (v3/p1, v3/p2, v3/p3 collegate agli ID 7, 8, 9)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(304, 'Dettaglio ravvicinato delle rocce ghiacciate degli anelli', './assets/img/viaggi/v3/p1.jpg', NULL, 7),
(305, 'Sottomarino esplorativo nei mari di Titano', './assets/img/viaggi/v3/p2.jpg', NULL, 8),
(306, 'Getti di vapore sulla superficie di Encelado', './assets/img/viaggi/v3/p3.jpg', NULL, 9);

-- 4. Inserimento di 3 istanze di VIAGGIO
INSERT INTO Viaggio (id, tipo_viaggio_nome, prezzo, prezzo_scontato, data_inizio, data_fine)
VALUES 
(16, 'Anelli di Saturno: Il Signore degli Anelli', 550000.00, 520000.00, '2150-09-01', '2150-09-25'),
(17, 'Anelli di Saturno: Il Signore degli Anelli', 400000.00, NULL, '2151-01-05', '2151-01-30'),
(18, 'Anelli di Saturno: Il Signore degli Anelli', 550000.00, 490000.00, '2151-04-10', '2151-05-05');


-- =======================================================
-- VIAGGIO 4: BUCO NERO (Cigno X-1) - Cartella v4
-- =======================================================

-- 1. Inserimento del TIPO DI VIAGGIO
INSERT INTO Tipo_Viaggio (nome, descrizione, durata_giorni)
VALUES (
    'Orizzonte degli Eventi: Cigno X-1',
    'Il viaggio definitivo per gli amanti del brivido estremo. Sperimenta la dilatazione temporale, osserva il disco di accrescimento e naviga in sicurezza al limite del punto di non ritorno.',
    15
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
-- IDs progressivi: 10, 11, 12
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(10, 'Orizzonte degli Eventi: Cigno X-1', 'Avvicinamento al Disco di Accrescimento: Osservazione sicura dei getti di raggi X e del plasma incandescente che ruota attorno alla singolarità.'),
(11, 'Orizzonte degli Eventi: Cigno X-1', 'La Sfera di Fotoni: Navigazione nel punto in cui la gravità è così forte che la luce orbita attorno al buco nero. Potrai vedere la parte posteriore della tua stessa nave.'),
(12, 'Orizzonte degli Eventi: Cigno X-1', 'Esperienza di Dilatazione Temporale: Stazionamento in orbita ravvicinata dove un''ora corrisponde a sette anni sulla Terra (monitoraggio medico incluso).');

-- 3. Inserimento delle IMMAGINI (Cartella v4)

-- A. Tre immagini associate al TIPO_VIAGGIO (Serie 400: 401, 402, 403)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(401, 'Il buco nero che piega la luce delle stelle circostanti', './assets/img/viaggi/v4/i1.jpg', 'Orizzonte degli Eventi: Cigno X-1', NULL),
(402, 'La nave da crociera schermata contro le radiazioni', './assets/img/viaggi/v4/i2.jpg', 'Orizzonte degli Eventi: Cigno X-1', NULL),
(403, 'Il disco di accrescimento luminoso nel buio dello spazio', './assets/img/viaggi/v4/i3.jpg', 'Orizzonte degli Eventi: Cigno X-1', NULL);

-- B. Una immagine per ogni PERIODO_ITINERARIO (Serie 400: 404, 405, 406 collegate agli ID 10, 11, 12)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(404, 'Vista ravvicinata del plasma infuocato', './assets/img/viaggi/v4/p1.jpg', NULL, 10),
(405, 'Effetto visivo della curvatura della luce nella sfera fotonica', './assets/img/viaggi/v4/p2.jpg', NULL, 11),
(406, 'Orologi sincronizzati che mostrano la differenza temporale', './assets/img/viaggi/v4/p3.jpg', NULL, 12);

-- 4. Inserimento di 3 istanze di VIAGGIO
-- IDs progressivi: 19, 20, 21
INSERT INTO Viaggio (id, tipo_viaggio_nome, prezzo, prezzo_scontato, data_inizio, data_fine)
VALUES 
(19, 'Orizzonte degli Eventi: Cigno X-1', 1200000.00, NULL, '2151-02-01', '2151-02-15'),
(20, 'Orizzonte degli Eventi: Cigno X-1', 1500000.00, NULL, '2151-07-10', '2151-07-25'),
(21, 'Orizzonte degli Eventi: Cigno X-1', 1200000.00, NULL, '2151-11-05', '2151-11-20');


-- =======================================================
-- VIAGGIO 5: LA LUNA (Cartella v5)
-- =======================================================

-- 1. Inserimento del TIPO DI VIAGGIO
INSERT INTO Tipo_Viaggio (nome, descrizione, durata_giorni)
VALUES (
    'Luna: un nuovo volto',
    'Goditi la vista più bella della Terra dal nostro resort nel Mare della Tranquillità. Un <span lang="en">mix</span> perfetto di <span lang="en">relax</span> a bassa gravità e tour storici nei luoghi dei primi allunaggi.',
    7
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
-- IDs progressivi: 13, 14, 15
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(13, 'Luna: un nuovo volto', 'Sulle orme di Apollo 11: Visita guidata al sito storico originale del 1969, preservato sotto una cupola di vetro protettiva.'),
(14, 'Luna: un nuovo volto', 'Cena "<span lang="en">Earthrise</span>": Esperienza gastronomica gourmet in una cupola panoramica mentre la Terra sorge all''orizzonte lunare.'),
(15, 'Luna: un nuovo volto', 'Il Lato Oscuro: Escursione in <span lang="en">rover</span> sul lato nascosto della Luna per un''osservazione stellare priva di qualsiasi inquinamento luminoso terrestre.');

-- 3. Inserimento delle IMMAGINI (Cartella v5 - Serie 500)

-- A. Tre immagini associate al TIPO_VIAGGIO
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(501, 'Il resort lunare con la Terra sullo sfondo', './assets/img/viaggi/v5/i1.jpg', 'Luna: un nuovo volto', NULL),
(502, 'Piscina a bassa gravità all interno dell hotel', './assets/img/viaggi/v5/i2.jpg', 'Luna: un nuovo volto', NULL),
(503, 'Turisti che camminano sulla superficie grigia', './assets/img/viaggi/v5/i3.jpg', 'Luna: un nuovo volto', NULL);

-- B. Una immagine per ogni PERIODO_ITINERARIO
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(504, 'Il modulo lunare Eagle originale protetto dalla cupola', './assets/img/viaggi/v5/p1.jpg', NULL, 13),
(505, 'Vista della Terra crescente da un tavolo ristorante', './assets/img/viaggi/v5/p2.jpg', NULL, 14),
(506, 'Il cielo stellato densissimo visto dal lato oscuro', './assets/img/viaggi/v5/p3.jpg', NULL, 15);

-- 4. Inserimento di 3 istanze di VIAGGIO
-- IDs progressivi: 22, 23, 24
INSERT INTO Viaggio (id, tipo_viaggio_nome, prezzo, prezzo_scontato, data_inizio, data_fine)
VALUES 
(22, 'Luna: un nuovo volto', 80000.00, 75000.00, '2150-07-01', '2150-07-07'),
(23, 'Luna: un nuovo volto', 95000.00, NULL, '2150-12-24', '2150-12-31'), -- Natale sulla Luna
(24, 'Luna: un nuovo volto', 80000.00, 60000.00, '2151-02-14', '2151-02-21'); -- San Valentino


-- =======================================================
-- VIAGGIO 6: GIOVE (Cartella v6)
-- =======================================================

-- 1. Inserimento del TIPO DI VIAGGIO
INSERT INTO Tipo_Viaggio (nome, descrizione, durata_giorni)
VALUES (
    'Giove: Il Re dei Pianeti',
    'Un safari spaziale nel sistema gioviano. Osserva la Grande Macchia Rossa da vicino, evita i vulcani attivi di Io e cerca forme di vita nell''oceano sotterraneo di Europa.',
    20
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
-- IDs progressivi: 16, 17, 18
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(16, 'Giove: Il Re dei Pianeti', 'L''Occhio del Ciclone: Sorvolo a bassa quota della Grande Macchia Rossa, una tempesta più grande della Terra intera.'),
(17, 'Giove: Il Re dei Pianeti', 'I Vulcani di Io: Navigazione pericolosa tra le nubi di zolfo e le eruzioni costanti della luna più geologicamente attiva del sistema.'),
(18, 'Giove: Il Re dei Pianeti', 'Perforazione su Europa: Atterraggio sulla crosta ghiacciata e utilizzo di droni sottomarini per esplorare l''oceano sottostante.');

-- 3. Inserimento delle IMMAGINI (Cartella v6 - Serie 600)

-- A. Tre immagini associate al TIPO_VIAGGIO
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(601, 'Giove e le sue lune visti da lontano', './assets/img/viaggi/v6/i1.jpg', 'Giove: Il Re dei Pianeti', NULL),
(602, 'La stazione spaziale orbitante attorno a Giove', './assets/img/viaggi/v6/i2.jpg', 'Giove: Il Re dei Pianeti', NULL),
(603, 'Dettaglio delle bande nuvolose colorate', './assets/img/viaggi/v6/i3.jpg', 'Giove: Il Re dei Pianeti', NULL);

-- B. Una immagine per ogni PERIODO_ITINERARIO
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(604, 'La Grande Macchia Rossa vista dall alto', './assets/img/viaggi/v6/p1.jpg', NULL, 16),
(605, 'Eruzione vulcanica su Io', './assets/img/viaggi/v6/p2.jpg', NULL, 17),
(606, 'Il ghiaccio azzurro di Europa con crepe scure', './assets/img/viaggi/v6/p3.jpg', NULL, 18);

-- 4. Inserimento di 3 istanze di VIAGGIO
-- IDs progressivi: 25, 26, 27
INSERT INTO Viaggio (id, tipo_viaggio_nome, prezzo, prezzo_scontato, data_inizio, data_fine)
VALUES 
(25, 'Giove: Il Re dei Pianeti', 350000.00, 320000.00, '2150-10-01', '2150-10-20'),
(26, 'Giove: Il Re dei Pianeti', 380000.00, NULL, '2151-03-15', '2151-04-05'),
(27, 'Giove: Il Re dei Pianeti', 350000.00, 299000.00, '2151-08-01', '2151-08-20');


-- =======================================================
-- ======================================================
-- utenti di test


INSERT INTO Utente (email, username, nome, cognome, password_hash, data_nascita)
VALUES 
(
    'mario.rossi@email.com', 
    'AstroMario90', 
    'Mario', 
    'Rossi', 
    '$2y$10$1JLofMAB2vmLYqKoIf/kt.UqUTaV38LAWy0S3gpapXoDbroreAWMW', -- Simulazione hash
    '1990-05-15'
),
(
    'elena.verdi@test.it', 
    'ElenaSpace', 
    'Elena', 
    'Verdi', 
    '$2y$10$1JLofMAB2vmLYqKoIf/kt.UqUTaV38LAWy0S3gpapXoDbroreAWMW', -- Simulazione hash
    '1995-11-22'
);

-- PASSWORD UTENTI: test



-- =======================================================
-- ======================================================
-- prenotazioni

INSERT INTO Prenotazione (utente_email, viaggio_id)
VALUES
(
    'mario.rossi@email.com',
    '11'
),
(
    'mario.rossi@email.com',
    '15'
),
(
    'mario.rossi@email.com',
    '14'
);


-- =======================================================
-- ======================================================
-- recensioni

INSERT INTO Recensione(utente_email,tipo_viaggio_nome,data_recensione,testo,punteggio)
VALUES
(
    'mario.rossi@email.com',
    'Esplorazione Rossa: Marte',
    '2013-04-12',
    'il tramonto blu e la bassa gravità sono mozzafiato, ma la polvere perenne e il cibo idroponico stancano. Un viaggio unico, sebbene sei mesi di claustrofobia siano una prova durissima.',
    '4'
);