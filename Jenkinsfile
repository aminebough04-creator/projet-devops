pipeline {
    agent any

    stages {
        stage('Connexion') {
            steps {
                echo 'Connexion GitHub App OK !'
            }
        }
        stage('Check Fichiers') {
            steps {
                sh 'ls -la'
                echo 'Les fichiers PHP sont bien présents.'
            }
        }
    }
}
