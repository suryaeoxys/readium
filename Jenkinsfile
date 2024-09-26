pipeline {
    agent any

    stages {
        stage('Clone Repository') {
            steps {
                git 'https://github.com/suryaeoxys/readium.git'
            }
        }

        stage('Copy .env File') {
            steps {
                // Replace with the actual path to your local .env file on the Jenkins server
                bat 'J:\Projects\readium\.env'
            }
        }

        stage('Install Dependencies') {
            steps {
                bat 'composer install'
            }
        }

        stage('Run Migrations') {
            steps {
                bat 'php artisan migrate'
            }
        }

        // Add other stages like running tests, etc.
    }

    post {
        success {
            echo 'Pipeline completed successfully.'
        }
        failure {
            echo 'Pipeline failed.'
        }
    }
}

