<?php
/**
 * Application de Vente en PHP
 * -----------------------------
 * Gestion des articles, facturation et fournisseurs.
 *
 * Structure des dossiers :
 *
 * vente_app/
 * ├── config/
 * │   └── database.php       // Configuration de la base de données
 * ├── controllers/
 * │   ├── ArticleController.php
 * │   ├── FactureController.php
 * │   └── FournisseurController.php
 * ├── models/
 * │   ├── Article.php
 * │   ├── Facture.php
 * │   └── Fournisseur.php
 * ├── views/
 * │   ├── articles/
 * │   │   ├── liste.php
 * │   │   ├── ajouter.php
 * │   │   ├── modifier.php
 * │   │   └── supprimer.php
 * │   ├── factures/
 * │   │   ├── liste.php
 * │   │   ├── creer.php
 * │   │   ├── details.php
 * │   └── fournisseurs/
 * │       ├── liste.php
 * │       ├── ajouter.php
 * │       ├── modifier.php
 * │       └── supprimer.php
 * ├── index.php             // Point d'entrée de l'application
 * └── database.sql          // Script SQL pour créer la base de données
 */

// Configuration de la base de données (config/database.php)
// Assurez-vous de remplacer ces valeurs par vos propres paramètres de base de données.
$host = 'localhost';
$dbname = 'vente_app'; // Nom de votre base de données
$username = 'root';    // Nom d'utilisateur de la base de données
$password = '';    // Mot de passe de la base de données

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Fonction pour se connecter à la base de données (utilisée dans tout l'application)
function connectDB() {
    global $pdo; // Utilisez la connexion PDO globale définie ci-dessus
    return $pdo;
}


// Modèles (dans le dossier models/)
// ---------------------------------

/**
 * Classe de modèle pour la gestion des articles.
 */
class Article
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les articles de la base de données.
     *
     * @return array Tableau associatif contenant tous les articles.
     */
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM articles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un article par son ID.
     *
     * @param int $id L'ID de l'article à récupérer.
     * @return array|null Tableau associatif contenant les données de l'article, ou null si l'article n'existe pas.
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un nouvel article à la base de données.
     *
     * @param string $nom   Le nom de l'article.
     * @param float  $prix  Le prix de l'article.
     * @param int    $stock Le stock de l'article.
     * @return bool True si l'article a été ajouté avec succès, false sinon.
     */
    public function add($nom, $prix, $stock)
    {
        $stmt = $this->pdo->prepare("INSERT INTO articles (nom, prix, stock) VALUES (?, ?, ?)");
        return $stmt->execute([$nom, $prix, $stock]);
    }

    /**
     * Met à jour un article existant dans la base de données.
     *
     * @param int    $id    L'ID de l'article à mettre à jour.
     * @param string $nom   Le nouveau nom de l'article.
     * @param float  $prix  Le nouveau prix de l'article.
     * @param int    $stock Le nouveau stock de l'article.
     * @return bool True si l'article a été mis à jour avec succès, false sinon.
     */
    public function update($id, $nom, $prix, $stock)
    {
        $stmt = $this->pdo->prepare("UPDATE articles SET nom = ?, prix = ?, stock = ? WHERE id = ?");
        return $stmt->execute([$nom, $prix, $stock, $id]);
    }

    /**
     * Supprime un article de la base de données.
     *
     * @param int $id L'ID de l'article à supprimer.
     * @return bool True si l'article a été supprimé avec succès, false sinon.
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM articles WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

/**
 * Classe de modèle pour la gestion des factures.
 */
class Facture
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère toutes les factures avec les noms des fournisseurs.
     *
     * @return array Tableau associatif contenant toutes les factures avec les noms des fournisseurs.
     */
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT factures.*, fournisseurs.nom AS fournisseur_nom 
                                FROM factures 
                                INNER JOIN fournisseurs ON factures.fournisseur_id = fournisseurs.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une facture par son ID avec le nom du fournisseur.
     *
     * @param int $id L'ID de la facture à récupérer.
     * @return array|null Tableau associatif contenant les données de la facture avec le nom du fournisseur, ou null si la facture n'existe pas.
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT factures.*, fournisseurs.nom AS fournisseur_nom
                                FROM factures
                                INNER JOIN fournisseurs ON factures.fournisseur_id = fournisseurs.id
                                WHERE factures.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute une nouvelle facture à la base de données.
     *
     * @param int    $fournisseur_id L'ID du fournisseur associé à la facture.
     * @param string $date_creation  La date de création de la facture (format YYYY-MM-DD HH:MM:SS).
     * @param float  $total          Le montant total de la facture.
     * @return bool True si la facture a été ajoutée avec succès, false sinon.
     */
    public function add($fournisseur_id, $date_creation, $total)
    {
        $stmt = $this->pdo->prepare("INSERT INTO factures (fournisseur_id, date_creation, total) VALUES (?, ?, ?)");
        return $stmt->execute([$fournisseur_id, $date_creation, $total]);
    }

     /**
     * Ajoute les détails d'une facture (articles et quantités).
     *
     * @param int $factureId L'ID de la facture à laquelle ajouter les détails.
     * @param array $details Un tableau associatif où chaque élément représente un article de la facture
     * et contient les clés 'article_id', 'quantite', 'prix_unitaire', 'sous_total'.
     * @return bool True si les détails ont été ajoutés avec succès, false sinon.
     */
    public function addDetails($factureId, $details) {
        try {
            $this->pdo->beginTransaction(); // Début de la transaction

            $stmt = $this->pdo->prepare("INSERT INTO details_facture (facture_id, article_id, quantite, prix_unitaire, sous_total) VALUES (?, ?, ?, ?, ?)");
            foreach ($details as $detail) {
                $stmt->execute([$factureId, $detail['article_id'], $detail['quantite'], $detail['prix_unitaire'], $detail['sous_total']]);
            }

            $this->pdo->commit(); // Validation de la transaction
            return true;
        } catch (PDOException $e) {
            $this->pdo->rollBack(); // Annulation de la transaction en cas d'erreur
            error_log("Erreur lors de l'ajout des détails de la facture : " . $e->getMessage()); // Log de l'erreur
            return false;
        }
    }

    // Autres méthodes pour la gestion des factures (par exemple, mise à jour, suppression) peuvent être ajoutées ici.
}

/**
 * Classe de modèle pour la gestion des fournisseurs.
 */
class Fournisseur
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Récupère tous les fournisseurs de la base de données.
     *
     * @return array Tableau associatif contenant tous les fournisseurs.
     */
    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM fournisseurs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un fournisseur par son ID.
     *
     * @param int $id L'ID du fournisseur à récupérer.
     * @return array|null Tableau associatif contenant les données du fournisseur, ou null si le fournisseur n'existe pas.
     */
    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM fournisseurs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ajoute un nouveau fournisseur à la base de données.
     *
     * @param string $nom    Le nom du fournisseur.
     * @param string $adresse L'adresse du fournisseur.
     * @return bool True si le fournisseur a été ajouté avec succès, false sinon.
     */
    public function add($nom, $adresse)
    {
        $stmt = $this->pdo->prepare("INSERT INTO fournisseurs (nom, adresse) VALUES (?, ?)");
        return $stmt->execute([$nom, $adresse]);
    }

    /**
     * Met à jour un fournisseur existant dans la base de données.
     *
     * @param int    $id      L'ID du fournisseur à mettre à jour.
     * @param string $nom    Le nouveau nom du fournisseur.
     * @param string $adresse La nouvelle adresse du fournisseur.
     * @return bool True si le fournisseur a été mis à jour avec succès, false sinon.
     */
    public function update($id, $nom, $adresse)
    {
        $stmt = $this->pdo->prepare("UPDATE fournisseurs SET nom = ?, adresse = ? WHERE id = ?");
        return $stmt->execute([$nom, $adresse, $id]);
    }

    /**
     * Supprime un fournisseur de la base de données.
     *
     * @param int $id L'ID du fournisseur à supprimer.
     * @return bool True si le fournisseur a été supprimé avec succès, false sinon.
     */
    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM fournisseurs WHERE id = ?");
        return $stmt->execute([$id]);
    }
}


// Contrôleurs (dans le dossier controllers/)
// ---------------------------------------

/**
 * Contrôleur pour la gestion des articles.
 */
class ArticleController
{
    private $articleModel;

    public function __construct($pdo)
    {
        $this->articleModel = new Article($pdo);
    }

    /**
     * Affiche la liste de tous les articles.
     *
     * @return void
     */
    public function liste()
    {
        $articles = $this->articleModel->getAll();
        include 'views/articles/liste.php';
    }

    /**
     * Affiche le formulaire d'ajout d'un nouvel article et traite l'ajout.
     *
     * @return void
     */
    public function ajouter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $prix = $_POST['prix'];
            $stock = $_POST['stock'];
             // Validation des données (à améliorer)
            if (empty($nom) || empty($prix) || empty($stock) || !is_numeric($prix) || !is_numeric($stock)) {
                $erreur = "Veuillez remplir tous les champs correctement.";
                include 'views/articles/ajouter.php';
                return; // Arrête l'exécution pour ne pas ajouter de données invalides
            }
            if ($this->articleModel->add($nom, $prix, $stock)) {
                header('Location: index.php?action=articles&page=liste');
                exit();
            } else {
                $erreur = "Erreur lors de l'ajout de l'article.";
                include 'views/articles/ajouter.php';
            }
        } else {
            include 'views/articles/ajouter.php';
        }
    }

    /**
     * Affiche le formulaire de modification d'un article et traite la modification.
     *
     * @param int $id L'ID de l'article à modifier.
     * @return void
     */
    public function modifier($id)
    {
        $article = $this->articleModel->getById($id);
        if ($article === null) {
            // Gérer le cas où l'article n'existe pas
            echo "Article non trouvé."; // À améliorer avec une vue d'erreur
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $prix = $_POST['prix'];
            $stock = $_POST['stock'];
            // Validation des données (à améliorer)
            if (empty($nom) || empty($prix) || empty($stock) || !is_numeric($prix) || !is_numeric($stock)) {
                $erreur = "Veuillez remplir tous les champs correctement.";
                include 'views/articles/modifier.php';
                return; // Arrête l'exécution
            }

            if ($this->articleModel->update($id, $nom, $prix, $stock)) {
                header('Location: index.php?action=articles&page=liste');
                exit();
            } else {
                $erreur = "Erreur lors de la modification de l'article.";
                include 'views/articles/modifier.php';
            }
        } else {
            include 'views/articles/modifier.php';
        }
    }

    /**
     * Supprime un article.
     *
     * @param int $id L'ID de l'article à supprimer.
     * @return void
     */
    public function supprimer($id)
    {
        $article = $this->articleModel->getById($id);
        if ($article === null) {
            echo "Article non trouvé."; // Améliorer avec une vue d'erreur
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->articleModel->delete($id)) {
                header('Location: index.php?action=articles&page=liste');
                exit();
            } else {
                $erreur = "Erreur lors de la suppression de l'article.";
                include 'views/articles/supprimer.php';
            }
        } else {
             include 'views/articles/supprimer.php';
        }
    }
}

/**
 * Contrôleur pour la gestion des factures.
 */
class FactureController
{
    private $factureModel;
    private $fournisseurModel;
    private $articleModel; // Ajoutez ceci pour accéder au modèle Article

    public function __construct($pdo)
    {
        $this->factureModel = new Facture($pdo);
        $this->fournisseurModel = new Fournisseur($pdo);
        $this->articleModel = new Article($pdo); // Instanciez le modèle Article
    }

    /**
     * Affiche la liste de toutes les factures.
     *
     * @return void
     */
    public function liste()
    {
        $factures = $this->factureModel->getAll();
        include 'views/factures/liste.php';
    }

    /**
     * Affiche le formulaire de création d'une nouvelle facture et traite la création.
     *
     * @return void
     */
    public function creer()
    {
        $fournisseurs = $this->fournisseurModel->getAll();
        $articles = $this->articleModel->getAll(); // Récupérez tous les articles pour le formulaire

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fournisseur_id = $_POST['fournisseur_id'];
            $date_creation = date('Y-m-d H:i:s');
            $total = 0; // Initialisez le total

            // Validation des données (à améliorer)
             if (empty($fournisseur_id)) {
                $erreur = "Veuillez sélectionner un fournisseur.";
                include 'views/factures/creer.php';
                return;
            }
            if (!isset($_POST['articles']) || !is_array($_POST['articles']) || count($_POST['articles']) == 0) {
                $erreur = "Veuillez ajouter au moins un article à la facture.";
                include 'views/factures/creer.php';
                return;
            }

            // Début de la transaction pour la facture et ses détails
            try {
                $this->factureModel->pdo->beginTransaction();

                // Ajouter la facture
                $factureId = $this->factureModel->add($fournisseur_id, $date_creation, $total);
                 if (!$factureId) {
                    throw new Exception("Erreur lors de l'ajout de la facture.");
                }

                $detailsFacture = [];
                // Parcourir les articles sélectionnés
                foreach ($_POST['articles'] as $articleId => $quantite) {
                    // Validation de la quantité
                    if ($quantite <= 0 || !is_numeric($quantite) || floor($quantite) != $quantite) {
                         throw new Exception("La quantité de l'article doit être un nombre entier positif.");
                    }
                    $article = $this->articleModel->getById($articleId);
                    if (!$article) {
                         throw new Exception("Article non trouvé : " . $articleId);
                    }
                    $prixUnitaire = $article['prix'];
                    $sousTotal = $quantite * $prixUnitaire;
                    $total += $sousTotal; // Accumuler le sous-total au total de la facture

                    $detailsFacture[] = [
                        'article_id' => $articleId,
                        'quantite' => $quantite,
                        'prix_unitaire' => $prixUnitaire,
                        'sous_total' => $sousTotal,
                    ];
                }
                // Mettre à jour le total de la facture
                $this->factureModel->pdo->prepare("UPDATE factures SET total = ? WHERE id = ?")->execute([$total, $factureId]);
                // Ajouter les détails de la facture
                $this->factureModel->addDetails($factureId, $detailsFacture);

                $this->factureModel->pdo->commit(); // Valider la transaction

                header('Location: index.php?action=factures&page=liste');
                exit();
            } catch (Exception $e) {
                $this->factureModel->pdo->rollBack(); // Annuler la transaction en cas d'erreur
                $erreur = "Erreur lors de la création de la facture : " . $e->getMessage();
                include 'views/factures/creer.php';
                return;
            }
        } else {
            include 'views/factures/creer.php';
        }
    }

    /**
     * Affiche les détails d'une facture spécifique.
     *
     * @param int $id L'ID de la facture à afficher.
     * @return void
     */
    public function details($id)
    {
        $facture = $this->factureModel->getById($id);
        if ($facture === null) {
            echo "Facture non trouvée."; // Améliorer avec une vue d'erreur
            return;
        }
        // Récupérer les détails des articles de cette facture (nécessite une table 'details_facture')
        $stmt = $this->factureModel->pdo->prepare("SELECT details_facture.*, articles.nom AS article_nom
                                                FROM details_facture
                                                INNER JOIN articles ON details_facture.article_id = articles.id
                                                WHERE details_facture.facture_id = ?");
        $stmt->execute([$id]);
        $details = $stmt->fetchAll(PDO::FETCH_ASSOC);

        include 'views/factures/details.php';
    }
}

/**
 * Contrôleur pour la gestion des fournisseurs.
 */
class FournisseurController
{
    private $fournisseurModel;

    public function __construct($pdo)
    {
        $this->fournisseurModel = new Fournisseur($pdo);
    }

    /**
     * Affiche la liste de tous les fournisseurs.
     *
     * @return void
     */
    public function liste()
    {
        $fournisseurs = $this->fournisseurModel->getAll();
        include 'views/fournisseurs/liste.php';
    }

    /**
     * Affiche le formulaire d'ajout d'un nouveau fournisseur et traite l'ajout.
     *
     * @return void
     */
    public function ajouter()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $adresse = $_POST['adresse'];
             // Validation des données (à améliorer)
            if (empty($nom) || empty($adresse)) {
                $erreur = "Veuillez remplir tous les champs.";
                include 'views/fournisseurs/ajouter.php';
                return;
            }
            if ($this->fournisseurModel->add($nom, $adresse)) {
                header('Location: index.php?action=fournisseurs&page=liste');
                exit();
            } else {
                $erreur = "Erreur lors de l'ajout du fournisseur.";
                include 'views/fournisseurs/ajouter.php';
            }
        } else {
            include 'views/fournisseurs/ajouter.php';
        }
    }

     /**
     * Affiche le formulaire de modification d'un fournisseur et traite la modification.
     *
     * @param int $id L'ID du fournisseur à modifier.
     * @return void
     */
    public function modifier($id)
    {
        $fournisseur = $this->fournisseurModel->getById($id);
        if ($fournisseur === null) {
            echo "Fournisseur non trouvé."; // Améliorer avec une vue d'erreur
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $adresse = $_POST['adresse'];
             // Validation des données (à améliorer)
            if (empty($nom) || empty($adresse)) {
                $erreur = "Veuillez remplir tous les champs.";
                include 'views/fournisseurs/modifier.php';
                return;
            }
            if ($this->fournisseurModel->update($id, $nom, $adresse)) {
                header('Location: index.php?action=fournisseurs&page=liste');
                exit();
            } else {
                $erreur = "Erreur lors de la modification du fournisseur.";
                include 'views/fournisseurs/modifier.php';
            }
        } else {
            include 'views/fournisseurs/modifier.php';
        }
    }

    /**
     * Supprime un fournisseur.
     *
     * @param int $id L'ID du fournisseur à supprimer.
     * @return void
     */
    public function supprimer($id)
    {
         $fournisseur = $this->fournisseurModel->getById($id);
        if ($fournisseur === null) {
            echo "Fournisseur non trouvé."; // Améliorer avec une vue d'erreur
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->fournisseurModel->delete($id)) {
                header('Location: index.php?action=fournisseurs&page=liste');
                exit();
            } else {
                $erreur = "Erreur lors de la suppression du fournisseur.";
                include 'views/fournisseurs/supprimer.php';
            }
        } else {
             include 'views/fournisseurs/supprimer.php';
        }
    }
}

// Point d'entrée de l'application (index.php)
// ------------------------------------------

// Inclut le fichier de configuration de la base de données// require_once 'config/database.php'; // La connexion est maintenant globale

// Pas besoin d'appeler connectDB() ici, la connexion est déjà établie dans database.php

$action = $_GET['action'] ?? 'articles'; // Action par défaut
$page = $_GET['page'] ?? 'liste';     // Page par défaut

// Router
switch ($action) {
    case 'articles':
        require_once 'controllers/ArticleController.php';
        $controller = new ArticleController($pdo);
        switch ($page) {
            case 'liste':
                $controller->liste();
                break;
            case 'ajouter':
                $controller->ajouter();
                break;
            case 'modifier':
                $id = $_GET['id'] ?? null;
                if ($id === null) {
                    echo "ID d'article manquant."; // Améliorer
                    break;
                }
                $controller->modifier($id);
                break;
            case 'supprimer':
                 $id = $_GET['id'] ?? null;
                if ($id === null) {
                    echo "ID d'article manquant."; // Améliorer
                    break;
                }
                $controller->supprimer($id);
                break;
            default:
                $controller->liste();
                break;
        }
        break;
    case 'factures':
        require_once 'controllers/FactureController.php';
        $controller = new FactureController($pdo);
        switch ($page) {
            case 'liste':
                $controller->liste();
                break;
            case 'creer':
                $controller->creer();
                break;
            case 'details':
                $id = $_GET['id'] ?? null;
                if ($id === null) {
                    echo "ID de facture manquant."; // Améliorer
                    break;
                }
                $controller->details($id);
                break;
            default:
                $controller->liste();
                break;
        }
        break;
    case 'fournisseurs':
        require_once 'controllers/FournisseurController.php';
        $controller = new FournisseurController($pdo);
        switch ($page) {
            case 'liste':
                $controller->liste();
                break;
            case 'ajouter':
                $controller->ajouter();
                break;
             case 'modifier':
                $id = $_GET['id'] ?? null;
                if ($id === null) {
                    echo "ID de fournisseur manquant."; // Améliorer
                    break;
                }
                $controller->modifier($id);
                break;
            case 'supprimer':
                $id = $_GET['id'] ?? null;
                if ($id === null) {
                    echo "ID de fournisseur manquant."; // Améliorer
                    break;
                }
                $controller->supprimer($id);
                break;
            default:
                $controller->liste();
                break;
        }
        break;
    default:
        require_once 'controllers/ArticleController.php';
        $controller = new ArticleController($pdo);
        $controller->liste();
        break;
}
?>
