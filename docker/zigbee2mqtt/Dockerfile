FROM koenkk/zigbee2mqtt

RUN id -u ${UNIX_UID} &>/dev/null || adduser --uid ${UNIX_UID} --gid ${UNIX_GID}
RUN usermod -a -G dialout ${UNIX_UID}

COPY docker/zigbee2mqtt/config/configuration.yaml /app/data/configuration.yaml
COPY docker/zigbee2mqtt/config/secret.yaml /app/data/secret.yaml

# Run supervisor
CMD ["/opt/zigbee2mqtt/pnpm start"]
