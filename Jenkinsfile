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
                    def dockerHome = tool name: 'dockerdev', type: 'org.jenkinsci.plugins.docker.commons.tools.DockerTool'
                    def dockerBin = "${dockerHome}/bin/docker"
                    
                    sh """
                        ${dockerBin} login -u \$DOCKER_CREDS_USR -p \$DOCKER_CREDS_PSW
                        ${dockerBin} build -t ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} .
                        ${dockerBin} tag ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}
                        ${dockerBin} push ${DOCKER_HUB_USER}/${APP_NAME}:latest
                        ${dockerBin} logout
                    """
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                // On utilise withKubeConfig qui est disponible sur votre Jenkins
                withKubeConfig([credentialsId: 'k8s-config-file']) {
                    sh """
                        # On met à jour l'image dans le fichier YAML avant d'appliquer
                        sed -i 's|image: .*|image: ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}|g' deployment.yaml
                        
                        # Déploiement standard
                        kubectl apply -f deployment.yaml
                        
                        # Vérification du statut
                        kubectl rollout status deployment/amine-app
                    """
                }
            }
        }
    }
}