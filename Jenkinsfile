pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                // Checkout the repository
                git url: 'https://github.com/suryaeoxys/readium.git', branch: 'main'
            }
        }

        stage('Install Composer') {
            steps {
                // Check if composer is installed, if not install it
                bat '''
                if not exist composer (
                    curl -sS https://getcomposer.org/installer -o composer-setup.php
                    php composer-setup.php
                    move composer.phar C:/ProgramData/Jenkins/.jenkins/composer/composer.phar
                    setx PATH "%PATH%;C:/ProgramData/Jenkins/.jenkins/composer"
                )
                '''
            }
        }

        stage('Install Dependencies') {
            steps {
                // Run composer install
                bat 'composer install'
            }
        }

        stage('Run Migrations') {
            steps {
                // Run migrations
                bat 'php artisan migrate'
            }
        }
    }

    post {
        success {
            echo 'Pipeline succeeded!'
        }
        failure {
            echo 'Pipeline failed.'
        }
    }
}
