script {
    // On récupère le chemin vers l'outil configuré dans Jenkins
    def dockerToolPath = tool name: 'dockerdev', type: 'dockerTool'
    
    withEnv(["PATH+DOCKER=${dockerToolPath}/bin"]) {
        docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
            // Construction
            def customImage = docker.build("aminebg10/amine-devops-app:${env.BUILD_NUMBER}")
            // Push
            customImage.push()
            customImage.push("latest")
        }
    }
}
