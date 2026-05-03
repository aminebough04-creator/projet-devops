pipeline {
    agent any

    environment {
        // Nom de votre image Docker
        DOCKER_IMAGE = "amine-devops-app"
    }

    stages {
        stage('Checkout') {
            steps {
                // Récupère le code depuis la branche main
                checkout scm
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    echo "Construction de l'image Docker..."
                    // On utilise le numéro de build Jenkins (#4, #5...) comme tag
                    sh "docker build -t ${DOCKER_IMAGE}:${BUILD_NUMBER} ."
                    sh "docker build -t ${DOCKER_IMAGE}:latest ."
                }
            }
        }

        stage('Vérification') {
            steps {
                echo "Liste des images créées :"
                sh "docker images | grep ${DOCKER_IMAGE}"
            }
        }
    }
}
