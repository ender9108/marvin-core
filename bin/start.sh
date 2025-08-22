#!/bin/bash

#BASE_DIR=$(realpath "$(dirname "$0")/../")
#FILES_COMPOSE_LIST="${BASE_DIR}/docker/compose_map.json"

#ITEMS=$(echo "$JSON_Content" | jq -c -r '.[]' $FILES_COMPOSE_LIST)
#DOCKER_CMD="docker compose "

#for ITEM in ${ITEMS[@]}; do
#    DOCKER_CMD="${DOCKER_CMD} -f ${ITEM}"
#    # whatever you are trying to do ...
#done

#DOCKER_CMD="${DOCKER_CMD} up -d --force-recreate --remove-orphans"

#$($DOCKER_CMD)

docker compose up -d --remove-orphans
