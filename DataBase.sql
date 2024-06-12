IF (SELECT  count(*) from mysql.user WHERE User='tournois_db_user')=0
BEGIN
	CREATE USER 'tournois_db_user'@'localhost';
        set password for 'tournois_db_user'@'localhost'=password('t0Urn!0s_DB!');
END

CREATE DATABASE IF NOT EXISTS tournois_db;

GRANT SELECT, INSERT, UPDATE, DELETE ON tournois_db.* TO 'tournois_db_user'@'localhost';

USE tournois_db;

CREATE TABLE  TournamentSiteUser (
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  CreatedOn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  LastLoggedIn DATETIME NULL ,
  EMail VARCHAR( 255 ) NOT NULL ,
  Salt VARCHAR( 255 ) NOT NULL ,
  Password VARCHAR( 32 ) NOT NULL ,
  DisplayName VARCHAR(255) NULL,
  IsAdmin TINYINT NOT NULL DEFAULT 0,
  IsRegistration TINYINT NOT NULL DEFAULT 0,
  IsWelcome TINYINT NOT NULL DEFAULT 0,
  IsWeighting TINYINT NOT NULL DEFAULT 0,
  IsMainTable TINYINT NOT NULL DEFAULT 0,
  IsMatTable TINYINT NOT NULL DEFAULT 0
);

INSERT TournamentSiteUser (EMail,Salt,Password,DisplayName,IsAdmin,IsRegistration,IsWelcome,IsWeighting,IsMainTable,IsMatTable)
VALUES('florian@dubath.org','2015-12-11','tttttttttttttttttttttttttttttttt','Florian Dubath',1,1,1,1,1,1);

CREATE TABLE TournamentGender(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  Name VARCHAR( 255 ) NOT NULL 
);

Insert TournamentGender (Name) VALUES ('Femme');
Insert TournamentGender (Name) VALUES ('Homme');
Insert TournamentGender (Name) VALUES ('Tous');

CREATE TABLE  TournamentAgeCategory (
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  Name VARCHAR( 255 ) NOT NULL ,
  ShortName VARCHAR( 255 ) NOT NULL ,
  GenderId INT NOT NULL ,
  MinAge INT NOT NULL ,
  MaxAge INT NOT NULL ,
  Duration INT NOT NULL DEFAULT 4,
  
  CONSTRAINT fk_age_cat_gen FOREIGN KEY (GenderId) REFERENCES TournamentGender(Id)
);

INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écolières D','F9',1,8,9,2);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écoliers D','M9',2,8,9,2);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écolières C','F11',1,9,11,2);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écoliers C','M11',2,9,11,2);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écolières B','F13',1,11,13,2);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écoliers B','M13',2,11,13,2);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écolières A','F15',1,13,15,3);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Écoliers A','M15',2,13,15,3);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Espoires','F18',1,15,18,4);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Espoirs','M18',2,15,18,4);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Juniores','F21',1,18,21,4);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Juniors','M21',2,18,21,4);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Élites','FE',1,21,99,4);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Élites','ME',2,21,99,4);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Vétérans','FV',1,30,99,3);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Vétérans','MV',2,30,99,3);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Open','FO',1,21,99,4);
INSERT TournamentAgeCategory(Name,ShortName,GenderId,MinAge,MaxAge,Duration) values ('Open','MO',2,21,99,4);



CREATE TABLE  TournamentCategory (
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  AgeCategoryId int not null,
  MinWeight INT  NULL ,
  MaxWeight INT  NULL,
  CONSTRAINT fk_cat_age_cat FOREIGN KEY (AgeCategoryId) REFERENCES TournamentAgeCategory(Id)
);

/* Écolières B */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,NULL,24);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,NULL,26);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,NULL,28);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,NULL,30);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,NULL,33);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,NULL,36);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,NULL,40);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (5,40, NULL);
/* Écoliers B */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,NULL,26);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,NULL,28);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,NULL,30);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,NULL,33);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,NULL,36);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,NULL,40);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,NULL,45);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (6,45, NULL);
/* Écolières A */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,30);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,33);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,36);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,40);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,44);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,48);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,52);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,NULL,57);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (7,57, NULL);
/* Écoliers A */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,NULL,33);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,NULL,36);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,NULL,40);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,NULL,45);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,NULL,50);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,NULL,55);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,NULL,60);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (8,60, NULL);

/* Espoires */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (9,NULL,44);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (9,NULL,48);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (9,NULL,52);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (9,NULL,57);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (9,NULL,63);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (9,63,NULL);
/* Espoirs */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,NULL,45);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,NULL,50);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,NULL,55);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,NULL,60);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,NULL,66);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,NULL,73);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,NULL,81);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (10,81, NULL);
/* Junior F */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (11,NULL,48);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (11,NULL,52);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (11,NULL,57);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (11,NULL,63);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (11,NULL,70);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (11,70,NULL);
/* Junior M */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (12,NULL,55);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (12,NULL,60);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (12,NULL,66);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (12,NULL,73);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (12,NULL,81);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (12,NULL,90);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (12,90, NULL);
/* Elites F */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (13,NULL,48);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (13,NULL,52);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (13,NULL,57);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (13,NULL,63);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (13,NULL,70);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (13,70,NULL);
/* Elites M */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (14,NULL,55);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (14,NULL,60);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (14,NULL,66);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (14,NULL,73);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (14,NULL,81);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (14,NULL,90);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (14,90, NULL);

/* Veteran F */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (15,NULL,48);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (15,NULL,52);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (15,NULL,57);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (15,NULL,63);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (15,NULL,70);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (15,70,NULL);
/* Veteran M */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (16,NULL,55);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (16,NULL,60);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (16,NULL,66);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (16,NULL,73);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (16,NULL,81);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (16,NULL,90);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (16,90, NULL);

/* OPEN */
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (17,NULL,NULL);
INSERT TournamentCategory (AgeCategoryId,MinWeight,MaxWeight) VALUES (18,NULL,NULL);





CREATE TABLE TournamentDoubleSatrt (
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  MainAgeCategoryId int not null,
  AcceptedAgeCategoryId int not null,
  CONSTRAINT fk_dbl_age_cat FOREIGN KEY (MainAgeCategoryId) REFERENCES TournamentAgeCategory(Id),
  CONSTRAINT fk_dbl_a_age_cat FOREIGN KEY (AcceptedAgeCategoryId) REFERENCES TournamentAgeCategory(Id) 
);

CREATE TABLE TournamentVenue(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  Name  VARCHAR( 255 ) NOT NULL ,
  Place  VARCHAR( 255 ) NOT NULL ,
  Transport  VARCHAR( 255 ) NOT NULL ,
  Organization  VARCHAR( 255 ) NOT NULL ,
  Admition  VARCHAR( 255 ) NOT NULL ,
  System  VARCHAR( 255 ) NOT NULL ,
  Prize  VARCHAR( 255 ) NOT NULL ,
  Judge VARCHAR( 255 ) NOT NULL ,
  Dressing  VARCHAR( 255 ) NOT NULL ,
  Contact  VARCHAR( 255 ) NOT NULL ,
  RegistrationEnd Date NOT NULL,
  TournamentStart Date NOT NULL,
  TournamentEnd Date NOT NULL
);

CREATE TABLE TournamentWeighting(
  AgeCategoryId  INT NOT NULL PRIMARY KEY ,
  WeightCategoryBasedOnAttendence TINYINT NOT NULL DEFAULt 0,
  WeightingBegin DATETIME NOT NULL,
  WeightingEnd DATETIME NOT NULL,
  CONSTRAINT fk_wgt_age_cat FOREIGN KEY (AgeCategoryId) REFERENCES TournamentAgeCategory(Id)
);


CREATE TABLE TournamentGrade(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  Name VARCHAR( 255 ) NOT NULL ,
  CollectVP TINYINT NOT NULL DEFAULT 0
);

INSERT TournamentGrade (Name) VALUES('6e Kyu');
INSERT TournamentGrade (Name) VALUES('5e Kyu');
INSERT TournamentGrade (Name) VALUES('4e Kyu');
INSERT TournamentGrade (Name) VALUES('3e Kyu');
INSERT TournamentGrade (Name) VALUES('2e Kyu');
INSERT TournamentGrade (Name,CollectVP) VALUES('1er Kyu',1);
INSERT TournamentGrade (Name,CollectVP) VALUES('Dan',1);

CREATE TABLE TournamentClub(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  Name VARCHAR( 255 ) NOT NULL ,
  Contact VARCHAR( 255 ) NULL 
);

CREATE TABLE TournamentCompetitor(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY , 
  StrId  VARCHAR( 12 ) NOT NULL,
  Name VARCHAR( 255 ) NOT NULL,
  Surname VARCHAR( 255 ) NOT NULL,
  Birth  Date NOT NULL, 
  GenderId INT NOT NULL , 
  LicenceNumber INT NOT NULL , 
  GradeId  INT NOT NULL , 
  ClubId  INT NOT NULL , 
  CheckedIn TINYINT NOT NULL DEFAULT 0,
  
  CONSTRAINT fk_comp_gen FOREIGN KEY (GenderId) REFERENCES TournamentGender(Id),
  CONSTRAINT fk_comp_grade FOREIGN KEY (GradeId) REFERENCES TournamentGrade(Id),
  CONSTRAINT fk_comp_club FOREIGN KEY (ClubId) REFERENCES TournamentClub(Id)
);  


CREATE TABLE TournamentRegistration(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  CompetitorId INT NOT NULL, 
  CategoryId INT NOT NULL,
  Payed TINYINT NOT NULL DEFAULT 0,
  WeightChecked TINYINT NOT NULL DEFAULT 0,
  CONSTRAINT fk_reg_com FOREIGN KEY (CompetitorId) REFERENCES TournamentCompetitor(Id),
  CONSTRAINT fk_reg_cat FOREIGN KEY (CategoryId) REFERENCES  TournamentCategory(Id)
);




INSERT INTO TournamentVenue(Name, Place, Transport, Organization, Admition, System, Prize, Judge, Dressing, Contact, RegistrationEnd, TournamentStart, TournamentEnd) VALUES (
     '49e Championnats Genevois Individuels de Judo',
     'Centre Omnisport du Sapay, ch. le Sapay 3, 1212 Grand-Lancy',
     'Transports publics recommandés. Arrêt Tpg Lancy-Bachet ou gare CEVA Lancy-Bachet (10mn à pied). Accès et parking difficiles pour les voitures.',
     'ACGJJJ',
     'Membre d\'un club de l\'association cantonale genevoise de judo et ju-jitsu Licence annuelle 2022 obligatoire. Ceux nés en 2012 ne sont pas autorisés à combattre.',
     'Compétitions individuelles. Pool jusqu\'à cinq combattants. Dès six combattants : pools au premier tour puis tableau sans repêchage.',
     'Une médaille pour les quatre premiers + le titre de « champion(ne) genevois(e) pour la première place.',
     'Assurés par les arbitres officiels de la Fédération Suisse de Judo.',
     'Judogi blanc uniquement. Cheveux longs attachés (chignon). T-shirt blanc pour les combattantes.',
     'info@acgjjj.ch, Alexandre Perles 079 260 79 67, Stéphane Fischer 077 421 15 67',
     '2022-03-20',
     '2022-03-27',
     '2022-03-27'   
);


CREATE VIEW V_Category AS 
select
     TournamentCategory.Id as CategoryId,
     CompetitorId as CompetitorId,
     WeightChecked as WeightChecked,
     WeightingEnd as WeightingEnd
FROM TournamentCategory
INNER JOIN TournamentAgeCategory on TournamentAgeCategory.Id = TournamentCategory.AgeCategoryId
INNER JOIN TournamentWeighting on TournamentAgeCategory.Id = TournamentWeighting.AgeCategoryId
INNER JOIN TournamentRegistration on TournamentCategory.Id = TournamentRegistration.CategoryId;

CREATE VIEW V_Age_Reg AS
SELECT DISTINCT  
    TournamentRegistration.CompetitorId as CompetitorId,
    TournamentCategory.AgeCategoryId as AgeCategoryId
FROM TournamentRegistration
INNER JOIN TournamentCategory on TournamentCategory.Id = TournamentRegistration.CategoryId;




CREATE TABLE ActualCategory(
  Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  CategoryId  INT NOT NULL,
  Category2Id  INT NULL,
  Name VARCHAR( 255 ) NOT NULL,
  IsCompleted TINYINT DEFAULT 0,
  
  CONSTRAINT fk_ackc_cat1 FOREIGN KEY (CategoryId) REFERENCES  TournamentCategory(Id),
  CONSTRAINT fk_ackc_cat2 FOREIGN KEY (Category2Id) REFERENCES  TournamentCategory(Id)
);

CREATE TABLE CategoryStepsType( 
  Id INT NOT NULL PRIMARY KEY ,
  Name VARCHAR( 255 ) NOT NULL
);




INSERT CategoryStepsType (Id,Name) VALUES (1,'SingleFight');
INSERT CategoryStepsType (Id,Name) VALUES (3,'Pool_3');
INSERT CategoryStepsType (Id,Name) VALUES (4,'Pool_4');
INSERT CategoryStepsType (Id,Name) VALUES (5,'Pool_5');

CREATE TABLE CategoryStep( 
    Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
    Name VARCHAR( 255 ) NOT NULL,
    ActualCategoryId INT NOT NULL,
    CategoryStepsTypeId INT NOT NULL,
    CONSTRAINT fk_cs_cat FOREIGN KEY (ActualCategoryId) REFERENCES  ActualCategory(Id),
    CONSTRAINT fk_cs_st FOREIGN KEY (CategoryStepsTypeId) REFERENCES  CategoryStepsType(Id)
);






CREATE TABLE StepLinking(
     ActualCategoryId INT NOT NULL,
     out_step_id INT NOT NULL,
     in_step_1_id INT NOT NULL,
     rank_in_step_1 INT NOT NULL,
     in_step_2_id INT NOT NULL,
     rank_in_step_2 INT NOT NULL,
     CONSTRAINT pk_csa PRIMARY KEY (ActualCategoryId, out_step_id,in_step_1_id,rank_in_step_1,in_step_2_id,rank_in_step_2)
     
);


CREATE TABLE Fight(
     Id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
     ActualCategoryId INT NOT NULL,
     step_id INT NOT NULL,
     TournamentCompetitor1Id INT NULL,
     pv1 INT  NULL,
     TournamentCompetitor2Id INT NULL,
     pv2 INT  NULL,
     CONSTRAINT fk_f_cat FOREIGN KEY (ActualCategoryId) REFERENCES  ActualCategory(Id),
     CONSTRAINT fk_f_stp FOREIGN KEY (step_id) REFERENCES  CategoryStep(Id),
     CONSTRAINT fk_f_tc1 FOREIGN KEY (TournamentCompetitor1Id) REFERENCES  TournamentCompetitor(Id),
     CONSTRAINT fk_f_tc2 FOREIGN KEY (TournamentCompetitor2Id) REFERENCES  TournamentCompetitor(Id)
);





CREATE TABLE ActualCategoryResult(
     ActualCategoryId INT NOT NULL,
     Competitor1Id INT NOT NULL,
     RankId INT NOT NULL,
     CONSTRAINT fk_acr_cat FOREIGN KEY (ActualCategoryId) REFERENCES  ActualCategory(Id),
     CONSTRAINT fk_acr_tc FOREIGN KEY (Competitor1Id) REFERENCES  TournamentCompetitor(Id)
);





/* Cleaning
DELETE FROM ActualCategoryResult;
DELETE FROM Fight;
DELETE FROM StepLinking;
DELETE FROM CategoryStep;
DELETE FROM ActualCategory;
DELETE FROM TournamentCompetitor;*/


// Club
INSERT INTO TournamentClub (Id ,Name) VALUES(1, 'BUDO SCHOOLS ASHITA');
INSERT INTO TournamentClub (Id ,Name) VALUES(2, 'DOJO LANCY–PALETTES');
INSERT INTO TournamentClub (Id ,Name) VALUES(3, 'ECOLE DE JUDO DE COLLONGE-BELLERIVE');
INSERT INTO TournamentClub (Id ,Name) VALUES(4, 'GOSHINJUTSU-KWAI DARDAGNY');
INSERT INTO TournamentClub (Id ,Name) VALUES(5, 'JU-JITSU JUDO CLUB COMPESIÈRES');
INSERT INTO TournamentClub (Id ,Name) VALUES(6, 'JUDO CLUB BUDOKAN VERNIER');
INSERT INTO TournamentClub (Id ,Name) VALUES(7, 'JUDO CLUB CAROUGE');
INSERT INTO TournamentClub (Id ,Name) VALUES(8, 'JUDO CLUB DES EAUX-VIVES');
INSERT INTO TournamentClub (Id ,Name) VALUES(9, 'JUDO CLUB GENEVE');
INSERT INTO TournamentClub (Id ,Name) VALUES(10, 'JUDO CLUB GRAND-SACONNEX');
INSERT INTO TournamentClub (Id ,Name) VALUES(11, 'JUDO CLUB LE SAMOURAI BERNEX');
INSERT INTO TournamentClub (Id ,Name) VALUES(12, 'JUDO CLUB LEMANIQUE');
INSERT INTO TournamentClub (Id ,Name) VALUES(13, 'JUDO CLUB MEYRIN');
INSERT INTO TournamentClub (Id ,Name) VALUES(14, 'JUDO CLUB SATIGNY');
INSERT INTO TournamentClub (Id ,Name) VALUES(15, 'JUDO CLUB TROIS-CHÊNES');
INSERT INTO TournamentClub (Id ,Name) VALUES(16, 'JUDO CLUB VERSOIX');
INSERT INTO TournamentClub (Id ,Name) VALUES(17, 'JUDO KWAI LANCY');
INSERT INTO TournamentClub (Id ,Name) VALUES(18, 'JUDO PREGNY-CHAMBESY');
INSERT INTO TournamentClub (Id ,Name) VALUES(19, 'SHINBUDO JUDO');
INSERT INTO TournamentClub (Id ,Name) VALUES(20, 'SHINBUDO-ONEX');
INSERT INTO TournamentClub (Id ,Name) VALUES(21, 'SHUNG DO KWAN BUDO')



// Audit
Alter table TournamentCompetitor add column CheckedInBy INT NULL;
Alter table TournamentRegistration add column WeightCheckedBy INT NULL;
Alter table Fight add column ResultSavedBy INT NULL;



Alter Table Fight ADD column TieBreakFight TINYINT NOT NULL DEFAULT 0;

CREATE TABLE OTA(
     StrId  VARCHAR( 12 ) PRIMARY KEY,
     UserId INT NOT NULL,
     CreatedOn DateTime NOT NULL DEFAULT CURRENT_TIMESTAMP,
     Used TINYINT NOT NULL DEFAULT 0,
     SId  VARCHAR( 12 ) NULL,
     CONSTRAINT fk_ota_us FOREIGN KEY (userId) REFERENCES  TournamentSiteUser(Id)
);

