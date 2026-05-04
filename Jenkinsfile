pipeline {
    agent any
    
    tools {
        dockerTool 'dockerdev' // Utilise l'outil configuré en build #7
    }

    environment {
        // Remplacez par votre pseudo Docker Hub
        DOCKER_HUB_USER = 'aminebough04' 
        APP_NAME = 'amine-devops-app'
    }

    stages {
        stage('Build & Push to Docker Hub') {
            steps {
                script {
                    // Connexion sécurisée à Docker Hub avec vos identifiants
                    docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
                        
                        // Construction de l'image
                        def customImage = docker.build("${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}")
                        
                        // Envoi vers le registre cloud
                        customImage.push()
                        customImage.push("latest")
                    }
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                // Cette étape applique vos fichiers YAML poussés sur GitHub
                sh "kubectl apply -f deployment.yaml"
                sh "kubectl apply -f service.yaml"
            }
        }
    }
}
