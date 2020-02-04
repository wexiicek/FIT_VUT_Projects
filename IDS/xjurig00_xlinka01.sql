DROP TABLE R_Osoba CASCADE CONSTRAINTS;
DROP TABLE R_Zamestnanec CASCADE CONSTRAINTS;
DROP TABLE R_Zakaznik CASCADE CONSTRAINTS;
DROP TABLE R_Pokrm CASCADE CONSTRAINTS;
DROP TABLE R_Surovina CASCADE CONSTRAINTS;
DROP TABLE R_Dodavatel CASCADE CONSTRAINTS;
DROP TABLE R_Miesto CASCADE CONSTRAINTS;
DROP TABLE R_Objednavka CASCADE CONSTRAINTS;
DROP TABLE R_Objednavka_Surovin CASCADE CONSTRAINTS;
DROP TABLE R_Plati CASCADE CONSTRAINTS;
DROP TABLE R_Rezervuje CASCADE CONSTRAINTS;
DROP TABLE R_Vyzaduje CASCADE CONSTRAINTS;
DROP TRIGGER osoba_trigger;
DROP SEQUENCE id_osoby_s;
DROP TRIGGER rezervace_mista_trigger;
DROP PROCEDURE Obsazenost;
DROP PROCEDURE Rezervace;
DROP INDEX osoba_index;
DROP INDEX objednavka_index;
DROP MATERIALIZED VIEW Rezervace_MV;

CREATE TABLE R_Osoba (
    ID INTEGER NOT NULL PRIMARY KEY,
    MENO VARCHAR(50),
    PRIEZVISKO VARCHAR(50),
    TELEFON VARCHAR(13) NOT NULL CHECK (REGEXP_LIKE(TELEFON, '^((\+420)|(\+421))? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$'))
);

/* TRIGGER 1: Trigger vytvara ID pre kazdu vytvorenu osobu */
CREATE SEQUENCE id_osoby_s;
CREATE OR REPLACE TRIGGER osoba_trigger
BEFORE INSERT ON R_Osoba
FOR EACH ROW
WHEN (new.ID IS NULL)
BEGIN
SELECT id_osoby_s.NEXTVAL
INTO :new.ID
FROM DUAL;
END;
/


CREATE TABLE R_Zamestnanec (
    ADRESA VARCHAR(50) NOT NULL,
    DRUH_POMERU VARCHAR(20) NOT NULL,
    RODNE_CISLO VARCHAR(8) NOT NULL,
    OD DATE NOT NULL,
    DO DATE,
    ID INTEGER PRIMARY KEY REFERENCES R_Osoba ON DELETE SET NULL
);

CREATE TABLE R_Zakaznik (
    EMAIL VARCHAR(88) NOT NULL CHECK(REGEXP_LIKE(EMAIL, '[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}')),
    POCET_NAVSTEV INTEGER NOT NULL,
    VERNOSTNE_BODY INTEGER NOT NULL,
    ID INTEGER PRIMARY KEY REFERENCES R_Osoba ON DELETE SET NULL
);

CREATE TABLE R_Pokrm (
    NAZOV VARCHAR(50) NOT NULL PRIMARY KEY,
    DRUH VARCHAR(15) NOT NULL,
    CENA INTEGER NOT NULL,
    POPIS VARCHAR(100),
    PRIPRAVIL INTEGER REFERENCES R_Zamestnanec ON DELETE SET NULL
);

CREATE TABLE R_Surovina (
    NAZOV VARCHAR(20) NOT NULL PRIMARY KEY,
    AKTUALNE_MNOZSTVO INTEGER NOT NULL,
    ALERGENY VARCHAR(30)
);

CREATE TABLE R_Dodavatel (
    NAZOV_FIRMY VARCHAR(25) NOT NULL PRIMARY KEY,
    ADRESA VARCHAR(30) NOT NULL,
    TELEFON VARCHAR(13) NOT NULL,
    EMAIL VARCHAR(30) NOT NULL
);

CREATE TABLE R_Objednavka (
    ID INTEGER GENERATED AS IDENTITY PRIMARY KEY,
    STOL INTEGER NOT NULL,
    DATUM DATE NOT NULL,
    POKRM VARCHAR (50) REFERENCES R_Pokrm ON DELETE SET NULL,
    OBSTARAVA_ZAM INTEGER REFERENCES R_Zamestnanec ON DELETE SET NULL,
    VYTVORIL_ZAM INTEGER REFERENCES R_Zamestnanec ON DELETE SET NULL,
    VYTVORIL_ZAK INTEGER REFERENCES R_Zakaznik ON DELETE SET NULL
);

CREATE TABLE R_Vyzaduje (
    MNOZSTVI INTEGER NOT NULL,
    POKRM VARCHAR (50) REFERENCES R_Pokrm ON DELETE SET NULL,
    SUROVINA VARCHAR (20) REFERENCES R_Surovina ON DELETE SET NULL
);

CREATE TABLE R_Objednavka_Surovin (
    ID INTEGER GENERATED AS IDENTITY PRIMARY KEY,
    CENA INTEGER NOT NULL,
    DATUM DATE,
    CAS DATE,
    SUROVINA VARCHAR (20) REFERENCES R_Surovina ON DELETE SET NULL,
    DODAVATEL VARCHAR (25) REFERENCES R_Dodavatel ON DELETE SET NULL,
    VYTVORIL_ZAM INTEGER REFERENCES R_Zamestnanec ON DELETE SET NULL
);



CREATE TABLE R_Plati (
    CENA INTEGER NOT NULL,
    DATUM DATE NOT NULL,
    SPOSOB_PLATBY VARCHAR(25),
    OBJEDNAVKA INTEGER REFERENCES R_Objednavka ON DELETE SET NULL,
    ZAKAZNIK INTEGER REFERENCES R_Zakaznik ON DELETE SET NULL
);

CREATE TABLE R_Miesto (
    ID INTEGER GENERATED AS IDENTITY PRIMARY KEY,
    TYP VARCHAR(10) NOT NULL,
    KAPACITA INTEGER
);

CREATE TABLE R_Rezervuje (
    DATUM DATE NOT NULL,
    CAS DATE NOT NULL,
    POCET_OSOB INTEGER NOT NULL,
    ZAKAZNIK INTEGER REFERENCES R_Zakaznik ON DELETE SET NULL,
    MIESTO INTEGER REFERENCES R_Miesto ON DELETE SET NULL
);


INSERT INTO R_OSOBA(MENO, PRIEZVISKO, TELEFON) VALUES ('Jozef','Stransky','+421999888557');
INSERT INTO R_OSOBA(MENO, PRIEZVISKO, TELEFON) VALUES ('Andrej','Valihora','+421999666777');
INSERT INTO R_OSOBA(MENO, PRIEZVISKO, TELEFON) VALUES ('Viktor','Ferus','+421999555714');
INSERT INTO R_OSOBA(MENO, PRIEZVISKO, TELEFON) VALUES ('Matej','Zelensky','+421111888779');
INSERT INTO R_OSOBA(MENO, PRIEZVISKO, TELEFON) VALUES ('Ferdinand','Piskot','+421999666767');
INSERT INTO R_OSOBA(MENO, PRIEZVISKO, TELEFON) VALUES ('Zbynek','Parabola','+420123321456');

INSERT INTO R_Zakaznik(EMAIL, POCET_NAVSTEV, VERNOSTNE_BODY, ID) VALUES ('ZELENSKY@ZAHRADYZELENSKY.SK', '14', '140', '4');
INSERT INTO R_Zakaznik(EMAIL, POCET_NAVSTEV, VERNOSTNE_BODY, ID) VALUES ('PISKOT@PISKOTOVO.PIS', '1', '5', '5');
INSERT INTO R_Zakaznik(EMAIL, POCET_NAVSTEV, VERNOSTNE_BODY, ID) VALUES ('ZBYNA@PARABO.LA', '42', '420', '6');

INSERT INTO R_Zamestnanec(ADRESA, DRUH_POMERU, RODNE_CISLO, OD, ID) VALUES ('Francuzska 408/44 Bratislava', 'trvaly', '19970508', TO_DATE('2016-01-01', 'YYYY-MM-DD'), '1');
INSERT INTO R_Zamestnanec(ADRESA, DRUH_POMERU, RODNE_CISLO, OD, ID) VALUES ('Zelena 420/42 Bratislava', 'trvaly', '19901207', TO_DATE('2017-08-11', 'YYYY-MM-DD'), '2');
INSERT INTO R_Zamestnanec(ADRESA, DRUH_POMERU, RODNE_CISLO, OD, ID) VALUES ('Francuzska 408/44 Bratislava', 'trvaly', '19890205', TO_DATE('2017-10-22', 'YYYY-MM-DD'), '3');


INSERT INTO R_Dodavatel(NAZOV_FIRMY, ADRESA, TELEFON, EMAIL) VALUES ('Food Trade s.r.o.', 'Krupicova 447/2a, Bratislava', '+421987789654', 'ftrade@ftrade.com');
INSERT INTO R_Dodavatel(NAZOV_FIRMY, ADRESA, TELEFON, EMAIL) VALUES ('Local Market s.r.o.', 'Zeleninova 42, Lozorno', '+421123321456', 'kontakt@lmarket.com');

INSERT INTO R_Surovina(NAZOV, AKTUALNE_MNOZSTVO, ALERGENY) VALUES ('kukurica', '4500', '1');
INSERT INTO R_Surovina(NAZOV, AKTUALNE_MNOZSTVO, ALERGENY) VALUES ('hrasok', '4500', '5');
INSERT INTO R_Surovina(NAZOV, AKTUALNE_MNOZSTVO, ALERGENY) VALUES ('ryza', '12700', '');
INSERT INTO R_Surovina(NAZOV, AKTUALNE_MNOZSTVO, ALERGENY) VALUES ('kuracie platky', '2500', '');
INSERT INTO R_Surovina(NAZOV, AKTUALNE_MNOZSTVO, ALERGENY) VALUES ('americke zemiaky', '4750', '');
INSERT INTO R_Surovina(NAZOV, AKTUALNE_MNOZSTVO, ALERGENY) VALUES ('muka', '44400', '2');

INSERT INTO R_Pokrm(NAZOV, DRUH, CENA, POPIS, PRIPRAVIL) VALUES ('Palacinky s dzemom', 'dezert', '4', 'Dva kusy palaciniek s dzemom podla vyberu', '1');
INSERT INTO R_Pokrm(NAZOV, DRUH, CENA, POPIS, PRIPRAVIL) VALUES ('Kuraci steak s americkymi zemiakmi', 'hlavny chod', '6', 'Marinovany kuraci steak so zemiakmi v supke', '1');
INSERT INTO R_Pokrm(NAZOV, DRUH, CENA, POPIS, PRIPRAVIL) VALUES ('Zeleninove rizoto', 'hlavny chod', '3', 'Rizoto s hraskom, kukuricou, mrkvou a jarnou cibulkou', '1');

INSERT INTO R_Vyzaduje(MNOZSTVI, POKRM, SUROVINA) VALUES ('100', 'Palacinky s dzemom', 'muka');
INSERT INTO R_Vyzaduje(MNOZSTVI, POKRM, SUROVINA) VALUES ('80', 'Zeleninove rizoto', 'kukurica');
INSERT INTO R_Vyzaduje(MNOZSTVI, POKRM, SUROVINA) VALUES ('75', 'Zeleninove rizoto', 'hrasok');
INSERT INTO R_Vyzaduje(MNOZSTVI, POKRM, SUROVINA) VALUES ('120', 'Kuraci steak s americkymi zemiakmi', 'americke zemiaky');

INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('miesto', '');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('miesto', '');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('miesto', '');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('miesto', '');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('miesto', '');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '4');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '4');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '4');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '4');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '4');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '4');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '4');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '8');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '8');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '8');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '8');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '8');
INSERT INTO R_Miesto(TYP, KAPACITA) VALUES ('salon', '8');


INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('1', TO_DATE('2018-04-04','YYYY-MM-DD'), 'Palacinky s dzemom', '1', '2', '5');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('1', TO_DATE('2018-04-04','YYYY-MM-DD'), 'Kuraci steak s americkymi zemiakmi', '1', '2', '6');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('2', TO_DATE('2018-04-04','YYYY-MM-DD'), 'Palacinky s dzemom', '1', '2', '4');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('3', TO_DATE('2018-07-11','YYYY-MM-DD'), 'Kuraci steak s americkymi zemiakmi', '2', '2', '5');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('3', TO_DATE('2018-04-12','YYYY-MM-DD'), 'Kuraci steak s americkymi zemiakmi', '1', '2', '5');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('1', TO_DATE('2019-01-04','YYYY-MM-DD'), 'Palacinky s dzemom', '1', '2', '5');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('2', TO_DATE('2019-01-05','YYYY-MM-DD'), 'Zeleninove rizoto', '3', '2', '4');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('2', TO_DATE('2019-01-05','YYYY-MM-DD'), 'Palacinky s dzemom', '3', '3', '5');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('3', TO_DATE('2019-02-11','YYYY-MM-DD'), 'Zeleninove rizoto', '1', '1', '5');
INSERT INTO R_Objednavka(STOL, DATUM, POKRM, OBSTARAVA_ZAM, VYTVORIL_ZAM, VYTVORIL_ZAK) VALUES ('1', TO_DATE('2019-03-12','YYYY-MM-DD'), 'Kuraci steak s americkymi zemiakmi', '1', '2', '5');


INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('440', TO_DATE('2019-01-01', 'YYYY-MM-DD'), TO_DATE('14:44', 'HH24:MI'), 'kuracie platky', 'Food Trade s.r.o.', '1');
INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('52', TO_DATE('2019-01-02', 'YYYY-MM-DD'), TO_DATE('15:44', 'HH24:MI'), 'ryza', 'Local Market s.r.o.', '2');
INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('77', TO_DATE('2019-02-04', 'YYYY-MM-DD'), TO_DATE('14:35', 'HH24:MI'), 'kukurica', 'Local Market s.r.o.', '1');
INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('88', TO_DATE('2019-02-04', 'YYYY-MM-DD'), TO_DATE('14:42', 'HH24:MI'), 'americke zemiaky', 'Food Trade s.r.o.', '1');
INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('69', TO_DATE('2019-02-07', 'YYYY-MM-DD'), TO_DATE('12:32', 'HH24:MI'), 'hrasok', 'Local Market s.r.o.', '3');
INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('420', TO_DATE('2019-03-01', 'YYYY-MM-DD'), TO_DATE('14:24', 'HH24:MI'), 'kukurica', 'Local Market s.r.o.', '1');
INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('44', TO_DATE('2019-03-01', 'YYYY-MM-DD'), TO_DATE('06:45', 'HH24:MI'), 'kuracie platky', 'Food Trade s.r.o.', '1');
INSERT INTO R_Objednavka_Surovin(CENA, DATUM, CAS, SUROVINA, DODAVATEL, VYTVORIL_ZAM) VALUES ('59', TO_DATE('2019-03-12', 'YYYY-MM-DD'), TO_DATE('07:47', 'HH24:MI'), 'hrasok', 'Local Market s.r.o.', '2');


INSERT INTO R_Rezervuje(DATUM, CAS, POCET_OSOB, ZAKAZNIK, MIESTO) VALUES (TO_DATE('2018-11-12','YYYY-MM-DD'),TO_DATE('20:00', 'HH24:MI'), '1', '4', '1');
INSERT INTO R_Rezervuje(DATUM, CAS, POCET_OSOB, ZAKAZNIK, MIESTO) VALUES (TO_DATE('2019-01-07','YYYY-MM-DD'),TO_DATE('20:00', 'HH24:MI'), '1', '6', '1');
INSERT INTO R_Rezervuje(DATUM, CAS, POCET_OSOB, ZAKAZNIK, MIESTO) VALUES (TO_DATE('2019-01-08','YYYY-MM-DD'),TO_DATE('20:00', 'HH24:MI'), '4', '4', '8');
INSERT INTO R_Rezervuje(DATUM, CAS, POCET_OSOB, ZAKAZNIK, MIESTO) VALUES (TO_DATE('2019-01-08','YYYY-MM-DD'),TO_DATE('20:00', 'HH24:MI'), '1', '6', '3');
INSERT INTO R_Rezervuje(DATUM, CAS, POCET_OSOB, ZAKAZNIK, MIESTO) VALUES (TO_DATE('2019-01-08','YYYY-MM-DD'),TO_DATE('20:30', 'HH24:MI'), '1', '4', '4');

INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('10', TO_DATE('2018-04-04', 'YYYY-MM-DD'), 'bezhotovostne', '1', '4');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('14', TO_DATE('2018-04-04', 'YYYY-MM-DD'), 'bezhotovostne', '2', '5');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('4', TO_DATE('2018-04-04', 'YYYY-MM-DD'), 'bezhotovostne', '3', '5');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('19', TO_DATE('2018-07-11', 'YYYY-MM-DD'), 'bezhotovostne', '4', '6');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('2', TO_DATE('2018-04-12', 'YYYY-MM-DD'), 'bezhotovostne', '5', '5');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('13', TO_DATE('2019-01-04', 'YYYY-MM-DD'), 'bezhotovostne', '6', '5');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('50', TO_DATE('2019-01-05', 'YYYY-MM-DD'), 'bezhotovostne', '7', '6');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('15', TO_DATE('2019-01-05', 'YYYY-MM-DD'), 'bezhotovostne', '8', '4');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('14', TO_DATE('2019-02-11', 'YYYY-MM-DD'), 'bezhotovostne', '9', '4');
INSERT INTO R_Plati(CENA, DATUM, SPOSOB_PLATBY, OBJEDNAVKA, ZAKAZNIK) VALUES('13', TO_DATE('2019-03-12', 'YYYY-MM-DD'), 'bezhotovostne', '10', '4');




/*
Kteri zakaznici maji alespon 20 vernostnich bodu?  (jmeno, prijmeni)
*/
SELECT R_Osoba.MENO, R_Osoba.PRIEZVISKO
FROM R_Osoba, R_Zakaznik
WHERE R_Osoba.ID = R_Zakaznik.ID and R_Zakaznik.VERNOSTNE_BODY >= 20;

/*
Kteri zakaznici si alespon jednou objednali palacinky s dzemom? (jmeno, prijmeni)
*/
SELECT DISTINCT R_Osoba.MENO, R_Osoba.PRIEZVISKO
FROM R_Osoba, R_Zakaznik, R_Objednavka
WHERE R_Osoba.ID = R_Zakaznik.ID and R_Objednavka.VYTVORIL_ZAK = R_Zakaznik.ID and R_Objednavka.POKRM = 'Palacinky s dzemom';

/*
Kolik objednavek vytvoril kazdy zakaznik? (jmeno, prijmeni, pocet objednavek)
*/
EXPLAIN PLAN FOR
SELECT R_Osoba.ID, R_Osoba.MENO, R_Osoba.PRIEZVISKO, COUNT(R_Osoba.ID) as Pocet_objednavek
FROM R_Osoba, R_Zakaznik, R_Objednavka
WHERE R_Osoba.ID = R_Zakaznik.ID and R_Zakaznik.ID = R_Objednavka.VYTVORIL_ZAK
GROUP BY R_Osoba.ID, R_Osoba.MENO, R_Osoba.PRIEZVISKO;

select * from table(dbms_xplan.display);

CREATE INDEX osoba_index 
ON R_Osoba(ID, MENO, PRIEZVISKO);

CREATE INDEX objednavka_index 
ON R_Objednavka(VYTVORIL_ZAK);

EXPLAIN PLAN FOR
SELECT R_Osoba.ID, R_Osoba.MENO, R_Osoba.PRIEZVISKO, COUNT(R_Osoba.ID) as Pocet_objednavek
FROM R_Osoba, R_Zakaznik, R_Objednavka
WHERE R_Osoba.ID = R_Zakaznik.ID and R_Zakaznik.ID = R_Objednavka.VYTVORIL_ZAK
GROUP BY R_Osoba.ID, R_Osoba.MENO, R_Osoba.PRIEZVISKO;

select * from table(dbms_xplan.display);

/*
Kteri zakaznici si zarezervovali misto na datum 7. 1. 2019? (jmeno, prijmeni)
*/
SELECT R_Osoba.MENO, R_Osoba.PRIEZVISKO
FROM R_Osoba
WHERE EXISTS (SELECT R_Rezervuje.ZAKAZNIK FROM R_Rezervuje WHERE R_Rezervuje.DATUM = TO_DATE('2019-01-07','YYYY-MM-DD') AND R_Rezervuje.ZAKAZNIK = R_Osoba.ID);

/*
Vypis vsechny udaje o zamestnanich.
*/
SELECT *
FROM R_Zamestnanec
NATURAL JOIN R_Osoba;

/*
Kteri zakaznici si alespon jednou neco objednali a zaroven si alespon jednou zarezerovali misto? (jmeno, prijmeni)
*/
SELECT DISTINCT R_Osoba.MENO, R_Osoba.PRIEZVISKO
FROM R_Osoba, R_Rezervuje
WHERE R_Osoba.ID = R_Rezervuje.ZAKAZNIK AND R_Rezervuje.ZAKAZNIK IN
(SELECT DISTINCT R_Objednavka.VYTVORIL_ZAK
FROM R_Objednavka);


/*
Pokud je zvolene misto ve zvolene datum a cas volne, vytvori na nej rezervaci. Delka rezervac je nastavena na 2 hodiny.
*/
CREATE OR REPLACE TRIGGER rezervace_mista_trigger
BEFORE INSERT ON R_Rezervuje
FOR EACH ROW
DECLARE pocet INT;
BEGIN
    SELECT COALESCE ((
        SELECT COUNT(*) FROM R_Rezervuje
            WHERE R_Rezervuje.MIESTO = :new.MIESTO AND
            R_Rezervuje.DATUM = :new.DATUM AND
            (:new.CAS BETWEEN R_Rezervuje.CAS AND (R_Rezervuje.CAS + INTERVAL '1' HOUR + INTERVAL '59' MINUTE) OR
            :new.CAS BETWEEN (R_Rezervuje.CAS - INTERVAL '1' HOUR - INTERVAL '59' MINUTE) AND R_Rezervuje.CAS)), 0)
        INTO pocet FROM DUAL;
    IF pocet > 0 
    THEN RAISE_APPLICATION_ERROR(-20001, 'Zvolene misto je jiz zarezervovano.');
    END IF;
END;
/


/*
Vypise cisla a typy mist, ktere jsou obsazeny v zadanem datu a v zadanem casovem useku. (napr. pro system, ktery zobrazuje obsazena mista)
*/
CREATE OR REPLACE PROCEDURE Obsazenost (datum IN DATE, zacatek IN DATE, konec IN DATE)
AS
c1 SYS_REFCURSOR;
BEGIN
IF Obsazenost.zacatek > Obsazenost.konec
THEN RAISE_APPLICATION_ERROR(-20002,'Pocatecni cas je pozdeji nez koncovy cas!');
END IF;
OPEN c1 FOR
SELECT R_Miesto.ID, R_Miesto.TYP
FROM R_Miesto, R_Rezervuje
WHERE R_Miesto.ID = R_Rezervuje.MIESTO AND R_Rezervuje.DATUM = Obsazenost.datum AND (R_Rezervuje.CAS BETWEEN Obsazenost.zacatek AND Obsazenost.konec OR R_Rezervuje.CAS + INTERVAL '1' HOUR + INTERVAL '59' MINUTE BETWEEN Obsazenost.zacatek AND Obsazenost.konec);
DBMS_SQL.RETURN_RESULT(c1);
END;
/

EXECUTE Obsazenost(TO_DATE('2019-01-08','YYYY-MM-DD'), TO_DATE('19:30', 'HH24:MI'), TO_DATE('23:30', 'HH24:MI'));


/*
Vypise vsechny rezervace na dnesni den ve formatu 'typMista_cisloMista, Rezervace od casRezervace'. (napr. pro generovani cedulek, ktere budou rozmisteny na rezervovanych mistech)
*/
CREATE OR REPLACE PROCEDURE Rezervace
AS
Cedulka VARCHAR(30);
Typ_miesta R_Miesto.TYP%TYPE;
CURSOR miesta_cursor IS
SELECT R_Miesto.ID, R_Miesto.TYP, R_Rezervuje.CAS
FROM R_Miesto, R_Rezervuje
WHERE R_Miesto.ID = R_Rezervuje.MIESTO AND R_Rezervuje.DATUM = TRUNC(SYSDATE);
BEGIN
FOR ITEM IN miesta_cursor
LOOP
Rezervace.Typ_miesta := ITEM.TYP;
Rezervace.Typ_miesta := CONCAT(Rezervace.Typ_miesta, ' ');
Rezervace.Typ_miesta := CONCAT(Rezervace.Typ_miesta, ITEM.ID);
Rezervace.Cedulka := CONCAT(Rezervace.Typ_miesta, ', Rezervace od ');
Rezervace.Cedulka := CONCAT(Rezervace.Cedulka, TO_CHAR(ITEM.CAS, 'HH24:MI'));
DBMS_OUTPUT.put_line(Rezervace.Cedulka);
END LOOP;
END;
/

EXECUTE Rezervace;

INSERT INTO R_Rezervuje(DATUM, CAS, POCET_OSOB, ZAKAZNIK, MIESTO) VALUES (TO_DATE('2019-04-29','YYYY-MM-DD'),TO_DATE('15:30', 'HH24:MI'), '1', '4', '10');

GRANT INSERT ON R_Rezervuje TO XLINKA01;




CREATE MATERIALIZED VIEW Rezervace_MV
CACHE
BUILD IMMEDIATE
REFRESH ON COMMIT
AS
    SELECT *
    FROM XJURIG00.R_Rezervuje
    NATURAL JOIN XJURIG00.R_Osoba
    WHERE XJURIG00.R_Osoba.ID = XJURIG00.R_Rezervuje.ZAKAZNIK;

GRANT ALL ON Rezervace_MV TO XLINKA01;



COMMIT; 


/*
Priklad uziti materializovaneho pohledu pro uzivatele XLINKA01

SELECT * FROM XJURIG00.REZERVACE_MV; -- Vypise aktualni pohled

INSERT INTO XJURIG00.R_Rezervuje(DATUM, CAS, POCET_OSOB, ZAKAZNIK, MIESTO) VALUES (TO_DATE('2019-04-29','YYYY-MM-DD'),TO_DATE('13:37', 'HH24:MI'), '1', '4', '4');
-- Vlozi rezervaci

SELECT * FROM XJURIG00.REZERVACE_MV; -- Vypise porad puvodni pohled

COMMIT; -- Ulozi zmeny v databazi

SELECT * FROM XJURIG00.REZERVACE_MV; -- Vypise upraveny pohled obsahujici novou polozku tabulky

*/




