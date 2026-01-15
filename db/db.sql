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
    username VARCHAR(100) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    cognome VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    data_nascita DATE
);

-- Creazione della tabella TIPO_VIAGGIO
-- PK: Nome (indicato dal pallino pieno)
CREATE TABLE Tipo_Viaggio (
    nome VARCHAR(100) PRIMARY KEY,
    descrizione TEXT
);

-- Creazione della tabella PERIODO_ITINERARIO
-- Relazione (1,N) con Tipo_Viaggio: Un tipo di viaggio ha molti periodi
CREATE TABLE Periodo_Itinerario (
    id INT PRIMARY KEY,
    tipo_viaggio_nome VARCHAR(100) NOT NULL,
    descrizione TEXT,
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
        ON UPDATE CASCADE
);

-- Creazione della tabella RECENSIONE
-- Relazione (1,1) con Utente e (1,1) con Tipo_Viaggio
CREATE TABLE Recensione (
    id INT PRIMARY KEY,
    utente_email VARCHAR(255) NOT NULL,
    tipo_viaggio_nome VARCHAR(100) NOT NULL,
    data_recensione DATE,
    testo TEXT,
    punteggio INT CHECK (punteggio BETWEEN 1 AND 5),
    FOREIGN KEY (utente_email) REFERENCES Utente(email)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (tipo_viaggio_nome) REFERENCES Tipo_Viaggio(nome)
        ON UPDATE CASCADE ON DELETE CASCADE
);

-- Creazione della tabella PRENOTAZIONE (Relazione Molti-a-Molti)
-- Collega UTENTE (0,N) e VIAGGIO (0,N)
CREATE TABLE Prenotazione (
    id INT PRIMARY KEY,
    utente_email VARCHAR(255) NOT NULL,
    viaggio_id INT NOT NULL,
    FOREIGN KEY (utente_email) REFERENCES Utente(email)
        ON UPDATE CASCADE,
    FOREIGN KEY (viaggio_id) REFERENCES Viaggio(id)
        ON UPDATE CASCADE
);



-- =======================================================
-- VIAGGIO 1: NETTUNO (Cartella v1)
-- =======================================================


-- 1. Inserimento del TIPO DI VIAGGIO (Il "pacchetto" base)
INSERT INTO Tipo_Viaggio (nome, descrizione)
VALUES (
    'Grande Tour di Nettuno',
    'Un viaggio ai confini del sistema solare per esplorare il gigante di ghiaccio. Include sorvoli ravvicinati, osservazione delle tempeste di diamanti e una sosta sulla luna Tritone.'
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
-- Nota: Collego questi periodi al 'Grande Tour di Nettuno'
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(1, 'Grande Tour di Nettuno', 'Arrivo e Inserimento Orbitale: Avvicinamento al pianeta blu profondo e stabilizzazione nell''orbita alta per ammirare gli anelli scuri.'),
(2, 'Grande Tour di Nettuno', 'Discesa nella Termosfera: Un tuffo controllato negli strati esterni dell''atmosfera per osservare i venti supersonici.'),
(3, 'Grande Tour di Nettuno', 'Spedizione su Tritone: Atterraggio sulla luna ghiacciata per osservare i geyser di azoto liquido.');

-- 3. Inserimento delle IMMAGINI (Totale 6)

-- A. Tre immagini associate direttamente al TIPO_VIAGGIO (periodo_itinerario_id è NULL)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(101, 'Vista panoramica di Nettuno dallo spazio profondo', './assets/img/viaggi/v1/i1.jpg', 'Grande Tour di Nettuno', NULL),
(102, 'Interno della cabina di lusso con vista sugli anelli', './assets/img/viaggi/v1/i2.jpg', 'Grande Tour di Nettuno', NULL),
(103, 'Il pianeta blu con la Grande Macchia Scura visibile', './assets/img/viaggi/v1/i3.jpg', 'Grande Tour di Nettuno', NULL);

-- B. Una immagine per ogni PERIODO_ITINERARIO (tipo_viaggio_nome è NULL per evitare ridondanza, ci colleghiamo all'ID del periodo)
INSERT INTO Immagini (id, alt_text, url_immagine, tipo_viaggio_nome, periodo_itinerario_id)
VALUES
(104, 'La nave spaziale entra nell orbita di Nettuno', './assets/img/viaggi/v1/p1.jpg', NULL, 1),
(105, 'Venti supersonici nell atmosfera di Nettuno', './assets/img/viaggi/v1/p2.jpg', NULL, 2),
(106, 'Geyser di azoto sulla superficie di Tritone', './assets/img/viaggi/v1/p3.jpg', NULL, 3);

-- 4. Inserimento di una istanza di VIAGGIO (La data specifica di partenza)
INSERT INTO Viaggio (id, tipo_viaggio_nome, prezzo, prezzo_scontato, data_inizio, data_fine)
VALUES 
(10, 'Grande Tour di Nettuno',450000.00, 420000.00, '2150-06-01','2150-06-20'),
(11, 'Grande Tour di Nettuno', 500000.00,NULL,'2150-12-20','2151-01-10'),
(12, 'Grande Tour di Nettuno', 450000.00, 380000.00, '2151-05-15', '2151-06-05');


-- =======================================================
-- VIAGGIO 2: MARTE (Cartella v2)
-- =======================================================

-- 1. Inserimento del TIPO DI VIAGGIO
INSERT INTO Tipo_Viaggio (nome, descrizione)
VALUES (
    'Esplorazione Rossa: Marte',
    'Un''avventura sul pianeta rosso. Visita le antiche valli fluviali, scala il Monte Olimpo (il vulcano più alto del sistema solare) e soggiorna nelle prime colonie umane terraformate.'
);

-- 2. Inserimento dei 3 PERIODI DELL'ITINERARIO
INSERT INTO Periodo_Itinerario (id, tipo_viaggio_nome, descrizione)
VALUES 
(4, 'Esplorazione Rossa: Marte', 'Atterraggio e Valles Marineris: Discesa nel canyon più grande del sistema solare, lungo 4000 km e profondo 7 km.'),
(5, 'Esplorazione Rossa: Marte', 'Scalata del Monte Olimpo: Escursione guidata con tute potenziate sulle pendici del vulcano scudo alto 21 km.'),
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

INSERT INTO Prenotazione (id, utente_email, viaggio_id)
VALUES
(
    '1',
    'mario.rossi@email.com',
    '11'
),
(
    '2',
    'mario.rossi@email.com',
    '15'
),
(
    '3',
    'mario.rossi@email.com',
    '14'
);


-- =======================================================
-- ======================================================
-- recensioni

INSERT INTO Recensione(id,utente_email,tipo_viaggio_nome,data_recensione,testo,punteggio)
VALUES
(
    '1',
    'mario.rossi@email.com',
    'Esplorazione Rossa: Marte',
    '2013-04-12',
    'il tramonto blu e la bassa gravità sono mozzafiato, ma la polvere perenne e il cibo idroponico stancano. Un viaggio unico, sebbene sei mesi di claustrofobia siano una prova durissima.',
    '4'
);