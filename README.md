# 🚀 Projet DevOps — Gestion des Besoins

Application PHP déployée avec un pipeline CI/CD complet : GitHub → Jenkins → Docker → Kubernetes (k3s) → AWS EC2 + Monitoring Grafana/Prometheus.

---

## 🏗️ Architecture

```
Developer
    │
    ▼
Git Push ──► GitHub
                │
                ▼ (Webhook)
            Jenkins CI/CD
                │
                ▼
        Docker Build & Push
                │
                ▼
          Docker Hub
                │
                ▼ (SSH)
           AWS EC2
                │
        ┌───────┴────────┐
        ▼                ▼
   k3s (Kubernetes)   Prometheus
        │                │
   ┌────┴────┐           ▼
   ▼         ▼      Grafana Cloud
 PHP App   MySQL       📊
(2 pods)  (1 pod)
```

---

## 🛠️ Stack Technologique

| Outil | Rôle |
|---|---|
| **PHP 8.2 + Apache** | Application web |
| **MySQL 8.0** | Base de données |
| **Docker** | Containerisation |
| **Docker Hub** | Registry d'images |
| **Jenkins** | CI/CD Pipeline |
| **GitHub** | Versioning + Webhook |
| **k3s** | Kubernetes léger |
| **AWS EC2 (t3.micro)** | Serveur cloud |
| **Prometheus** | Collecte des métriques |
| **Grafana Cloud** | Dashboard monitoring |
| **ngrok** | Exposition Jenkins |
| **Helm** | Gestionnaire packages K8s |

---

## 📋 Prérequis

- Git installé sur le PC
- Docker Desktop installé
- Compte GitHub
- Compte Docker Hub
- Compte AWS
- Compte Grafana Cloud

---

## 🚀 Étapes d'installation

### ÉTAPE 1 — Préparer et pusher sur GitHub

```bash
# Initialiser git
git init

# Ajouter tous les fichiers
git add .

# Premier commit
git commit -m "Initial commit - projet-devops PHP"

# Connecter au repo GitHub
git remote add origin https://github.com/aminebough04-creator/projet-devops.git

# Pousser le code
git branch -M main
git push -u origin main
```

---

### ÉTAPE 2 — Lancer Jenkins avec Docker

```bash
# Lancer Jenkins
docker run -d \
  --name jenkins-dev \
  -p 8080:8080 \
  -p 50000:50000 \
  -v jenkins_home:/var/jenkins_home \
  -v /var/run/docker.sock:/var/run/docker.sock \
  jenkins/jenkins:lts

# Récupérer le mot de passe initial
docker exec jenkins-dev cat /var/jenkins_home/secrets/initialAdminPassword
```

Ouvrir Jenkins : **http://localhost:8080**

#### Plugins à installer :
- Docker Pipeline
- Docker Commons
- GitHub Integration
- SSH Agent

#### Installer Docker dans Jenkins :
```bash
docker exec -it --user root jenkins-dev bash
apt-get update && apt-get install -y docker.io
chmod 666 /var/run/docker.sock
docker --version
exit
```

---

### ÉTAPE 3 — Configurer les Credentials Jenkins

#### Docker Hub credentials :
- **Kind** : Username with password
- **ID** : `dockerhub-credentials`
- **Username** : `aminebg10`
- **Password** : ton mot de passe Docker Hub

#### SSH EC2 credentials :
- **Kind** : SSH Username with private key
- **ID** : `ec2-ssh-key`
- **Username** : `ubuntu`
- **Private Key** : contenu du fichier `.pem`

---

### ÉTAPE 4 — Créer le Pipeline Jenkins

- **Type** : Pipeline
- **Definition** : Pipeline script from SCM
- **SCM** : Git
- **Repository URL** : `https://github.com/aminebough04-creator/projet-devops.git`
- **Branch** : `*/main`
- **Script Path** : `Jenkinsfile`

---

### ÉTAPE 5 — Créer l'instance AWS EC2

#### Configuration :
| Paramètre | Valeur |
|---|---|
| **AMI** | Ubuntu Server 22.04 LTS |
| **Type** | t3.micro (Free Tier) |
| **Key pair** | devops-key.pem |

#### Security Group — Ports à ouvrir :
| Port | Usage |
|---|---|
| 22 | SSH |
| 80 | HTTP |
| 30080 | App Kubernetes |
| 31001 | Prometheus |

---

### ÉTAPE 6 — Installer Docker + k3s sur EC2

```bash
# Connexion SSH
ssh -i devops-key.pem ubuntu@<IP_EC2>

# Installer Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker ubuntu
newgrp docker

# Installer k3s
curl -sfL https://get.k3s.io | INSTALL_K3S_EXEC="server --tls-san <IP_EC2> --disable traefik" sh -

# Configurer kubectl
mkdir -p ~/.kube
sudo cp /etc/rancher/k3s/k3s.yaml ~/.kube/config
sudo chown $(whoami):$(whoami) ~/.kube/config
chmod 600 ~/.kube/config
echo 'export KUBECONFIG=~/.kube/config' >> ~/.bashrc
source ~/.bashrc

# Vérifier
kubectl get nodes
```

---

### ÉTAPE 7 — Déployer MySQL sur Kubernetes

```bash
# Appliquer le déploiement MySQL
kubectl apply -f mysql-deployment.yaml --validate=false

# Vérifier
kubectl get pods
kubectl get services
```

#### Importer la base de données :
```bash
# Copier le SQL dans le pod MySQL
kubectl cp ~/gestion_besoins.sql <mysql-pod-name>:/tmp/gestion_besoins.sql

# Importer
kubectl exec -it <mysql-pod-name> -- bash
mysql -u root -proot123 gestion_besoins < /tmp/gestion_besoins.sql

# Vérifier les tables
mysql -u root -proot123 -e "USE gestion_besoins; SHOW TABLES;"
```

---

### ÉTAPE 8 — Configurer la connexion PHP → MySQL

Modifier `config/db.php` :

```php
<?php
$host = 'mysql-service';  // Nom du service Kubernetes
$dbname = 'gestion_besoins';
$user = 'phpuser';
$pass = 'phppass123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion BD : " . $e->getMessage());
}
```

```bash
git add -f config/db.php
git commit -m "Connect PHP to MySQL Kubernetes service"
git push origin main
```

---

### ÉTAPE 9 — Webhook GitHub (Auto-deploy)

#### 1. Exposer Jenkins avec ngrok :
```bash
ngrok http 8080
# Copier l'URL : https://xxx.ngrok-free.dev
```

#### 2. Jenkins URL :
- Gérer Jenkins → Configurer → Jenkins URL → `https://xxx.ngrok-free.dev/`

#### 3. Webhook GitHub :
- GitHub → Settings → Webhooks → Add webhook
- **Payload URL** : `https://xxx.ngrok-free.dev/github-webhook/`
- **Content type** : `application/json`
- **Events** : Push event

#### 4. Jenkins job :
- Build Triggers → ✅ GitHub hook trigger for GITScm polling

---

### ÉTAPE 10 — Monitoring Prometheus + Grafana Cloud

#### Installer Prometheus sur k3s :
```bash
helm repo add prometheus-community https://prometheus-community.github.io/helm-charts
helm repo update

helm install prometheus prometheus-community/prometheus \
  --namespace monitoring \
  --create-namespace \
  --set server.service.type=NodePort \
  --set server.service.nodePort=31001 \
  --set alertmanager.enabled=false \
  --set pushgateway.enabled=false \
  --set nodeExporter.enabled=false \
  --set kubeStateMetrics.enabled=false \
  --set server.resources.requests.memory=100Mi \
  --set server.resources.limits.memory=200Mi
```

#### Connecter Grafana Cloud :
1. Créer un compte sur **https://grafana.com**
2. Connections → Data sources → Add → Prometheus
3. URL : `http://<IP_EC2>:31001`
4. Save & Test ✅
5. Dashboards → Import → ID `3119`

---

## 📁 Structure du projet

```
projet-devops/
├── assets/              # CSS, JS, images
├── classes/             # Classes PHP
├── config/
│   ├── db.php           # Connexion base de données
│   └── email.php        # Configuration email
├── includes/            # Fichiers inclus
├── sql/
│   └── gestion_besoins.sql  # Base de données
├── uploads/             # Fichiers uploadés
├── Dockerfile           # Image Docker PHP
├── Jenkinsfile          # Pipeline CI/CD
├── deployment.yaml      # Déploiement K8s PHP
├── service.yaml         # Service K8s PHP
├── mysql-deployment.yaml # Déploiement K8s MySQL
├── .dockerignore
├── .gitignore
└── README.md
```

---

## 🌐 URLs d'accès

| Service | URL |
|---|---|
| **Application** | http://54.236.21.213:30080 |
| **Jenkins** | http://localhost:8080 |
| **Prometheus** | http://54.236.21.213:31001 |
| **Grafana Cloud** | https://amineboughrioul04.grafana.net |

---

## 👤 Compte admin par défaut

| Champ | Valeur |
|---|---|
| **Email** | admin@example.com |
| **Mot de passe** | admin123 |

---

## 🔄 Jenkinsfile

```groovy
pipeline {
    agent any
    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-credentials')
        EC2_IP = '54.236.21.213'
    }
    stages {
        stage('Checkout') {
            steps { checkout scm }
        }
        stage('Build Image') {
            steps {
                sh 'docker build -t aminebg10/projet-devops-php:latest .'
            }
        }
        stage('Push to Hub') {
            steps {
                sh 'echo $DOCKERHUB_CREDENTIALS_PSW | docker login -u $DOCKERHUB_CREDENTIALS_USR --password-stdin'
                sh 'docker push aminebg10/projet-devops-php:latest'
            }
        }
        stage('Deploy to EC2') {
            steps {
                sshagent(['ec2-ssh-key']) {
                    sh '''
                        ssh -o StrictHostKeyChecking=no ubuntu@54.236.21.213 "
                            kubectl apply -f ~/deployment.yaml
                            kubectl apply -f ~/service.yaml
                            kubectl rollout restart deployment/php-app-deployment
                        "
                    '''
                }
            }
        }
    }
}
```

---

## ✅ Checklist finale

- [x] Code PHP sur GitHub
- [x] Jenkins CI/CD configuré
- [x] Docker image buildée et pushée
- [x] AWS EC2 t3.micro créé
- [x] k3s Kubernetes installé
- [x] MySQL déployé sur Kubernetes
- [x] Base de données importée
- [x] App PHP connectée à MySQL
- [x] Webhook GitHub auto-deploy
- [x] Prometheus installé
- [x] Grafana Cloud connecté

---

## 👨‍💻 Auteur

**Amine Boughrioul**  
GitHub : [@aminebough04-creator](https://github.com/aminebough04-creator)  
Docker Hub : [aminebg10](https://hub.docker.com/u/aminebg10)