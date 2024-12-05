
-- Create the database
CREATE DATABASE IF NOT EXISTS rooster_maker;
USE rooster_maker;

-- Table for classes
CREATE TABLE IF NOT EXISTS klassen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(50) NOT NULL,
    aantal_leerlingen INT NOT NULL
);

-- Table for teachers
CREATE TABLE IF NOT EXISTS docenten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(50) NOT NULL,
    vak VARCHAR(50) NOT NULL,
    minimale_uren INT NOT NULL
);

-- Table for classrooms
CREATE TABLE IF NOT EXISTS lokalen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(50) NOT NULL,
    type VARCHAR(50) NOT NULL,
    zitplaatsen INT NOT NULL
);

-- Table for subjects
CREATE TABLE IF NOT EXISTS vakken (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(50) NOT NULL,
    uren_per_week INT NOT NULL,
    docent_id INT,
    FOREIGN KEY (docent_id) REFERENCES docenten(id)
);

-- Table for timetable slots
CREATE TABLE IF NOT EXISTS tijdsloten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dag VARCHAR(20) NOT NULL,
    starttijd TIME NOT NULL,
    eindtijd TIME NOT NULL
);

-- Table for schedules
CREATE TABLE IF NOT EXISTS roosters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klas_id INT,
    tijdslot_id INT,
    lokaal_id INT,
    vak_id INT,
    docent_id INT,
    FOREIGN KEY (klas_id) REFERENCES klassen(id),
    FOREIGN KEY (tijdslot_id) REFERENCES tijdsloten(id),
    FOREIGN KEY (lokaal_id) REFERENCES lokalen(id),
    FOREIGN KEY (vak_id) REFERENCES vakken(id)
    FOREIGN KEY (docent_id) REFERENCES docenten(id)
);


-- Vul de tabel `klassen`
INSERT INTO klassen (naam, aantal_leerlingen)
VALUES
    ('23SD-A', 16),
    ('23SD-B', 14);

-- Vul de tabel `docenten`
INSERT INTO docenten (naam, vak, minimale_uren)
VALUES
    ('Jeffry Visser', 'Programmeren', 10),
    ('Jos Bleeker', 'Programmeren', 10),
    ('Elice Patoui', 'Engels', 2),
    ('Elee Oulas', 'Wiskunde', 1),
    ('Amier Juman', 'Nederlands', 2);

-- Vul de tabel `lokalen`
INSERT INTO lokalen (naam, type, zitplaatsen)
VALUES
    ('B221-1', 'Praktijklokaal', 36),
    ('B221-2', 'Vaklokaal', 18),
    ('A228', 'Vaklokaal', 30);

-- Vul de tabel `vakken`
INSERT INTO vakken (naam, uren_per_week, docent_id)
VALUES
    ('Wiskunde', 1, (SELECT id FROM docenten WHERE naam = 'Elee Oulas')),
    ('Programmeren', 10, (SELECT id FROM docenten WHERE naam = 'Jeffry Visser')),
    ('Praktijk', 3, (SELECT id FROM docenten WHERE naam = 'Jos Bleeker')),
    ('Engels', 2, (SELECT id FROM docenten WHERE naam = 'Elice Patoui')),
    ('Nederlands', 2, (SELECT id FROM docenten WHERE naam = 'Amier Juman')),
    ('Mentoruur', 1, NULL), -- Als mentor geen specifieke docent heeft
    ('SVU', 1, NULL);

-- Vul de tabel `tijdsloten`
INSERT INTO tijdsloten (dag, starttijd, eindtijd)
VALUES
    ('maandag', '09:00', '10:00'),
    ('maandag', '10:00', '11:00'),
    ('maandag', '11:00', '12:00'),
    ('maandag', '12:00', '13:00'),
    ('maandag', '13:00', '14:00'),
    ('maandag', '14:00', '15:00'),
    ('maandag', '15:00', '16:00'),
    ('maandag', '16:00', '17:00'),
    
    ('dinsdag', '09:00', '10:00'),
    ('dinsdag', '10:00', '11:00'),
    ('dinsdag', '11:00', '12:00'),
    ('dinsdag', '12:00', '13:00'),
    ('dinsdag', '13:00', '14:00'),
    ('dinsdag', '14:00', '15:00'),
    ('dinsdag', '15:00', '16:00'),
    ('dinsdag', '16:00', '17:00'),
    
    ('woensdag', '09:00', '10:00'),
    ('woensdag', '10:00', '11:00'),
    ('woensdag', '11:00', '12:00'),
    ('woensdag', '12:00', '13:00'),
    ('woensdag', '13:00', '14:00'),
    ('woensdag', '14:00', '15:00'),
    ('woensdag', '15:00', '16:00'),
    ('woensdag', '16:00', '17:00'),
    
    ('donderdag', '09:00', '10:00'),
    ('donderdag', '10:00', '11:00'),
    ('donderdag', '11:00', '12:00'),
    ('donderdag', '12:00', '13:00'),
    ('donderdag', '13:00', '14:00'),
    ('donderdag', '14:00', '15:00'),
    ('donderdag', '15:00', '16:00'),
    ('donderdag', '16:00', '17:00'),
    
    ('vrijdag', '09:00', '10:00'),
    ('vrijdag', '10:00', '11:00'),
    ('vrijdag', '11:00', '12:00'),
    ('vrijdag', '12:00', '13:00'),
    ('vrijdag', '13:00', '14:00'),
    ('vrijdag', '14:00', '15:00'),
    ('vrijdag', '15:00', '16:00'),
    ('vrijdag', '16:00', '17:00');


-- Vul de tabel `roosters` met een paar voorbeeldrecords
INSERT INTO roosters (klas_id, tijdslot_id, lokaal_id, vak_id)
VALUES
    ((SELECT id FROM klassen WHERE naam = '23SD-A'), 1, (SELECT id FROM lokalen WHERE naam = 'B221-1'), (SELECT id FROM vakken WHERE naam = 'Programmeren')),
    ((SELECT id FROM klassen WHERE naam = '23SD-A'), 2, (SELECT id FROM lokalen WHERE naam = 'B221-2'), (SELECT id FROM vakken WHERE naam = 'Wiskunde')),
    ((SELECT id FROM klassen WHERE naam = '23SD-B'), 3, (SELECT id FROM lokalen WHERE naam = 'A228'), (SELECT id FROM vakken WHERE naam = 'Engels')),
    ((SELECT id FROM klassen WHERE naam = '23SD-B'), 4, (SELECT id FROM lokalen WHERE naam = 'B221-1'), (SELECT id FROM vakken WHERE naam = 'Praktijk')),
    ((SELECT id FROM klassen WHERE naam = '23SD-B'), 5, (SELECT id FROM lokalen WHERE naam = 'B221-2'), (SELECT id FROM vakken WHERE naam = 'Nederlands'));
