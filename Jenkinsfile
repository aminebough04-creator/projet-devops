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
                    def dockerHome = tool name: 'dockerdev', type: 'dockerTool'
                    def dockerBin = "${dockerHome}/bin/docker"
                    
                    sh """
                        echo \$DOCKER_CREDS_PSW | ${dockerBin} login -u \$DOCKER_CREDS_USR --password-stdin
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
                script {
                    // Cette partie télécharge kubectl et déploie l'application
                    sh """
                        curl -LO "https://dl.k8s.io/release/\$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
                        chmod +x kubectl
                        
                        sed -i 's|image:.*|image: ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}|' deployment.yaml
                        
                        ./kubectl apply -f deployment.yaml
                        ./kubectl apply -f service.yaml
                    """
                }
            }
        }
    }
}
