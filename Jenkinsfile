pipeline {
    agent any

        stage('Clone Repository') {
           steps {
             script {
               git branch: 'main', url: 'https://github.com/suryaeoxys/readium.git'
              }
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

