\# Gestion des Besoins — DevOps CI/CD



Application web PHP/MySQL pour la gestion des demandes internes, avec trois espaces : Demandeur, Validateur et Administrateur. Déployée via un pipeline CI/CD complet sur AWS EC2 avec Kubernetes.



\---



\## Stack



\- PHP 8.2 + Apache

\- MySQL 8.0

\- Docker + Docker Hub

\- Jenkins (CI/CD)

\- Kubernetes k3s

\- Prometheus + Grafana Cloud



\---



\## Pipeline CI/CD



```

git push → GitHub → Jenkins → Docker Build → Push Hub → k3s (AWS EC2)

```



Chaque `git push` sur `main` déclenche automatiquement le pipeline via webhook GitHub.



\---



\## Démarrage local



```bash

\# Cloner le repo

git clone https://github.com/aminebough04-creator/projet-devops.git

cd projet-devops

```



Configurer la base de données dans `config/db.php` :



```php

$host   = 'localhost';

$dbname = 'gestion\_besoins';

$user   = 'root';

$pass   = '';

```



Importer la base :



```bash

mysql -u root -p gestion\_besoins < sql/gestion\_besoins.sql

```



Accès local (via XAMPP) :



\- http://localhost/gestion\_besoins/



\---



\## Déploiement Docker



```bash

\# Build de l'image

docker build -t aminebg10/projet-devops-php:latest .



\# Lancer le conteneur

docker run -d -p 8080:80 aminebg10/projet-devops-php:latest

```



Accès :



\- http://localhost:8080/



\---



\## Déploiement Kubernetes (k3s)



```bash

\# Déployer MySQL

kubectl apply -f mysql-deployment.yaml --validate=false



\# Déployer l'application PHP

kubectl apply -f deployment.yaml --validate=false



\# Vérifier les pods

kubectl get pods

kubectl get services

```



Accès production :



\- http://54.236.21.213:30080/



\---



\## Jenkins



Lancer Jenkins via Docker :



```bash

docker run -d --name jenkins-dev \\

&#x20; -p 8080:8080 \\

&#x20; -v jenkins\_home:/var/jenkins\_home \\

&#x20; -v /var/run/docker.sock:/var/run/docker.sock \\

&#x20; jenkins/jenkins:lts

```



Accès :



\- http://localhost:8080/



Credentials à configurer :



| ID | Type | Usage |

|---|---|---|

| `dockerhub-credentials` | Username/Password | Docker Hub |

| `ec2-ssh-key` | SSH Private Key | AWS EC2 |



\---



\## Monitoring Grafana



Prometheus est déployé sur le cluster k3s :



```bash

helm repo add prometheus-community https://prometheus-community.github.io/helm-charts

helm repo update



helm install prometheus prometheus-community/prometheus \\

&#x20; --namespace monitoring --create-namespace \\

&#x20; --set server.service.type=NodePort \\

&#x20; --set server.service.nodePort=31001 \\

&#x20; --set alertmanager.enabled=false \\

&#x20; --set server.resources.limits.memory=200Mi

```



Accès Prometheus :



\- http://54.236.21.213:31001/



Dashboard Grafana Cloud :



\- https://amineboughrioul04.grafana.net/



\---



\## Structure



```

projet-devops/

├── assets/               # CSS, JS, images

├── classes/              # Classes PHP

├── config/

│   └── db.php            # Connexion base de données

├── includes/             # Fichiers partagés

├── sql/

│   └── gestion\_besoins.sql

├── uploads/              # Pièces jointes

├── Dockerfile

├── Jenkinsfile

├── deployment.yaml       # K8s PHP App

├── service.yaml          # K8s NodePort 30080

└── mysql-deployment.yaml # K8s MySQL

```



\---



\## Comptes par défaut



| Rôle | Email | Mot de passe |

|---|---|---|

| Admin | admin@example.com | admin123 |



\---



\## Liens



| Service | URL |

|---|---|

| Application | http://54.236.21.213:30080 |

| Prometheus | http://54.236.21.213:31001 |

| Grafana | https://amineboughrioul04.grafana.net |

| Docker Hub | https://hub.docker.com/r/aminebg10/projet-devops-php |



\---



\*\*Auteur\*\* — Amine Boughrioul · \[@aminebough04-creator](https://github.com/aminebough04-creator)

