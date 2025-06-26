# Projet Pratique GIT : Gestion de Version d'un Site Web Statique

## Introduction

Ce projet a pour objectif de démontrer la maîtrise des concepts fondamentaux de Git pour la gestion de version d'un mini-site web statique. Le site a été développé individuellement, en suivant une stratégie de branchement structurée et un flux de travail précis.

### Informations sur le Projet
* **Nom de l'étudiant(e) :** NGALA STELLA
* **Classe :** B1
* **Matière :** GIT
* **Enseignant :** BOGNI-DANCHII
* **Nom de l'entreprise fictive :** Tech Solutions Innovantes
* **Objectif du site :** Présenter l'entreprise, ses services et ses coordonnées.

## Stratégie de Branchement Adoptée

Pour organiser le développement et garantir la stabilité du code, la stratégie de branchement suivante a été appliquée, inspirée par le modèle Git Flow simplifié :

* `main` : Branche principale et stable. Elle représente la version du code prête à être déployée en production. Seules les versions finales et validées y sont fusionnées.
* `dev` : Branche de développement et d'intégration. C'est ici que le travail provenant des branches `test/*` est consolidé avant d'être potentiellement fusionné dans `main`.
* `feature/*` : Branches de fonctionnalités. Chaque nouvelle fonctionnalité ou groupe de travail est développé isolément dans une branche préfixée par `feature/` (ex: `feature/html-structure`, `feature/css-theme`).
* `test/*` : Branches de test et d'intégration intermédiaire. Ces branches sont utilisées pour regrouper et tester les fonctionnalités développées avant de les pousser vers la branche `dev`. (ex: `test/frontend`).

### Flux de Travail Recommandé et Suivi

Le développement a suivi rigoureusement le flux de travail recommandé :

`feature/*` $\rightarrow$ `test/*` $\rightarrow$ `dev` $\rightarrow$ `main`

Chaque fonctionnalité a été développée sur sa propre branche `feature`, puis fusionnée dans une branche `test` pour une première intégration et validation. Une fois stable, le contenu des branches `test` a été intégré dans `dev`, et finalement, la branche `dev` a été fusionnée dans `main` pour la version finale.

## Étapes Suivies et Commandes Git Utilisées

Voici un résumé chronologique des étapes clés du projet, avec les commandes Git associées :

1.  **Initialisation du Dépôt Git et Préparation**
    * Création du dossier du projet `mon-mini-site-entreprise`.
    * Initialisation du dépôt Git.
    * Renommage de la branche principale en `main`.
    * **Commandes utilisées :**
        ```bash
        cd chemin/vers/mon-mini-site-entreprise
        git init
        git branch -M main
        ```

2.  **Développement de la Structure HTML de Base (`feature/html-structure`)**
    * Création et basculement sur la branche `feature/html-structure`.
    * Création des fichiers `index.html`, `services.html`, `contact.html`.
    * Ajout de la structure HTML minimale (doctype, head, body, header, nav, main, footer) et du contenu de base pour chaque page.
    * **Commandes utilisées :**
        ```bash
        git branch feature/html-structure
        git checkout feature/html-structure
        touch index.html services.html contact.html # ou création manuelle
        # (édition des fichiers HTML)
        git add .
        git commit -m "Initialisation de la structure HTML de base (index, services, contact)"
        git commit -m "Ajout de la structure HTML de base (doctype, head, body, header, nav, main, footer)"
        ```

3.  **Développement du Thème CSS (`feature/css-theme`)**
    * Retour sur `main` puis création et basculement sur la branche `feature/css-theme`.
    * Fusion du travail de `feature/html-structure` dans `feature/css-theme` pour pouvoir styliser le HTML existant.
    * Création du dossier `css` et du fichier `css/style.css`.
    * Liaison du fichier `style.css` dans toutes les pages HTML.
    * Ajout de styles CSS de base (polices, palette de couleurs, mise en page simple, réactivité de base).
    * **Commandes utilisées :**
        ```bash
        git checkout main
        git branch feature/css-theme
        git checkout feature/css-theme
        git merge feature/html-structure
        mkdir css
        touch css/style.css # ou création manuelle
        # (édition des fichiers HTML pour la liaison CSS)
        # (édition de css/style.css)
        git add .
        git commit -m "Création de la branche feature/css-theme et liaison du fichier style.css"
        git commit -m "Ajout des styles CSS de base (polices, couleurs, mise en page)"
        ```

4.  **Phase de Test et d'Intégration Frontend (`test/frontend`)**
    * Retour sur `main` puis création et basculement sur la branche `test/frontend`.
    * Fusion des branches `feature/html-structure` et `feature/css-theme` dans `test/frontend`.
    * Vérification visuelle de la cohérence et des fonctionnalités dans le navigateur.
    * **Commandes utilisées :**
        ```bash
        git checkout main
        git branch test/frontend
        git checkout test/frontend
        git merge feature/html-structure
        git merge feature/css-theme
        # (Vérification dans le navigateur)
        ```

5.  **Intégration Globale (`dev`)**
    * Retour sur `main` puis création (si nécessaire) et basculement sur la branche `dev`.
    * Fusion de la branche `test/frontend` dans `dev`.
    * Vérification de l'intégration globale.
    * **Commandes utilisées :**
        ```bash
        git checkout main
        git branch dev # si non existante
        git checkout dev
        git merge test/frontend
        # (Vérification dans le navigateur)
        ```

6.  **Validation Finale (`main`)**
    * Retour sur la branche `main`.
    * Fusion de la branche `dev` dans `main`, marquant la version finale et stable du projet.
    * Vérification finale du site.
    * **Commandes utilisées :**
        ```bash
        git checkout main
        git merge dev
        # (Vérification finale dans le navigateur)
        ```

## Conclusion

Ce projet a permis de mettre en pratique les concepts fondamentaux de Git, notamment la gestion des branches, les fusions et le suivi de l'historique des modifications, essentiels pour le développement collaboratif et la maintenance de projets logiciels.