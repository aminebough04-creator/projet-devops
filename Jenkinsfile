pipeline {
    agent any

    tools {
        dockerTool 'dockerdev' 
    }

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
        DOCKER_CREDS = credentials('docker-hub-amine')
        // Force le client à utiliser une version d'API compatible avec Docker Desktop
        DOCKER_API_VERSION = '1.40'
    }

    stages {
        stage('Build & Push to Docker Hub') {
            steps {
                script {
                    def dockerHome = tool name: 'dockerdev', type: 'org.jenkinsci.plugins.docker.commons.tools.DockerTool'
                    def dockerBin = "${dockerHome}/bin/docker"
                    
                    sh """
                        # Connexion
                        ${dockerBin} login -u \$DOCKER_CREDS_USR -p \$DOCKER_CREDS_PSW
                        
                        # Build avec l'image
                        ${dockerBin} build -t ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} .
                        
                        # Tags et Push
                        ${dockerBin} tag ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        
                        # Logout
                        ${dockerBin} logout
                    """
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                kubernetesDeploy(
                    configs: 'deployment.yaml',
                    kubeconfigId: 'k8s-config-file'
                )
            }
        }
    }
}