-- Suppression si les tables existent déjà (à exécuter avec prudence)
DROP TABLE IF EXISTS ligne_commande, commande, article, client CASCADE;

-- Création de la table article avec ID auto-incrémenté
CREATE TABLE article (
    id_article SERIAL PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    prix_unitaire FLOAT NOT NULL CHECK (prix_unitaire >= 0),
    stock INT NOT NULL CHECK (stock >= 0)
);

-- Création de la table client
CREATE TABLE client (
    id_client SERIAL PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    ville VARCHAR(255) NOT NULL,
    téléphone VARCHAR(20) NOT NULL
);

-- Création de la table commande
CREATE TABLE commande (
    id_commande SERIAL PRIMARY KEY,
    id_client INT NOT NULL,
    date DATE NOT NULL,
    FOREIGN KEY (id_client) REFERENCES client(id_client) ON DELETE CASCADE
);

-- Création de la table ligne_commande
CREATE TABLE ligne_commande (
    id_article INT,
    id_commande INT,
    quantité INT NOT NULL CHECK (quantité > 0),
    PRIMARY KEY (id_article, id_commande),
    FOREIGN KEY (id_article) REFERENCES article(id_article) ON DELETE CASCADE,
    FOREIGN KEY (id_commande) REFERENCES commande(id_commande) ON DELETE CASCADE
);
