-- Kayttaja-taulun testidata
INSERT INTO Kayttaja (nimi, email, salasana, taso) VALUES
('aDmin', 'admin@ruokaa.net', 'iAmHungry', 2),
('Heikki', 'heikki@ankkalinna.net', 'Hahhaa', 1);

-- Resepti-taulun testidata
INSERT INTO Resepti (tekijaId, nimi, ohje) VALUES
(2, 'Keitetyt perunat', 'Pese perunat hyvin. Laita perunat suolalla maustettuun kiehuvaan veteen. Keitä kunnes perunat ovat kypsiä. Nauti voinokareen kera.');

-- Aine-taulun testidata
INSERT INTO Aine (nimi) VALUES
('peruna'),
('vesi'),
('suola'),
('voi');

-- Tag-taulun testidata
INSERT INTO Tag (nimi) VALUES
('lisuke'),
('helppo');

-- Reseptiaine-taulun testidata
INSERT INTO Reseptiaine (reseptiId, aineId, maara) VALUES
(1, 1, 'kilo'),
(1, 2, '1,5 litraa'),
(1, 3, 'tl'),
(1, 4, 'muutama nokare');

-- Reseptitag-taulun testidata
INSERT INTO Reseptitag (reseptiId, tagId) VALUES
(1, 1),
(1, 2);
