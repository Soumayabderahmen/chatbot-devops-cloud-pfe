# 🤖 Chatbot DevOps & Cloud (PFE)

Projet de Fin d’Études (PFE) réalisé chez **Braincode**, visant la **conception, le développement et le déploiement d’un chatbot intelligent** au sein d’une **plateforme cloud-native sur AWS**, en appliquant les bonnes pratiques **DevOps, DevSecOps** et **Infrastructure as Code (Terraform)**.

---

## ✨ Fonctionnalités clés
- Déploiement cloud sécurisé sur **AWS** (VPC public/privé, EC2, RDS MySQL, S3, Route53)
- Backend applicatif développé avec **Laravel (PHP)**
- Frontend interactif développé avec **Vue.js**
- **Backend IA** dédié au chatbot développé avec **FastAPI (Python)** et connecté à **Ollama (LLM)**
- Application entièrement conteneurisée avec **Docker & Docker Compose**
- **Reverse proxy Nginx** pour exposer l’application et router les requêtes
- Pipeline **CI/CD GitLab** : build, tests, qualité, scan sécurité, docker build/push, déploiement automatique sur EC2
- Supervision et observabilité avec **Prometheus & Grafana**

---

## 🧱 Architecture (résumé)
**Chemin d’une requête utilisateur :**  
DNS (**Route53**) → **EC2 (Nginx)** → (**Laravel / FastAPI**) → **RDS MySQL (subnet privé)** & **S3**.

Les services applicatifs (**Nginx, Laravel/PHP-FPM, Vue.js, FastAPI, Ollama**) sont orchestrés via **Docker Compose**.

---

## 🛠️ Stack technique

### Développement applicatif
- **Laravel (PHP)** : backend principal, logique métier, API, gestion des utilisateurs et accès base de données
- **Vue.js** : frontend dynamique, interface utilisateur et intégration du chatbot
- **FastAPI (Python)** : backend IA pour le traitement des requêtes du chatbot et l’inférence LLM

### DevOps & Cloud
- Docker & Docker Compose  
- GitLab CI/CD  
- Terraform (Infrastructure as Code)  
- AWS : EC2, RDS (MySQL), S3, Route53, VPC  
- Nginx (reverse proxy)

### Qualité, sécurité & monitoring
- SonarQube (analyse qualité du code)
- Trivy (scan de vulnérabilités Docker)
- Prometheus (collecte de métriques)
- Grafana (dashboards et visualisation)

---

## 📁 Structure du repository
- `app/` : backend **Laravel (PHP)**
- `resources/` / `public/` : frontend **Vue.js**
- `backend-llm/` : backend IA **FastAPI (Python)**
- `nginx/` : configuration **Nginx**
- `infra/` : infrastructure & provisioning (**Terraform**)
- `aws/` : scripts et ressources AWS
- `.gitlab-ci.yml` & `.gitlab/` : pipeline **GitLab CI/CD**
- `docker-compose.yml` : orchestration des services
- `prometheus.yml` : configuration Prometheus
- `tests/` : tests unitaires (**PHPUnit, Vitest, Pytest**)

---

## ✅ Prérequis
- Docker & Docker Compose
- Node.js & npm (si exécution hors Docker du frontend)
- PHP & Composer (si exécution hors Docker du backend Laravel)
- Python 3.x (si exécution hors Docker du backend IA)

---

## 🚀 Lancement rapide (Docker)
> Le projet est conçu pour fonctionner en environnement multi-conteneurs.

```bash
docker compose up -d --build
