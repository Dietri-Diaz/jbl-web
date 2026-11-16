# Política de seguridad

JBL Web es un proyecto académico del curso Herramientas de Desarrollo (UTP). La tienda es de demostración: no cobra, no envía productos y no guarda datos reales de clientes.

## Versiones con soporte

| Versión | Soporte |
|---------|---------|
| 2.x | Sí |
| 1.x | No |

## Cómo reportar una vulnerabilidad

No abras un issue público con los detalles de un problema de seguridad.

1. Entra a la pestaña **Security** del repositorio y usa **Report a vulnerability**, o escribe al líder del equipo ([@Dietri-Diaz](https://github.com/Dietri-Diaz)).
2. Indica qué parte afecta (tienda, ERP o API), los pasos para reproducirlo y el impacto que le ves.
3. El equipo lo revisa y publica la corrección en la siguiente versión.

## Medidas que ya tiene el proyecto

- Los secretos del despliegue (token de Vercel e identificadores) están en Jenkins Credentials; no hay claves en el repositorio.
- GitHub revisa cada push en busca de secretos (secret scanning con protección de push).
- El archivo `.env` del ERP está fuera del control de versiones.
- La API pública limita la cantidad de solicitudes por minuto y valida todos los datos en el servidor.
- El panel del ERP exige inicio de sesión y separa los permisos en tres roles.
