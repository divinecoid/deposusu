<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="google" content="notranslate">

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="{{ asset('css/flux.css') }}">
    <style type="text/css">
        @theme {
            --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';

            --color-zinc-50: #fafafa;
            --color-zinc-100: #f5f5f5;
            --color-zinc-200: #e5e5e5;
            --color-zinc-300: #d4d4d4;
            --color-zinc-400: #a3a3a3;
            --color-zinc-500: #737373;
            --color-zinc-600: #525252;
            --color-zinc-700: #404040;
            --color-zinc-800: #262626;
            --color-zinc-900: #171717;
            --color-zinc-950: #0a0a0a;

            --color-accent: var(--color-neutral-800);
            --color-accent-content: var(--color-neutral-800);
            --color-accent-foreground: var(--color-white);
        }

        .dark {
            --color-accent: var(--color-white);
            --color-accent-content: var(--color-white);
            --color-accent-foreground: var(--color-neutral-800);
        }

        *,
        ::after,
        ::before,
        ::backdrop,
        ::file-selector-button {
            border-color: var(--color-gray-200, currentColor);
        }

        [data-flux-field]:not(ui-radio, ui-checkbox) {
            display: grid;
            gap: 0.5rem;
        }

        [data-flux-label] {
            margin-bottom: 0px !important;
            line-height: 1.25 !important;
        }

        input:focus[data-flux-control],
        textarea:focus[data-flux-control],
        select:focus[data-flux-control] {
            outline: 2px solid transparent;
            outline-offset: 2px;
            --tw-ring-overflow-shadow: 0 0 0 2px var(--color-accent);
            box-shadow: 0 0 0 2px var(--color-accent);
        }
    </style>
@fluxAppearance
