DROP TABLE IF EXISTS BON_DE_COMMANDE;
DROP TABLE IF EXISTS INSCRIPTION;
DROP TABLE IF EXISTS PRODUIT;
DROP TABLE IF EXISTS EVENEMENT;
DROP TABLE IF EXISTS COMMANDE;
DROP TABLE IF EXISTS MEMBRE;
DROP TABLE IF EXISTS PROMO;
DROP TABLE IF EXISTS GRADE;
DROP TABLE IF EXISTS ROLE;

CREATE TABLE GRADE(
   Id_Grade INT AUTO_INCREMENT,
   Nom_Grade VARCHAR(50) NOT NULL,
   Prix_Grade DECIMAL(15,2) NOT NULL,
   Description_Grade VARCHAR(150),
   Avantage_Grade DECIMAL(15,2),
   PRIMARY KEY(Id_Grade),
   UNIQUE(Nom_Grade)
);

CREATE TABLE ROLE(
   Id_Role INT AUTO_INCREMENT,
   Nom_Role VARCHAR(50) NOT NULL,
   Description_Role VARCHAR(150),
   PRIMARY KEY(Id_Role),
   UNIQUE(Nom_Role)
);

CREATE TABLE PRODUIT(
   Id_Produit INT AUTO_INCREMENT,
   Nom_Produit VARCHAR(50) NOT NULL,
   Prix_Produit DECIMAL(15,2) NOT NULL,
   Description_Produit VARCHAR(150),
   Stock_Produit INT NOT NULL,
   Img_Produit VARCHAR(250),
   PRIMARY KEY(Id_Produit)
);

CREATE TABLE EVENEMENT(
   Id_Event INT AUTO_INCREMENT,
   Nom_Event VARCHAR(50) NOT NULL,
   Prix_Event DECIMAL(15,2) NOT NULL,
   Description_Event VARCHAR(150),
   Nb_Place_Event INT NOT NULL,
   Date_Fin_Inscription DATETIME NOT NULL,
   Date_Event DATETIME NOT NULL,
   PRIMARY KEY(Id_Event)
);

CREATE TABLE PROMO(
   Id_Promo INT AUTO_INCREMENT,
   Nom_Promo VARCHAR(50) NOT NULL,
   Pourcentage_Promo DECIMAL(15,2) NOT NULL,
   PRIMARY KEY(Id_Promo)
);

CREATE TABLE MEMBRE(
   Id_Membre INT AUTO_INCREMENT,
   Nom_Membre VARCHAR(50) NOT NULL,
   Mail_Membre VARCHAR(50) NOT NULL,
   Mdp_Membre VARCHAR(50) NOT NULL,
   Grp_Membre VARCHAR(50) NOT NULL,
   Pdp_Membre VARCHAR(120) NOT NULL,
   Id_Grade INT,
   Id_Role INT,
   PRIMARY KEY(Id_Membre),
   UNIQUE(Nom_Membre),
   UNIQUE(Mail_Membre),
   FOREIGN KEY(Id_Grade) REFERENCES GRADE(Id_Grade),
   FOREIGN KEY(Id_Role) REFERENCES ROLE(Id_Role)
);

CREATE TABLE COMMANDE(
   Id_Commande INT AUTO_INCREMENT,
   Statut_Commande VARCHAR(50) NOT NULL,
   Date_Commande DATETIME NOT NULL,
   Id_Promo INT,
   Id_Membre INT NOT NULL,
   PRIMARY KEY(Id_Commande),
   FOREIGN KEY(Id_Promo) REFERENCES PROMO(Id_Promo),
   FOREIGN KEY(Id_Membre) REFERENCES Membre(Id_Membre)
);

CREATE TABLE BON_DE_COMMANDE(
   Id_Bon_Cmd INT AUTO_INCREMENT,
   Qte_Produit INT NOT NULL,
   Id_Commande INT NOT NULL,
   Id_Produit INT NOT NULL,
   PRIMARY KEY(Id_Bon_Cmd),
   UNIQUE(Id_Commande),
   FOREIGN KEY(Id_Commande) REFERENCES COMMANDE(Id_Commande),
   FOREIGN KEY(Id_Produit) REFERENCES PRODUIT(Id_Produit)
);

CREATE TABLE INSCRIPTION(
   Id_Inscription INT AUTO_INCREMENT,
   Qte_Inscription INT NOT NULL,
   Id_Commande INT NOT NULL,
   Id_Event INT NOT NULL,
   PRIMARY KEY(Id_Inscription),
   UNIQUE(Id_Commande),
   FOREIGN KEY(Id_Commande) REFERENCES COMMANDE(Id_Commande),
   FOREIGN KEY(Id_Event) REFERENCES EVENEMENT(Id_Event)
);


-- Insertion pour la table GRADE
INSERT INTO GRADE (Nom_Grade, Prix_Grade, Description_Grade, Avantage_Grade)
VALUES	
('Fer', 5, 'Fais vivre le BDE !', 0),
('Or', 10, 'Adhesion au BDE, Grade premium sur le serveur minecraft de l ADIIL', 0),
('Diamant', 13, 'Adhesion au BDE, Grade premium sur le serveur minecraft de l ADIIL, 10% de remise sur tout les achats du site !', 10);

-- Insertion pour la table ROLE
INSERT INTO ROLE (Nom_Role, Description_Role)
VALUES				
('Visiteur', 'Utilisateur n ayant pas achete de grade sur le site de l ADIIL'),
('Membre', 'Utilisateur ayant un grade sur le site '),
('Administrateur', 'Utilisateur ayant des permissions administrateur sur le site');

-- Insertion pour la table PRODUIT
INSERT INTO PRODUIT (Nom_Produit, Prix_Produit, Description_Produit, Stock_Produit, Img_Produit)
VALUES
('T-shirt Etudiant', 15.00, 'T-shirt officiel de l association ADIIL', 100, 'tshirt.png'),
('Cle USB 32Go', 10.00, 'Cle USB avec logo de l association', 200, 'cle_usb.png'),
('Sweat a capuche', 30.00, 'Sweat a capuche aux couleurs de l association', 50, 'sweat.png'),
('Sac a dos', 25.00, 'Sac a dos avec logo de l association', 40, 'sac_a_dos.png'),
('Mug', 8.00, 'Mug aux couleurs de l association', 120, 'mug.png'),
('Carnet de notes', 5.00, 'Carnet de notes avec le logo de l association', 150, 'carnet.png'),
('Stylo personnalise', 2.00, 'Stylo avec le nom de l association', 300, 'stylo.png'),
('Cafe', 0.30, 'Cafe 12cl', 80, 'cafe.png'),
('Coca-Cola', 1.00, 'Canette de Coca-Cola 33cl', 20, 'coca-cola.png'),
('Napolitain', 0.250, 'Gateau Napolitain de Lu', 60, 'bouteille.png');

-- Insertion pour la table EVENEMENT
INSERT INTO EVENEMENT (Nom_Event, Prix_Event, Description_Event, Nb_Place_Event, Date_Fin_Inscription, Date_Event)
VALUES
('Soiree d integration', 5.00, 'Une soiree pour accueillir les nouveaux etudiants en informatique', 100, '2024-09-10T00:00:00', '2024-09-15T19:30:00'),
('Conference sur l IA', 0.00, 'Conference gratuite sur les dernieres tendances de l Intelligence Artificielle', 200, '2024-10-05T00:00:00', '2024-10-10T10:00:00'),
('Hackathon', 10.00, 'Concours de programmation sur 48 heures', 50, '2024-11-01T00:00:00', '2024-11-05T13:00:00'),
('Atelier Git et DevOps', 0.00, 'Atelier pratique sur les outils de gestion de version et l integration continue', 30, '2024-12-01T00:00:00', '2024-12-05T13:45:00'),
('Tournoi de jeux video', 3.00, 'Tournoi de jeux video pour tous les etudiants', 80, '2024-12-15T00:00:00', '2024-12-20T18:15:00'),
('Karting', 25.00, 'Soiree karting pour les etudiants en Informatique', 40, '2024-06-01T00:00:00', '2024-06-10T19:45:00'),
('Journee portes ouvertes', 0.00, 'Inscription pour participer a l organisation des JPO', 0, '2024-04-20T00:00:00', '2024-04-25T08:00:00'),
('Atelier CV et LinkedIn', 0.00, 'Atelier pour ameliorer son CV et son profil LinkedIn', 40, '2024-03-10T00:00:00', '2024-03-15T09:00:00'),
('Rencontre avec les anciens etudiants', 0.00, 'Echange avec les anciens etudiants du departement informatique', 100, '2024-05-01T00:00:00', '2024-05-05T17:30:00'),
('Soiree de fin d annee', 10.00, 'Grande soiree pour celebrer la fin de l annee universitaire', 150, '2024-06-15T00:00:00', '2024-06-20T19:30:00');

-- Insertion pour la table PROMO
INSERT INTO PROMO (Nom_Promo, Pourcentage_Promo)
VALUES
('RENTREE2024', 10.00),
('HACKATON', 15.00),
('IA', 20.00),
('Promo Etudiant Fidele', 5.00),
('BLACKFRIDAY', 30.00),
('NOEL2024', 25.00),
('REDUC5', 5.00),
('REINSCRIPTION', 10.00),
('HALLOWEEN2024', 20.00),
('SUMMER', 15.00);

-- Insertion pour la table MEMBRE
INSERT INTO MEMBRE (Nom_Membre, Mail_Membre, Mdp_Membre, Grp_Membre, Pdp_Membre, Id_Grade, Id_Role)
VALUES
('Lucas Dupont', 'lucas.dupont@example.com', 'password123', 'TP11A', 'lucas_profile.png', NULL, 1),
('Marie Lefevre', 'marie.lefevre@example.com', 'password456', 'TP22C', 'marie_profile.png', 3, 2),
('Antoine Martin', 'antoine.martin@example.com', 'password789', 'TP11B', 'antoine_profile.png', 2, 2),
('Claire Dubois', 'claire.dubois@example.com', '000000000', 'TP31A', 'claire_profile.png', 1, 2),
('Sophie Bernard', 'sophie.bernard@example.com', '987654321', 'TP32D', 'sophie_profile.png', NULL, 1),
('Thomas Leroy', 'thomas.leroy@example.com', 'gjosfezg526', 'TP11B', 'thomas_profile.png', 3, 2),
('Julie Moreau', 'julie.moreau@example.com', '1d5f2e9s3qf', 'Enseignant', 'julie_profile.png', 2, 2),
('Pierre Simon', 'pierre.simon@example.com', 'password', 'TP12D', 'pierre_profile.png', NULL, 1),
('Nicolas Petit', 'nicolas.petit@example.com', 'bonjour', 'TP22C', 'nicolas_profile.png', 2, 2),
('Emma Roux', 'emma.roux@example.com', '123456789', 'TP31B', 'emma_profile.png', 3, 3);

-- Insertion pour la table COMMANDE 
INSERT INTO COMMANDE (Statut_Commande, Date_Commande, Id_Promo, Id_Membre)
VALUES
('Recuperee', '2024-09-10T17:39:24', NULL, 1),   
('Recuperee', '2024-10-01T09:04:48', 2, 2),      
('En cours', '2024-09-12T11:25:30', NULL, 3),    
('Recuperee', '2024-09-13T13:01:00', 4, 4),      
('Recuperee', '2024-12-14T14:43:16', NULL, 5),   
('Recuperee', '2024-05-30T22:20:05', 6, 6),      
('Recuperee', '2024-04-15T20:17:59', NULL, 7),   
('En cours', '2024-09-17T15:05:50', 8, 8),       
('Recuperee', '2024-04-30T19:17:39', NULL, 9),   
('En cours', '2024-09-19T10:34:38', NULL, 10),  
('En cours', '2024-10-01T09:00:00', NULL, 1), 
('Recuperee', '2024-10-02T10:30:00', 3, 2),     
('En cours', '2024-10-03T11:00:00', NULL, 3),   
('Prete', '2024-10-04T12:15:00', 4, 4),         
('Recuperee', '2024-10-05T13:20:00', NULL, 5),  
('Recuperee', '2024-09-09T14:30:00', 7, 6),     
('Recuperee', '2024-10-29T15:45:00', 9, 7),     
('Recuperee', '2024-11-30T16:50:00', NULL, 8),  
('Recuperee', '2024-03-09T17:55:00', NULL, 9),  
('Recuperee', '2024-06-14T18:00:00', 10, 10);   



-- Insertion pour la table BON_DE_COMMANDE 
INSERT INTO BON_DE_COMMANDE (Qte_Produit,Id_Commande, Id_Produit)
VALUES
(2, 1, 1),  
(1, 3, 3),  
(1, 4, 4),  
(2, 8, 8),  
(4, 10, 10), 
(3, 11, 2),  
(1, 12, 5),  
(2, 13, 7),  
(5, 14, 6),  
(1, 15, 9); 


-- Insertion pour la table INSCRIPTION
INSERT INTO INSCRIPTION (Qte_Inscription, Id_Commande, Id_Event)
VALUES
(1, 2, 2),  
(3, 5, 5),  
(1, 6, 6),  
(2, 7, 7),  
(1, 9, 9),  
(4, 16, 1),  
(2, 17, 3),  
(3, 18, 4),  
(1, 19, 8),  
(5, 20, 10);