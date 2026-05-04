pipeline {
    agent any // <--- C'est ce qui manquait ! Dit à Jenkins d'utiliser un exécuteur.

    tools {
        // Utilise l'installateur Docker configuré dans Jenkins sous le nom 'dockerdev'
        dockerTool 'dockerdev' 
    }

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
    }

    stages {
        stage('Build & Push to Docker Hub') {
            steps {
                script {
                    // On récupère le chemin de l'outil Docker pour l'ajouter au PATH
                    def dockerToolPath = tool name: 'dockerdev', type: 'dockerTool'
                    
                    withEnv(["PATH+DOCKER=${dockerToolPath}/bin"]) {
                        // Connexion et envoi vers Docker Hub
                        docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
                            def customImage = docker.build("${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}")
                            customImage.push()
                            customImage.push("latest")
                        }
                    }
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                // Déploiement automatique sur votre cluster local
                sh "kubectl apply -f deployment.yaml"
                sh "kubectl apply -f service.yaml"
            }
        }
    }
}
