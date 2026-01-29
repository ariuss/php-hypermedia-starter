#!/usr/bin/env bash
set -euo pipefail

DB_FILE="storage/database/app.db"
MIGRATIONS_DIR="./migrations"
COMMAND="${1:-up}"

sqlite3 "$DB_FILE" <<EOF
CREATE TABLE IF NOT EXISTS schema_migrations (
    version TEXT PRIMARY KEY,
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
EOF

apply_up() {
  for file in $(ls "$MIGRATIONS_DIR"/*.up.sql 2>/dev/null | sort); do
    version=$(basename "$file" | cut -d_ -f1)

    applied=$(sqlite3 "$DB_FILE" "SELECT COUNT(1) FROM schema_migrations WHERE version = '$version';")

    if [[ "$applied" -eq 0 ]]; then
      echo "▶ Applying $file"
      sqlite3 "$DB_FILE" < "$file"
      sqlite3 "$DB_FILE" "INSERT INTO schema_migrations (version) VALUES ('$version');"
    else
      echo "✓ Skipping $file (already applied)"
    fi
  done
}

apply_down() {
  version=$(sqlite3 "$DB_FILE" "SELECT version FROM schema_migrations ORDER BY version DESC LIMIT 1;")

  if [[ -z "$version" ]]; then
    echo "Nothing to rollback"
    exit 0
  fi

  file="$MIGRATIONS_DIR/${version}_"*.down.sql

  echo "◀ Rolling back $file"
  sqlite3 "$DB_FILE" < $file
  sqlite3 "$DB_FILE" "DELETE FROM schema_migrations WHERE version = '$version';"
}

to_snake_case() {
  echo "$1" \
    | tr '[:upper:]' '[:lower:]' \
    | sed -E 's/[^a-z0-9]+/_/g' \
    | sed -E 's/^_+|_+$//g'
}

create_migration() {
  if [[ $# -lt 1 ]]; then
    echo "Usage: $0 create <migration_name>"
    exit 1
  fi

  raw_name="$*"
  name="$(to_snake_case "$raw_name")"
  timestamp=$(date +%s)

  up_file="$MIGRATIONS_DIR/${timestamp}_${name}.up.sql"
  down_file="$MIGRATIONS_DIR/${timestamp}_${name}.down.sql"

  mkdir -p "$MIGRATIONS_DIR"

  if [[ -e "$up_file" || -e "$down_file" ]]; then
    echo "Migration already exists"
    exit 1
  fi

  cat > "$up_file" <<EOF
-- Up migration: $name
BEGIN TRANSACTION;

-- Write your SQL here

COMMIT;
EOF

  cat > "$down_file" <<EOF
-- Down migration: $name
BEGIN TRANSACTION;

-- Write rollback SQL here

COMMIT;
EOF

  echo "✓ Created migration:"
  echo "  $up_file"
  echo "  $down_file"
}

case "$COMMAND" in
  up)
    apply_up
    ;;
  down)
    apply_down
    ;;
  create)
    shift
    create_migration "$@"
    ;;
  *)
    echo "Usage: $0 [up|down|create <name>]"
    exit 1
    ;;
esac

