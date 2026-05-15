pipeline {
    agent any
    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-credentials')
        EC2_IP = '54.236.21.213'
    }
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
                sh 'echo $DOCKERHUB_CREDENTIALS_PSW | docker login -u $DOCKERHUB_CREDENTIALS_USR --password-stdin'
                sh 'docker push aminebg10/projet-devops-php:latest'
            }
        }
        stage('Deploy to EC2') {
            steps {
                sshagent(['ec2-ssh-key']) {
                    sh '''
                        ssh -o StrictHostKeyChecking=no ubuntu@54.236.21.213 "
                            kubectl apply -f - <<EOF
apiVersion: apps/v1
kind: Deployment
metadata:
  name: php-app-deployment
spec:
  replicas: 2
  selector:
    matchLabels:
      app: php-web
  template:
    metadata:
      labels:
        app: php-web
    spec:
      containers:
      - name: php-container
        image: aminebg10/projet-devops-php:latest
        ports:
        - containerPort: 80
EOF
                            kubectl apply -f - <<EOF
apiVersion: v1
kind: Service
metadata:
  name: php-app-service
spec:
  type: NodePort
  selector:
    app: php-web
  ports:
    - port: 80
      targetPort: 80
      nodePort: 30080
EOF
                        "
                    '''
                }
            }
        }
    }
}