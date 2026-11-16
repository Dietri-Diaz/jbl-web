# Jenkins con las herramientas que usa el pipeline de JBL Web:
# PHP y Composer (ERP en Laravel), Node.js (verificación de la landing)
# y la CLI de Vercel (despliegue).
FROM jenkins/jenkins:lts-jdk17

USER root

# PHP (el de la distribución base, hoy 8.4) con las extensiones de Laravel, más utilidades del sistema.
RUN apt-get update && apt-get install -y --no-install-recommends \
        php-cli php-mbstring php-xml php-curl php-zip php-sqlite3 php-mysql php-bcmath php-gd \
        unzip git curl ca-certificates gnupg \
    && rm -rf /var/lib/apt/lists/*

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Node.js 22 y la CLI de Vercel
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && npm install --global vercel \
    && rm -rf /var/lib/apt/lists/*

USER jenkins

# Plugins que necesita el pipeline (se instalan al construir la imagen).
RUN jenkins-plugin-cli --plugins \
    workflow-aggregator \
    git \
    github \
    credentials-binding \
    pipeline-stage-view \
    timestamper \
    ws-cleanup
