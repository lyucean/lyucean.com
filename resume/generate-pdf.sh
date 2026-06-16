#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "$0")" && pwd)"
HTML="$DIR/panchenko-valentin.html"
OUT_YANDEX="/Users/admin/Yandex.Disk.localized/Документы/Панченко Валентин Олегович - резюме.pdf"
OUT_LOCAL="$DIR/Панченко_Валентин_Олегович.pdf"
CHROME="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

"$CHROME" \
  --headless=new \
  --disable-gpu \
  --no-pdf-header-footer \
  --print-to-pdf="$OUT_LOCAL" \
  "file://$HTML"

cp "$OUT_LOCAL" "$OUT_YANDEX"
echo "PDF: $OUT_LOCAL"
echo "PDF: $OUT_YANDEX"
