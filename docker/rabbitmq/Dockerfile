FROM rabbitmq:management-alpine

COPY --link --chmod=755 docker/rabbitmq/config/init.sh /init.sh
EXPOSE 15672

# Define default command
CMD ["/init.sh"]
