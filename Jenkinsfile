pipeline {
    agent any

    // C'EST ICI QUE VOUS AJOUTEZ LE BLOC TOOLS
    tools {
        dockerTool 'dockerdev' // 'dockerdev' est le nom configuré dans Jenkins
    }

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
    }

    stages {
        stage('Push to Docker Hub') {
            steps {
                script {
                    docker.withRegistry('https://index.docker.io/v1/', 'docker-hub-amine') {
                        def myImage = docker.build("${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}")
                        myImage.push()
                        myImage.push("latest")
                    }
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                script {
                    kubernetesDeploy(
                        configs: 'deployment.yaml',
                        kubeconfigId: 'k8s-config-file'
                    )
                }
            }
        }
    }
}