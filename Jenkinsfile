pipeline {
  agent any
  stages {
    stage('clone') {
      parallel {
        stage('clone') {
          steps {
            sh '''cd /home/s/new/node-docker-demo
git checkout start-here
git pull
'''
          }
        }
        stage('build image') {
          steps {
            sh '''cd /home/s/giri
cp Dockerfile customer_web/.
docker build -t giri/app-$(date +%Y-%m-%d) customer_web/.'''
          }
        }
      }
    }
  }
}