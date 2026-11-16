// Pipeline de JBL Web: integración continua y entrega continua.
//
//   Checkout -> Build -> Test -> Package -> Deploy
//
// Las cuatro primeras etapas corren en todas las ramas (integración continua).
// Deploy solo corre en main y publica la landing en Vercel (entrega continua).
// Jenkins se levanta con jenkins/docker-compose.yml, que ya trae PHP, Composer,
// Node.js y la CLI de Vercel.

pipeline {
    agent any

    options {
        timestamps()
        disableConcurrentBuilds()
        skipDefaultCheckout(true)   // la descarga se hace en la etapa Checkout
        buildDiscarder(logRotator(numToKeepStr: '15'))
    }

    triggers {
        // El webhook de GitHub avisa a Jenkins en cada push.
        githubPush()
    }

    environment {
        PAQUETE = "jbl-landing-${env.BUILD_NUMBER}.tar.gz"
    }

    stages {
        stage('Checkout') {
            steps {
                script {
                    // Se guarda la rama descargada (llega como "origin/main")
                    // para decidir más abajo si corresponde desplegar.
                    def descarga = checkout scm
                    env.RAMA = descarga.GIT_BRANCH ?: ''
                }
                sh 'git log -1 --format="Commit %h de %an: %s"'
            }
        }

        stage('Build') {
            steps {
                dir('erp') {
                    // Dependencias del ERP y archivo de entorno para las pruebas.
                    sh 'composer install --no-interaction --prefer-dist --no-progress'
                    sh 'cp .env.example .env'
                    sh 'php artisan key:generate --ansi'
                }
            }
        }

        stage('Test') {
            steps {
                // Tienda: enlaces de las páginas, catálogo, fotos y sintaxis de JavaScript.
                sh 'node scripts/verificar-landing.mjs'
                // ERP: pruebas de Laravel sobre SQLite en memoria.
                dir('erp') {
                    sh 'php artisan test'
                }
            }
        }

        stage('Package') {
            steps {
                // Empaqueta solo lo que se publica: la landing.
                sh 'tar -czf "$PAQUETE" *.html css js assets data docs/creditos-imagenes.md'
                archiveArtifacts artifacts: "${env.PAQUETE}", fingerprint: true
            }
        }

        stage('Deploy') {
            when {
                expression { env.RAMA in ['main', 'origin/main'] }
            }
            environment {
                // Secretos guardados en Jenkins Credentials (Secret text).
                // Jenkins los oculta con **** si aparecen en la consola.
                VERCEL_TOKEN      = credentials('vercel-token')
                VERCEL_ORG_ID     = credentials('vercel-org-id')
                VERCEL_PROJECT_ID = credentials('vercel-project-id')
            }
            steps {
                // La CLI lee VERCEL_ORG_ID y VERCEL_PROJECT_ID del entorno.
                // .vercelignore deja fuera el ERP y todo lo que no es la landing.
                sh 'vercel deploy --prod --yes --token "$VERCEL_TOKEN" > url-despliegue.txt'
                sh 'echo "Landing publicada en: $(cat url-despliegue.txt)"'
                archiveArtifacts artifacts: 'url-despliegue.txt'
            }
        }
    }

    post {
        success { echo 'Pipeline terminado sin errores.' }
        failure { echo 'El pipeline falló: revisa la etapa marcada en rojo.' }
        always  { cleanWs() }
    }
}
