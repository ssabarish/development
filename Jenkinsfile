pipeline {
  agent any
  stages {
    stage('error') {
      steps {
        sh '''cd /home/s/new/
rm -rf development
git clone https://github.com/ssabarish/development.git'''
      }
    }
  }
}