pipeline {
    agent any

    tools {
        dockerTool 'dockerdev'
    }

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
        // On récupère les identifiants Docker Hub de manière sécurisée
        DOCKER_CREDS = credentials('docker-hub-amine')
    }

    stages {
        stage('Build & Push to Docker Hub') {
            steps {
                script {
                    def dockerHome = tool name: 'dockerdev', type: 'dockerTool'
                    def dockerBin = "${dockerHome}/bin/docker"
                    
                    // Connexion, Build et Push via commandes SH directes
                    sh """
                        # Connexion
                        echo \$DOCKER_CREDS_PSW | ${dockerBin} login -u \$DOCKER_CREDS_USR --password-stdin
                        
                        # Build
                        ${dockerBin} build -t ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} .
                        ${dockerBin} tag ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        
                        # Push
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        
                        # Déconnexion par sécurité
                        ${dockerBin} logout
                    """
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                // Utilise l'image spécifique buildée juste avant
                sh "sed -i 's|image:.*|image: ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}|' deployment.yaml"
                sh "kubectl apply -f deployment.yaml"
                sh "kubectl apply -f service.yaml"
            }
        }
    }
}
