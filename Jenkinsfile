pipeline {
    agent any

    tools {
        // Cela télécharge/configure l'outil dockerdev sur le noeud
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
                    // 1. Récupérer le dossier d'installation de l'outil
                    def dockerHome = tool name: 'dockerdev', type: 'dockerTool'
                    
                    // 2. Définir le chemin du binaire (sous Linux/Jenkins Docker, c'est souvent dans /bin)
                    def dockerBin = "${dockerHome}/bin/docker"
                    
                    // 3. Utiliser withEnv pour s'assurer que le plugin Docker trouve le binaire
                    withEnv(["PATH+DOCKER=${dockerHome}/bin"]) {
                        docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
                            // On construit l'image
                            def customImage = docker.build("${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}")
                            // On la pousse
                            customImage.push()
                            customImage.push("latest")
                        }
                    }
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                // S'assure que kubectl est disponible pour appliquer vos fichiers YAML
                sh "kubectl apply -f deployment.yaml"
                sh "kubectl apply -f service.yaml"
            }
        }
    }
}
