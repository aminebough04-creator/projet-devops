pipeline {
    agent any

    tools {
        dockerTool 'dockerdev' 
    }

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
    }

    stages {
        stage('Push to Docker Hub') {
            steps {
                script {
                    // Récupération du chemin de l'outil
                    def dockerHome = tool name: 'dockerdev', type: 'org.jenkinsci.plugins.docker.commons.tools.DockerTool'
                    
                    // Ajout au PATH et utilisation du bloc natif pour la connexion
                    withEnv(["PATH+DOCKER=${dockerHome}/bin"]) {
                        // Ce bloc gère le 'docker login' et 'docker logout' automatiquement
                        docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
                            
                            // Build de l'image
                            def myImage = docker.build("${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}")
                            
                            // Push des tags
                            myImage.push()
                            myImage.push("latest")
                        }
                    }
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