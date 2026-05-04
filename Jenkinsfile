stage('Deploy to Kubernetes') {
            steps {
                script {
                    // On télécharge kubectl dans le répertoire courant pour ce build
                    sh """
                        curl -LO "https://dl.k8s.io/release/\$(curl -L -s https://dl.k8s.io/release/stable.txt)/bin/linux/amd64/kubectl"
                        chmod +x kubectl
                        
                        # Mise à jour du fichier YAML
                        sed -i 's|image:.*|image: ${DOCKER_HUB_USER}/${APP_NAME}:${env.BUILD_NUMBER}|' deployment.yaml
                        
                        # Utilisation du kubectl local (./kubectl)
                        ./kubectl apply -f deployment.yaml
                        ./kubectl apply -f service.yaml
                    """
                }
            }
        }
