pipeline {
    agent any

    tools {
        dockerTool 'dockerdev' 
    }

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
        DOCKER_CREDS = credentials('docker-hub-amine')
    }

    stages {
        stage('Build & Push to Docker Hub') {
            steps {
                script {
                    // Localisation du binaire Docker
                    def dockerHome = tool name: 'dockerdev', type: 'org.jenkinsci.plugins.docker.commons.tools.DockerTool'
                    def dockerBin = "${dockerHome}/bin/docker"
                    
                    sh """
                        # Connexion compatible avec les anciennes versions
                        ${dockerBin} login -u \$DOCKER_CREDS_USR -p \$DOCKER_CREDS_PSW
                        
                        # Build de l'image
                        ${dockerBin} build -t ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} .
                        
                        # Tags et Push
                        ${dockerBin} tag ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        
                        # Déconnexion
                        ${dockerBin} logout
                    """
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                // Déploiement avec le nom de conteneur amine-app
                kubernetesDeploy(
                    configs: 'deployment.yaml',
                    kubeconfigId: 'k8s-config-file'
                )
            }
        }
    }
}