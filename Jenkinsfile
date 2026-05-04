stage('Push to Docker Hub') {
    steps {
        script {
            // "docker-hub-amine" doit correspondre à l'ID choisi à l'étape 2
            docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
                // Remplacez 'votre_user' par votre vrai pseudo Docker Hub
                def myImage = docker.build("votre_user/amine-devops-app:${env.BUILD_NUMBER}")
                myImage.push()
            }
        }
    }
}
