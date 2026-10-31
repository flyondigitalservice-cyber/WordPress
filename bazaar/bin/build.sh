#!/usr/bin/env bash
#
# Build installable zips for Bazaar Core + the Bazaar themes, and the update manifest.
#
# Usage (from anywhere):
#   bazaar/bin/build.sh https://example.com/bazaar
#
# The argument is the public HTTPS folder where you will upload the files. Output goes to
# bazaar/dist/: one zip per package plus manifest.json. Upload everything in dist/ to that
# folder, and add to each client's wp-config.php:
#   define( 'BAZAAR_UPDATE_URL', 'https://example.com/bazaar/manifest.json' );
#
# Release checklist: bump "Version" in the plugin header / theme style.css (and
# BAZAAR_CORE_VERSION for the plugin), regenerate .pot files (node bazaar/bin/make-pot.js),
# then run this script.

set -euo pipefail

BASE_URL="${1:-}"
if [[ ! "$BASE_URL" =~ ^https:// ]]; then
	echo "Usage: $0 https://your-server/path   (must be HTTPS)" >&2
	exit 1
fi
BASE_URL="${BASE_URL%/}"

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DIST="$ROOT/bazaar/dist"
rm -rf "$DIST"
mkdir -p "$DIST"

# slug|path|header file
PACKAGES=(
	"bazaar-core|wp-content/plugins/bazaar-core|bazaar-core.php"
	"bazaar-store|wp-content/themes/bazaar-store|style.css"
	"bazaar-electronics|wp-content/themes/bazaar-electronics|style.css"
	"bazaar-boutique|wp-content/themes/bazaar-boutique|style.css"
)

header() { # file field
	grep -m1 -E "^[ *]*$2:" "$1" | sed -E "s/^[ *]*$2:[[:space:]]*//" | tr -d '\r'
}

json="{"
sep=""
for entry in "${PACKAGES[@]}"; do
	IFS='|' read -r slug path file <<<"$entry"
	src="$ROOT/$path"
	version="$(header "$src/$file" "Version")"
	requires="$(header "$src/$file" "Requires at least")"
	requires_php="$(header "$src/$file" "Requires PHP")"
	tested="$(header "$src/$file" "Tested up to" || true)"
	zip_name="$slug-$version.zip"

	if [[ "$slug" == "bazaar-core" ]] && ! grep -q "BAZAAR_CORE_VERSION', '$version'" "$src/$file"; then
		echo "bazaar-core: BAZAAR_CORE_VERSION does not match header Version $version" >&2
		exit 1
	fi

	(cd "$(dirname "$src")" && zip -qr "$DIST/$zip_name" "$slug" -x "*/.DS_Store" "*/node_modules/*" "*/.git*")
	echo "Built $zip_name"

	json+="$sep
	\"$slug\": { \"version\": \"$version\", \"package\": \"$BASE_URL/$zip_name\", \"requires\": \"$requires\", \"requires_php\": \"$requires_php\", \"tested\": \"$tested\" }"
	sep=","
done
json+="
}"

printf '%s\n' "$json" >"$DIST/manifest.json"
php -r 'json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR);' "$DIST/manifest.json"
echo "Wrote $DIST/manifest.json"
