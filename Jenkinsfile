pipeline {
    agent any

    tools {
        // Assure-toi que ce nom correspond exactement à celui dans "Global Tool Configuration"
        dockerTool 'dockerdev' 
    }

    environment {
        DOCKER_HUB_USER = 'aminebg10'
        APP_NAME = 'amine-devops-app'
        // Identifiants Docker Hub stockés dans Jenkins
        DOCKER_CREDS = credentials('docker-hub-amine')
    }

    stages {
        stage('Push to Docker Hub') {
            steps {
                script {
                    // 1. On récupère le chemin dynamique où Jenkins a installé Docker
                    def dockerHome = tool name: 'dockerdev', type: 'org.jenkinsci.plugins.docker.commons.tools.DockerTool'
                    
                    // 2. On ajoute le dossier 'bin' de Docker au PATH pour cette étape
                    withEnv(["PATH+DOCKER=${dockerHome}/bin"]) {
                        sh """
                            # Connexion au Docker Hub
                            echo \$DOCKER_CREDS_PSW | docker login -u \$DOCKER_CREDS_USR --password-stdin
                            
                            # Build de l'image avec le numéro de build
                            docker build -t ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} .
                            
                            # Création du tag 'latest'
                            docker tag ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER} ${DOCKER_HUB_USER}/${APP_NAME}:latest
                            
                            # Envoi des deux tags vers Docker Hub
                            docker push ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}
                            docker push ${DOCKER_HUB_USER}/${APP_NAME}:latest
                            
                            # Déconnexion par sécurité
                            docker logout
                        """
                    }
                }
            }
        }

        stage('Deploy to Kubernetes') {
            steps {
                // Utilise le plugin Kubernetes Continuous Deploy
                kubernetesDeploy(
                    configs: 'deployment.yaml',
                    kubeconfigId: 'k8s-config-file'
                )
            }
        }
    }
}