#!/usr/bin/env python3
import argparse
import base64
import json
import re
import sys
from pathlib import Path
from typing import Dict, List, Optional, Tuple
from urllib.error import HTTPError, URLError
from urllib.parse import urlencode, urljoin
from urllib.request import Request, urlopen


def build_auth_header(username: Optional[str], app_password: Optional[str]) -> Optional[str]:
    if not username or not app_password:
        return None
    token = base64.b64encode(f"{username}:{app_password}".encode("utf-8")).decode("ascii")
    return f"Basic {token}"


def request_json(url: str, auth_header: Optional[str]) -> Tuple[List[Dict], Dict[str, str]]:
    request = Request(url)
    request.add_header("Accept", "application/json")
    if auth_header:
        request.add_header("Authorization", auth_header)

    with urlopen(request, timeout=60) as response:
        body = response.read().decode("utf-8")
        headers = {k.lower(): v for k, v in response.headers.items()}
        return json.loads(body), headers


def safe_filename(value: str) -> str:
    value = value.strip().lower()
    value = re.sub(r"[^a-z0-9_-]+", "-", value)
    value = re.sub(r"-{2,}", "-", value).strip("-")
    return value or "post"


def export_posts(
    base_url: str,
    output_dir: Path,
    status: str,
    per_page: int,
    auth_header: Optional[str],
) -> int:
    output_dir.mkdir(parents=True, exist_ok=True)
    posts_dir = output_dir / "posts"
    posts_dir.mkdir(parents=True, exist_ok=True)

    page = 1
    total_written = 0
    index_items: List[Dict] = []

    while True:
        query = {
            "per_page": per_page,
            "page": page,
            "_embed": 1,
            "orderby": "date",
            "order": "asc",
        }
        if status != "all":
            query["status"] = status

        if auth_header:
            query["context"] = "edit"

        endpoint = f"wp-json/wp/v2/posts?{urlencode(query)}"
        url = urljoin(base_url.rstrip("/") + "/", endpoint)

        try:
            items, headers = request_json(url, auth_header)
        except HTTPError as exc:
            if exc.code == 400 and page > 1:
                break
            if exc.code == 401:
                raise RuntimeError(
                    "Нет доступа к API. Для приватных/черновиков укажите WP_EXPORT_USER и WP_EXPORT_APP_PASSWORD."
                ) from exc
            raise RuntimeError(f"Ошибка HTTP {exc.code} при запросе {url}") from exc
        except URLError as exc:
            raise RuntimeError(f"Не удалось подключиться к {url}: {exc}") from exc

        if not items:
            break

        total_pages = int(headers.get("x-wp-totalpages", "1"))
        total_posts = int(headers.get("x-wp-total", "0"))
        print(f"[page {page}/{total_pages}] получено {len(items)} статей (всего: {total_posts})")

        for post in items:
            post_id = post.get("id")
            slug = safe_filename(post.get("slug", "post"))
            date = str(post.get("date", ""))[:10]
            filename = f"{date}_{slug}_{post_id}.json" if date else f"{slug}_{post_id}.json"
            filepath = posts_dir / filename

            with filepath.open("w", encoding="utf-8") as f:
                json.dump(post, f, ensure_ascii=False, indent=2)
                f.write("\n")

            index_items.append(
                {
                    "id": post_id,
                    "slug": post.get("slug"),
                    "status": post.get("status"),
                    "date": post.get("date"),
                    "title": (post.get("title") or {}).get("rendered"),
                    "file": str(Path("posts") / filename),
                }
            )
            total_written += 1

        if page >= total_pages:
            break
        page += 1

    index_path = output_dir / "index.json"
    with index_path.open("w", encoding="utf-8") as f:
        json.dump(index_items, f, ensure_ascii=False, indent=2)
        f.write("\n")

    return total_written


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(
        description="Экспорт статей WordPress в JSON-файлы для последующей генерации/синхронизации."
    )
    parser.add_argument("--base-url", required=True, help="Базовый URL сайта WordPress, например https://example.com/")
    parser.add_argument("--output-dir", required=True, help="Папка, куда сохранять экспорт")
    parser.add_argument(
        "--status",
        default="publish",
        choices=["publish", "draft", "private", "future", "pending", "all"],
        help="Какие статьи выгружать (по умолчанию: publish)",
    )
    parser.add_argument("--per-page", type=int, default=100, help="Количество статей на страницу API (по умолчанию: 100)")
    parser.add_argument("--username", default=None, help="Логин WordPress (для приватных/черновиков)")
    parser.add_argument("--app-password", default=None, help="Application Password WordPress")
    return parser.parse_args()


def main() -> int:
    args = parse_args()
    auth_header = build_auth_header(args.username, args.app_password)

    try:
        written = export_posts(
            base_url=args.base_url,
            output_dir=Path(args.output_dir),
            status=args.status,
            per_page=args.per_page,
            auth_header=auth_header,
        )
    except RuntimeError as exc:
        print(f"Ошибка: {exc}", file=sys.stderr)
        return 1

    print(f"Готово. Выгружено файлов: {written}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
