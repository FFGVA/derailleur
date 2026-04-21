#!/usr/bin/env bash
#
# Dérailleur — Local DB loader.
# Drops and recreates the local database, loads the latest production dump,
# then applies local-only seeds (admin@ffgva.ch).
#
# Usage:
#   ./scripts/db-load.sh                             # uses database/agiletra_ffgva.sql
#   ./scripts/db-load.sh path/to/other/dump.sql
#
# ALWAYS use this script to refresh local data — never run `mysql < dump.sql`
# directly, because the local admin user won't be seeded.
#

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
DUMP="${1:-$PROJECT_DIR/database/agiletra_ffgva.sql}"
SEED="$PROJECT_DIR/database/seed_admin.sql"
ENV_FILE="$PROJECT_DIR/.env"

if [[ ! -f "$DUMP" ]]; then
    echo "Error: dump file not found at $DUMP" >&2
    exit 1
fi

if [[ ! -f "$SEED" ]]; then
    echo "Error: seed file not found at $SEED" >&2
    exit 1
fi

if [[ ! -f "$ENV_FILE" ]]; then
    echo "Error: .env not found at $ENV_FILE" >&2
    exit 1
fi

get_env() {
    grep -E "^$1=" "$ENV_FILE" | head -1 | cut -d= -f2- | tr -d '"' | tr -d "'"
}

DB_HOST="$(get_env DB_HOST)"
DB_PORT="$(get_env DB_PORT)"
DB_USER="$(get_env DB_USERNAME)"
DB_NAME="$(get_env DB_DATABASE)"
export MYSQL_PWD="$(get_env DB_PASSWORD)"

MYSQL=(mysql "-h${DB_HOST:-127.0.0.1}" "-P${DB_PORT:-3306}" "-u${DB_USER}")

echo "→ Dropping and recreating database ${DB_NAME}"
"${MYSQL[@]}" -e "DROP DATABASE IF EXISTS \`${DB_NAME}\`; CREATE DATABASE \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "→ Loading dump ${DUMP}"
"${MYSQL[@]}" "${DB_NAME}" < "${DUMP}"

echo "→ Seeding local admin user"
"${MYSQL[@]}" "${DB_NAME}" < "${SEED}"

echo "✓ Done. Local admin: admin@ffgva.ch / password"
