# 🤖 Chatbot DevOps & Cloud (PFE)

Projet de Fin d’Études (PFE) réalisé chez **Braincode** : conception, développement et déploiement d’un **chatbot intelligent** dans une plateforme **cloud-native sur AWS**, en appliquant les pratiques **DevOps / DevSecOps** et **Infrastructure as Code (Terraform)**.

---

## ✨ Fonctionnalités clés
- Déploiement cloud sécurisé sur **AWS** (VPC public/privé, EC2, RDS MySQL, S3, Route53)
- Application conteneurisée avec **Docker / Docker Compose**
- **Reverse proxy Nginx** pour exposer l’application et router vers les services
- **Backend IA** (FastAPI) connecté à **Ollama** pour l’inférence (LLM)
- Pipeline **CI/CD GitLab** : build, tests, qualité, scan sécurité, docker build/push, déploiement EC2
- Supervision **Prometheus + Grafana** (+ Alerting via Alertmanager selon config)

---

## 🧱 Architecture (résumé)
**Chemin d’une requête :** DNS (Route53) → EC2 (Nginx) → (Laravel / FastAPI) → RDS (privé) & S3.  
Les services applicatifs (Nginx, Laravel/PHP-FPM, FastAPI, Ollama) sont orchestrés via **Docker Compose**.

---

## 📁 Structure du repository
- `app/` : application web (Laravel + front)
- `backend-llm/` : backend IA (FastAPI)
- `nginx/` : configuration reverse proxy
- `infra/` : infrastructure & provisioning (Terraform / scripts)
- `aws/` : ressources / scripts AWS (selon contenu du projet)
- `.gitlab-ci.yml` + `.gitlab/` : pipeline GitLab CI/CD
- `docker-compose.yml` : orchestration des services
- `prometheus.yml` : configuration Prometheus
- `tests/` : tests unitaires (PHPUnit, Vitest, Pytest)

---

## ✅ Prérequis
- Docker & Docker Compose
- Node.js + npm (si exécution hors Docker du front)
- PHP + Composer (si exécution hors Docker du backend Laravel)
- Python 3.x (si exécution hors Docker du backend IA)

---

## 🚀 Lancement rapide (Docker)
> Le projet est conçu pour tourner en multi-conteneurs avec `docker-compose.yml`.

```bash
docker compose up -d --build
