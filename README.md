# circolodelre.it

Sito ufficiale dell'**A.S.D. Circolo del Re** — club di scacchi di Castelvetrano (TP).

## Architettura

Il progetto utilizza un sistema **dual-mode**:

- **Sviluppo locale**: server PHP embedded con routing dinamico
- **Produzione**: sito statico pre-generato deployato su GitHub Pages (`/docs`)

```
src/
├── app/           # Classi PHP (Events, GrandPrix, Functions, Services, System)
├── config.json    # Configurazione globale
├── events/        # Dati eventi (JSON)
├── pages/         # Controller delle pagine (index, events, grandprix)
├── seasons/       # Dati tornei Grand Prix per anno (formato Vega Chess)
│   └── YYYY/
│       ├── N.txt  # Risultati torneo (formato Vega Chess)
│       └── N.pts  # Punti Grand Prix per giocatore
├── services/      # Service providers (config, twig)
├── tasks/         # Script CLI (build, download-events)
├── views/         # Template Twig
│   ├── partials/  # Componenti riutilizzabili
│   └── blocks/    # Blocchi generici
└── router.php     # Entry point sviluppo locale
docs/              # Output statico (GitHub Pages)
```

## Sviluppo locale

```bash
make serve         # Build immagine Docker e avvia PHP built-in server su :8080
```

Il server è accessibile su `http://localhost:8080`. Le modifiche ai file PHP e Twig sono riflesse immediatamente senza rebuild.

## Build e deploy

```bash
make build         # Genera tutti i file HTML statici in /docs
make download-events  # Scarica i flyer PDF degli eventi da Google Docs
make release       # build + download-events + git push
```

La build genera:
- `docs/index.html` — homepage
- `docs/grandprix/YYYY/index.html` — classifica Grand Prix per stagione
- `docs/eventi/YYYY/slug.html` — pagina di ogni evento
- `docs/eventi/YYYY/slug.pdf` — flyer PDF di ogni evento

## Dipendenze

```bash
make install       # composer install dentro Docker
make update        # composer update dentro Docker
```

Dipendenze PHP: `twig/twig ^2.0`, `webmozart/glob ^4.1`

## Sistema eventi

Gli eventi sono definiti in `src/events/events.json`:

```json
{
  "events": [
    {
      "title": "Torneo #251 - Rapid 25'",
      "date": "2026-03-28",
      "flyer": "https://docs.google.com/document/d/.../export?format=pdf",
      "type": "tournament",
      "vesus": "https://vesus.org/tournament/..."
    }
  ]
}
```

## Sistema Grand Prix

I dati dei tornei Grand Prix sono in `src/seasons/YYYY/`:

- `N.txt` — classifica del torneo in formato Vega Chess
- `N.pts` — assegnazione punti GP (`NomeGiocatore punti` per riga)

La classifica annuale è accessibile su `/grandprix/YYYY/`. L'ordinamento usa i **punti GP** come criterio primario e il **Perf%** (punti torneo / punti massimi possibili) come spareggio.

## CI/CD

GitHub Actions (`.github/workflows/sync-events.yml`) esegue automaticamente `download-events` + `build` ogni lunedì e giovedì alle 4:00 UTC.

Per il push manuale è necessaria la variabile d'ambiente `GITHUB_TOKEN`:

```bash
export GITHUB_TOKEN=ghp_...
make push
```
