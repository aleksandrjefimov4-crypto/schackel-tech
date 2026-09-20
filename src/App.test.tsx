import { render, screen } from '@testing-library/react'
import userEvent from '@testing-library/user-event'
import { describe, expect, it } from 'vitest'
import App from './App'

describe('App', () => {
  it('renders the hero heading', () => {
    render(<App />)
    expect(
      screen.getByRole('heading', { name: /build\. ship\. iterate\./i }),
    ).toBeInTheDocument()
  })

  it('increments the counter when clicked', async () => {
    const user = userEvent.setup()
    render(<App />)

    const button = screen.getByRole('button', { name: /you clicked 0 times/i })
    await user.click(button)

    expect(
      screen.getByRole('button', { name: /you clicked 1 time/i }),
    ).toBeInTheDocument()
  })

  it('shows all feature cards', () => {
    render(<App />)
    expect(screen.getAllByRole('article')).toHaveLength(3)
  })
})
