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
        stage('error') {
          steps {
            sh '''cd /home/s/new/node-docker-demo
docker run --rm -v $(pwd):/app -w /app node:9 node hello.js
'''
          }
        }
      }
    }
  }
}