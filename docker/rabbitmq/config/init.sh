#!/bin/sh

# Create Rabbitmq user
( sleep 5 ; \
rabbitmq-plugins enable rabbitmq_mqtt ; \
rabbitmqctl add_user $RABBITMQ_USER $RABBITMQ_PASSWORD 2>/dev/null ; \
rabbitmqctl set_user_tags $RABBITMQ_USER administrator ; \
rabbitmqctl set_permissions -p / $RABBITMQ_USER  ".*" ".*" ".*" ; \
echo "*** User '$RABBITMQ_USER' created. ***" ; \
rabbitmqctl add_user $RABBITMQ_MQTT_USER $RABBITMQ_MQTT_PASSWORD 2>/dev/null ; \
rabbitmqctl set_user_tags $RABBITMQ_MQTT_USER management ; \
rabbitmqctl set_permissions -p / $RABBITMQ_MQTT_USER  ".*" ".*" ".*" ; \
echo "*** User '$RABBITMQ_MQTT_USER' created. ***" ; \
echo "*** Log in the WebUI at port 15672 (example: http://localhost:15672) ***" ; \
rabbitmqctl delete_user guest ; \
echo "*** User guest deleted. ***" ;) &

# $@ is used to pass arguments to the rabbitmq-server command.
# For example if you use it like this: docker run -d rabbitmq arg1 arg2,
# it will be as you run in the container rabbitmq-server arg1 arg2
rabbitmq-server $@
