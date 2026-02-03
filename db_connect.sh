#!/bin/bash

# MySQL connection parameters
HOST="3.7.158.23"
PORT="3306"
USER="sfa"
PASS="Passw0rd123#$"
DB="acedns_STAR"

# Connect using MySQL client with forced charset
mysql --host="$HOST" --port="$PORT" --user="$USER" --password="$PASS" \
      --database="$DB" --default-character-set=latin1 \
      --execute="SET NAMES binary; SELECT 'Shell connection established' AS status;"

# Alternative if you need to pass the connection to PHP:
# MYSQL_SOCKET=$(mktemp -u)
# mysql --host="$HOST" --port="$PORT" --user="$USER" --password="$PASS" \
#       --database="$DB" --socket="$MYSQL_SOCKET" --skip-column-names \
#       --execute="SET NAMES binary; SELECT CONCAT('unix://', '$MYSQL_SOCKET')" &
# sleep 1  # Give MySQL time to start
# echo "Connection established at $MYSQL_SOCKET"
