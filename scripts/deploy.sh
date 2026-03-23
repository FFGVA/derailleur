#!/bin/bash
#
# Dérailleur — Hostpoint Deployment Script
# Deploys Laravel application via FTP to Hostpoint shared hosting.
#
# Usage: ./scripts/deploy.sh
#
# Prerequisites:
#   - lftp installed (apt install lftp)
#   - scripts/ftp.conf with FTP credentials (line 1=user, line 2=password, line 3=host)
#   - .env.production with production settings (DB password, APP_KEY filled in)
#
# Production layout on Hostpoint:
#   FTP root (/)        = Laravel app root (app/, config/, vendor/, etc.)
#   FTP root /public/   = Apache document root (index.php, .htaccess, assets)
#
# The standalone mail scripts (chaine.php, guidon.php) stay in public/
# alongside the Laravel front controller until the Hugo site switches
# to the /api/ endpoints. The rate/ folder is inside public/.
#
# IMPORTANT: public/.htaccess on Hostpoint has a managed header line
# ("Use php-fpm latest") that must NOT be overwritten. The script
# preserves it by excluding .htaccess from the mirror.
#

set -e

# ── Configuration ──────────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
DEPLOY_TEMP="/tmp/derailleur-deploy-$$"
FTP_CREDS_FILE="$SCRIPT_DIR/ftp.conf"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}=== Dérailleur — Deployment to Hostpoint ===${NC}"
echo "Project: $PROJECT_DIR"
echo ""

# ── Pre-flight checks ─────────────────────────────────────────────
if ! command -v lftp &> /dev/null; then
    echo -e "${RED}ERROR: lftp is not installed. Run: sudo apt install lftp${NC}"
    exit 1
fi

if [[ ! -f "$FTP_CREDS_FILE" ]]; then
    echo -e "${RED}ERROR: FTP credentials file not found: $FTP_CREDS_FILE${NC}"
    echo "  Expected format: line 1 = FTP user, line 2 = FTP password, line 3 = FTP host"
    exit 1
fi

FTP_USER=$(sed -n '1p' "$FTP_CREDS_FILE")
FTP_PASS=$(sed -n '2p' "$FTP_CREDS_FILE")
FTP_HOST=$(sed -n '3p' "$FTP_CREDS_FILE")

if [[ -z "$FTP_USER" || -z "$FTP_PASS" || -z "$FTP_HOST" ]]; then
    echo -e "${RED}ERROR: Invalid FTP credentials file (need 3 lines: user, password, host)${NC}"
    exit 1
fi

if [[ ! -f "$PROJECT_DIR/.env.production" ]]; then
    echo -e "${RED}ERROR: .env.production not found. Create it first.${NC}"
    exit 1
fi

if grep -q "CHANGE_ME\|GENERATE_WITH" "$PROJECT_DIR/.env.production"; then
    echo -e "${RED}ERROR: .env.production still has placeholder values. Configure it first:${NC}"
    grep -n "CHANGE_ME\|GENERATE_WITH" "$PROJECT_DIR/.env.production"
    exit 1
fi

echo -e "${GREEN}Target: $FTP_HOST${NC}"
echo ""

# ── Step 1: Clear caches ──────────────────────────────────────────
echo -e "${GREEN}[1/5] Clearing caches...${NC}"
cd "$PROJECT_DIR"
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# ── Step 2: Prepare deployment package ─────────────────────────────
echo -e "${GREEN}[2/5] Preparing deployment package...${NC}"
rm -rf "$DEPLOY_TEMP"
mkdir -p "$DEPLOY_TEMP"

# Copy the full Laravel app, excluding dev-only files
rsync -a \
    --exclude='.git' \
    --exclude='.gitignore' \
    --exclude='.gitattributes' \
    --exclude='.editorconfig' \
    --exclude='.claude' \
    --exclude='.phpunit.cache' \
    --exclude='.phpunit.result.cache' \
    --exclude='.env' \
    --exclude='.env.backup' \
    --exclude='.env.production' \
    --exclude='.env.example' \
    --exclude='node_modules' \
    --exclude='tests' \
    --exclude='phpunit.xml' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*.php' \
    --exclude='storage/app/private/livewire-tmp' \
    --exclude='storage/app/private/invoices' \
    --exclude='bootstrap/cache/*.php' \
    --exclude='scripts/deploy.sh' \
    --exclude='scripts/ftp.conf' \
    --exclude='database' \
    --exclude='resources/css/app.css' \
    --exclude='resources/js/app.js' \
    --exclude='resources/js/bootstrap.js' \
    --exclude='CLAUDE.md' \
    --exclude='README.md' \
    --exclude='composer.lock' \
    --exclude='package.json' \
    --exclude='package-lock.json' \
    --exclude='vite.config.js' \
    "$PROJECT_DIR/" "$DEPLOY_TEMP/"

# Copy .env.production as .env
cp "$PROJECT_DIR/.env.production" "$DEPLOY_TEMP/.env"

# Remove public/storage symlink (Hostpoint doesn't support symlinks)
rm -f "$DEPLOY_TEMP/public/storage"

# Remove test-forms page (dev only)
rm -f "$DEPLOY_TEMP/resources/views/test-forms.blade.php"

# Ensure required directories exist with placeholders
for dir in storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache public/rate; do
    mkdir -p "$DEPLOY_TEMP/$dir"
    touch "$DEPLOY_TEMP/$dir/.gitkeep"
done

# Ensure rate directory has deny .htaccess
echo "Require all denied" > "$DEPLOY_TEMP/public/rate/.htaccess"

# Copy standalone mail scripts into public/ (kept until Hugo switches to /api/)
if [[ -d "$PROJECT_DIR/mail" ]]; then
    cp "$PROJECT_DIR/mail/chaine.php" "$DEPLOY_TEMP/public/chaine.php" 2>/dev/null || true
    cp "$PROJECT_DIR/mail/guidon.php" "$DEPLOY_TEMP/public/guidon.php" 2>/dev/null || true
fi

# ── Step 3: Verify package ─────────────────────────────────────────
echo -e "${GREEN}[3/5] Verifying package...${NC}"

MISSING=0
for file in ".env" "artisan" "config/database.php" "public/index.php" "public/.htaccess" "vendor/autoload.php" "public/chaine.php" "public/guidon.php"; do
    if [[ -f "$DEPLOY_TEMP/$file" ]]; then
        echo "  ✓ $file"
    else
        echo -e "  ${RED}✗ MISSING: $file${NC}"
        MISSING=1
    fi
done

if [[ $MISSING -eq 1 ]]; then
    echo -e "${RED}Aborting: missing critical files${NC}"
    rm -rf "$DEPLOY_TEMP"
    exit 1
fi

# Safety checks on production .env
if grep -q "^APP_DEBUG=true" "$DEPLOY_TEMP/.env"; then
    echo -e "  ${RED}✗ APP_DEBUG=true in production .env!${NC}"
    rm -rf "$DEPLOY_TEMP"
    exit 1
fi
echo "  ✓ APP_DEBUG is not true"

if grep -q "^APP_ENV=local" "$DEPLOY_TEMP/.env"; then
    echo -e "  ${RED}✗ APP_ENV=local in production .env!${NC}"
    rm -rf "$DEPLOY_TEMP"
    exit 1
fi
echo "  ✓ APP_ENV is not local"

TOTAL_SIZE=$(du -sh "$DEPLOY_TEMP" | cut -f1)
echo "  Package size: $TOTAL_SIZE"

echo -e "${GREEN}[4/5] Uploading to production...${NC}"
lftp -u "$FTP_USER","$FTP_PASS" "$FTP_HOST" << LFTP_EOF
set ssl:verify-certificate no
set ftp:ssl-allow yes
set net:timeout 60
set net:max-retries 5
set ftp:passive-mode yes

# Mirror entire Laravel app to FTP root.
# Exclude .htaccess everywhere — Hostpoint manages its own header in public/.htaccess.
# Exclude server-side state (logs, sessions, cache, rate-limit files).
mirror -R --verbose=1 --parallel=4 --no-perms --delete \
    --exclude ^\.htaccess$ \
    --exclude \.htaccess$ \
    --exclude-glob storage/logs/*.log \
    --exclude-glob storage/framework/sessions/* \
    --exclude-glob storage/framework/cache/data/* \
    --exclude-glob storage/framework/views/*.php \
    --exclude-glob public/rate/*.json \
    --exclude-glob storage/app/private/invoices/* \
    "$DEPLOY_TEMP" "/"
bye
LFTP_EOF

# ── Step 5: Cleanup ───────────────────────────────────────────────
echo -e "${GREEN}[5/5] Cleanup...${NC}"
rm -rf "$DEPLOY_TEMP"
unset FTP_USER FTP_PASS FTP_HOST

echo ""
echo -e "${GREEN}=== Deployment Complete ===${NC}"
echo ""
echo "Post-deployment checklist:"
echo "  1. Verify: https://derailleur.ffgva.ch/admin"
echo "  2. First deploy only:"
echo "     - Run database/create_database.sql on Hostpoint MariaDB"
echo "     - Generate APP_KEY: php artisan key:generate --show"
echo "     - Set APP_KEY and DB_PASSWORD in .env via FTP"
echo "     - Verify public/.htaccess has 'Use php-fpm latest' header"
echo "  3. If errors: check storage/logs/laravel.log via FTP"
