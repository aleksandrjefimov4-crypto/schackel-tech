# schackel-tech

A modern web application built with [Vite](https://vite.dev/), [React](https://react.dev/), and [TypeScript](https://www.typescriptlang.org/).

## Requirements

- Node.js 20+ (developed on Node 22)
- npm 10+

## Getting started

```bash
npm install        # install dependencies
npm run dev        # start the dev server on http://localhost:5173
```

## Available scripts

| Script            | Description                                        |
| ----------------- | -------------------------------------------------- |
| `npm run dev`     | Start the Vite dev server (host `0.0.0.0:5173`).   |
| `npm run build`   | Type-check with `tsc` and build for production.    |
| `npm run preview` | Preview the production build on port `4173`.       |
| `npm run lint`    | Lint the project with ESLint.                      |
| `npm test`        | Run the unit tests once with Vitest.               |

## Project structure

```
.
├── index.html          # App entry HTML
├── src/
│   ├── main.tsx        # React entry point
│   ├── App.tsx         # Root component
│   ├── App.test.tsx    # Component tests (Vitest + Testing Library)
│   └── test/setup.ts   # Test setup (jest-dom matchers)
├── vite.config.ts      # Vite + Vitest configuration
└── .cursor/            # Cloud Agent environment configuration
```
