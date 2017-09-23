pipeline {
    agent any
    stages {
        stage('Build') {
            steps {
		sh 'echo "Composer update"'
                sh 'composer selfupdate'
		sh 'echo "Install dependencies"'
		sh 'composer install'
            }
        }
    }
}
