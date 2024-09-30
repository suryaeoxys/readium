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
                if not exist C:/ProgramData/Jenkins/.jenkins/composer/composer.phar (
                    curl -sS https://getcomposer.org/installer -o composer-setup.php
                    C:/ProgramData/Jenkins/.jenkins/php.exe composer-setup.php --install-dir=C:/ProgramData/Jenkins/.jenkins/composer --filename=composer.phar
                    setx PATH "%PATH%;C:/ProgramData/Jenkins/.jenkins/composer"
                )
                '''
            }
        }

        stage('Install Dependencies') {
            steps {
                // Use the full path for Composer to install dependencies
                bat 'C:/ProgramData/Jenkins/.jenkins/composer/composer.phar install --ignore-platform-reqs'
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
