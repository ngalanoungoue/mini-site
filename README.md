# Mini-site d'entreprise fictive

Projet réalisé dans le cadre du cours de Développement Web (HTML/CSS) et d'une évaluation de gestion de version avec Git & GitHub.

---

##  Objectif

- Créer un site web statique représentant une entreprise fictive
- Utiliser un workflow Git professionnel avec branches : `feature/*`, `test`, `dev`, `main`
- Gérer l’ensemble du projet en ligne avec GitHub

---

## Organisation Git

```mermaid
graph TD
    A[feature/accueil] --> B[test]
    B --> C[dev]
    C --> D[main]
