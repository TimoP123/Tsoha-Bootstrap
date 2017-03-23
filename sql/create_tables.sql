CREATE TABLE Kayttaja (
	id SERIAL PRIMARY KEY,
	nimi varchar(30) NOT NULL,
	email varchar(50) NOT NULL,
	salasana varchar(50) NOT NULL,
	taso INTEGER DEFAULT 1
);

CREATE TABLE Resepti (
	id SERIAL PRIMARY KEY,
	tekijaId INTEGER REFERENCES Kayttaja(id),
	nimi varchar(50) NOT NULL,
	ohje varchar(1000) NOT NULL
);

CREATE TABLE Aine (
	id SERIAL PRIMARY KEY,
	nimi varchar(30) NOT NULL
);

CREATE TABLE Tag (
	id SERIAL PRIMARY KEY,
	nimi varchar(30) NOT NULL
);

CREATE TABLE Reseptiaine (
	id SERIAL PRIMARY KEY,
	reseptiId INTEGER REFERENCES Resepti(id),
	aineId INTEGER REFERENCES Aine(id),
	maara varchar(30) NOT NULL
);

CREATE TABLE Reseptitag (
	id SERIAL PRIMARY KEY,
	reseptiId INTEGER REFERENCES Resepti(id),
	tagId INTEGER REFERENCES Tag(id)
);


