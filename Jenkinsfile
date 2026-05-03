stage('Build Docker Image') {
    steps {
        script {
            // Cette syntaxe utilise le plugin Docker au lieu du terminal simple
            docker.build("amine-devops-app:${env.BUILD_NUMBER}")
        }
    }
}
