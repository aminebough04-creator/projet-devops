pipeline {
    agent any
    tools {
        // Cela doit correspondre exactement au nom dans l'image image_d4d3e0.png
        dockerTool 'dockerdev' 
    }
    stages {
        stage('Build Docker Image') {
            steps {
                script {
                    // Utilisation de la syntaxe native du plugin
                    docker.build("amine-devops-app:${env.BUILD_NUMBER}")
                }
            }
        }
    }
}
