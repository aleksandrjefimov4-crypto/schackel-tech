import { useState } from 'react'
import './App.css'

const FEATURES = [
  {
    title: 'Fast by default',
    body: 'Vite-powered dev server with instant hot module replacement.',
  },
  {
    title: 'Typed end to end',
    body: 'TypeScript across the app for confident refactors.',
  },
  {
    title: 'Test ready',
    body: 'Vitest and Testing Library wired up out of the box.',
  },
]

function App() {
  const [count, setCount] = useState(0)

  return (
    <main className="app">
      <header className="hero">
        <span className="badge">schackel-tech</span>
        <h1>Build. Ship. Iterate.</h1>
        <p className="tagline">
          A modern React + TypeScript starter, ready for your next idea.
        </p>
        <button className="counter" onClick={() => setCount((c) => c + 1)}>
          You clicked {count} {count === 1 ? 'time' : 'times'}
        </button>
      </header>

      <section className="features" aria-label="Features">
        {FEATURES.map((feature) => (
          <article className="feature-card" key={feature.title}>
            <h2>{feature.title}</h2>
            <p>{feature.body}</p>
          </article>
        ))}
      </section>

      <footer className="footer">
        <p>Edit <code>src/App.tsx</code> and save to reload.</p>
      </footer>
    </main>
  )
}

export default App
