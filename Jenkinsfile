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
                    // On localise précisément où Jenkins a installé Docker
                    def dockerHome = tool name: 'dockerdev', type: 'org.jenkinsci.plugins.docker.commons.tools.DockerTool'
                    def dockerBin = "${dockerHome}/bin/docker"
                    
                    sh """
                        # 1. Connexion (On utilise le chemin complet vers le binaire)
                        echo \$DOCKER_CREDS_PSW | ${dockerBin} login -u \$DOCKER_CREDS_USR --password-stdin
                        
                        # 2. Build
                        ${dockerBin} build -t ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} .
                        
                        # 3. Tags et Push
                        ${dockerBin} tag ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        
                        # 4. Logout
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