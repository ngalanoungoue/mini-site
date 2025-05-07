-- Création de la table article
CREATE TABLE article (
    Id_article INT PRIMARY KEY AUTO_INCREMENT,
    Description VARCHAR(255) NOT NULL,
    Prix_unitaire FLOAT NOT NULL CHECK (Prix_unitaire >= 0),
    Stock INT NOT NULL CHECK (Stock >= 0)
) ENGINE=InnoDB;

-- Création de la table client
CREATE TABLE client (
    Id_client INT PRIMARY KEY AUTO_INCREMENT,
    Nom VARCHAR(255) NOT NULL,
    Ville VARCHAR(255) NOT NULL,
    Téléphone VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

-- Création de la table commande
CREATE TABLE commande (
    Id_commande INT PRIMARY KEY AUTO_INCREMENT,
    Id_client INT,
    Date DATE NOT NULL,
    FOREIGN KEY (Id_client) REFERENCES client(Id_client) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Création de la table ligne_commande
CREATE TABLE ligne_commande (
    Id_article INT,
    Id_commande INT,
    Quantité INT NOT NULL CHECK (Quantité > 0),
    PRIMARY KEY (Id_article, Id_commande),
    FOREIGN KEY (Id_article) REFERENCES article(Id_article) ON DELETE CASCADE,
    FOREIGN KEY (Id_commande) REFERENCES commande(Id_commande) ON DELETE CASCADE
) ENGINE=InnoDB;