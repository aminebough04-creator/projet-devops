pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps { checkout scm }
        }
        stage('Build Image') {
            steps {
                sh 'docker build -t aminebg10/projet-devops-php:latest .'
            }
        }
        stage('Push to Hub') {
            steps {
                // On configurera les secrets plus tard dans Jenkins
                sh 'docker push aminebg10/projet-devops-php:latest'
            }
        }
        stage('Deploy K8s') {
            steps {
                sh 'kubectl apply -f deployment.yaml'
                sh 'kubectl apply -f service.yaml'
            }
        }
    }
}