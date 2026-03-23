# buchversteher.de — Child Theme

Strukturiertes WordPress Child Theme für buchversteher.de.

## Struktur

```
├── functions.php        — Theme Loader (Parent CSS + Module laden)
├── inc/
│   ├── meta.php         — REST API Book Fields + average_book_rating
│   ├── rest-endpoints.php — Custom Endpoints (set-review-meta, set-linked-book)
│   ├── shortcodes.php   — [book_details] + [buchrezensionen] Shortcodes
│   ├── post-types.php   — Book → /buchseite/ Redirects
│   └── frontend.php     — JavaScript (Suchformular, Select2, Toggle)
└── README.md
```

## Installation

1. Child Theme Ordner auf Server kopieren (`/wp-content/themes/book-review-blog-child/`)
2. functions.php prüfen (Lade-Pfade sicherstellen)
3. im WordPress Admin aktivieren

## Neues Feature hinzufügen

1. Bestehende `inc/`-Datei bearbeiten ODER neue Datei erstellen
2. `require_once` in functions.php hinzufügen
3. Testen

## Technische Details

- **Plugin:** RS WP Books Showcase
- **Review CPT:** `book_reviews` (Meta-Keys: `_rswpbs_*`)
- **Book CPT:** `book`
- **Custom Endpoints:** `/wp-json/buchversteher/v1/`
- **Shortcodes:** `[book_details]`, `[buchrezensionen]`
