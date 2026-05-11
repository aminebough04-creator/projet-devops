pipeline {
    agent any

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
    }

    stages {
        stage('Push to Docker Hub') {
            steps {
                script {
                    // Utilise le plugin Docker Pipeline avec vos identifiants Jenkins
                    docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
                        // Construction de l'image avec le numéro de build dynamique
                        def myImage = docker.build("${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}")
                        
                        // Push de la version spécifique et de la version 'latest'
                        myImage.push()
                        myImage.push("latest")
                    }
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                script {
                    // Utilise le plugin Kubernetes Continuous Deploy
                    // L'ID 'k8s-config-file' doit correspondre au "Secret file" créé dans Jenkins
                    kubernetesDeploy(
                        configs: 'deployment.yaml',
                        kubeconfigId: 'k8s-config-file'
                    )
                }
            }
        }
    }
}