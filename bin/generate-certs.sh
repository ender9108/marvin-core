#!/usr/bin/env bash
set -euo pipefail

CURRENT_DIR=$(pwd)
ROOT_DIR="${CURRENT_DIR}"
DOCKER_DIR="${ROOT_DIR}/docker"

mkdir -p "${DOCKER_DIR}/certs/ca" "${DOCKER_DIR}/frankenphp/certs" "${DOCKER_DIR}/rabbitmq/certs" "${DOCKER_DIR}/mqtt_broker/certs"

# 1) CA
openssl genrsa -out "${DOCKER_DIR}/certs/ca/ca.key" 4096
openssl req -x509 -new -nodes -key "${DOCKER_DIR}/certs/ca/ca.key" -sha256 -days 3650 \
  -subj "/CN=Marvin Local CA/O=Marvin Dev/C=FR" \
  -out "${DOCKER_DIR}/certs/ca/ca.crt"

gen_server() {
  NAME="$1"        # app | rabbitmq | mqtt_broker
  HOST="$2"        # app | rabbitmq | mqtt_broker (hostname docker)

  cat > "${DOCKER_DIR}/${NAME}/certs/csr.cnf" <<EOF
[req]
distinguished_name = dn
req_extensions = v3_req
prompt = no
[dn]
CN = ${HOST}
O = Marvin Dev
C = FR
[v3_req]
subjectAltName = @alt_names
[alt_names]
DNS.1 = ${HOST}
EOF

  openssl genrsa -out "${DOCKER_DIR}/${NAME}/certs/server.key" 2048
  openssl req -new -key "${DOCKER_DIR}/${NAME}/certs/server.key" -out "${DOCKER_DIR}/${NAME}/certs/server.csr" -config "${DOCKER_DIR}/${NAME}/certs/csr.cnf"

  cat > "${DOCKER_DIR}/${NAME}/certs/ca.ext" <<EOF
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, keyEncipherment
extendedKeyUsage = serverAuth
subjectAltName = @alt_names
[alt_names]
DNS.1 = ${HOST}
EOF

  openssl x509 -req -in "${DOCKER_DIR}/${NAME}/certs/server.csr" -CA "${DOCKER_DIR}/certs/ca/ca.crt" -CAkey "${DOCKER_DIR}/certs/ca/ca.key" \
    -CAcreateserial -out "${DOCKER_DIR}/${NAME}/certs/server.crt" -days 825 -sha256 -extfile "${DOCKER_DIR}/${NAME}/certs/ca.ext"
  rm -f "${DOCKER_DIR}/${NAME}/certs/csr.cnf" "${DOCKER_DIR}/${NAME}/certs/server.csr" "${DOCKER_DIR}/${NAME}/certs/ca.ext"
}

gen_server frankenphp frankenphp
gen_server rabbitmq rabbitmq
gen_server mqtt_broker mqtt_broker

echo "✅ Certificats générés"
