FROM koenkk/zigbee2mqtt:2.6

# Copy original configuration files
COPY ./docker/zigbee2mqtt/config/configuration.yaml /app/data/configuration.yaml.orig
COPY ./docker/zigbee2mqtt/config/secret.yaml /app/data/secret.yaml.orig

# Create entrypoint script to copy config files if they don't exist
#RUN echo '#!/bin/sh' > /entrypoint.sh && \
#    echo 'if [ ! -f /app/data/configuration.yaml ]; then' >> /entrypoint.sh && \
#    echo '  cp /app/data/configuration.yaml.orig /app/data/configuration.yaml' >> /entrypoint.sh && \
#    echo 'fi' >> /entrypoint.sh && \
#    echo 'if [ ! -f /app/data/secret.yaml ]; then' >> /entrypoint.sh && \
#    echo '  cp /app/data/secret.yaml.orig /app/data/secret.yaml' >> /entrypoint.sh && \
#    echo 'fi' >> /entrypoint.sh && \
#    echo 'exec node index.js' >> /entrypoint.sh && \
#    chmod +x /entrypoint.sh

ENTRYPOINT ["pnpm install --frozen-lockfile"]
