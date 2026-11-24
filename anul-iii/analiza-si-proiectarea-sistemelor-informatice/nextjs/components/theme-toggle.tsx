'use client'

import { useEffect, useState } from 'react'
import { Sun, Moon } from 'lucide-react'

export function ThemeToggle() {
  const [dark, setDark] = useState(false)
  const [mounted, setMounted] = useState(false)

  useEffect(() => {
    setMounted(true)
    const stored = localStorage.getItem('eloquence_theme')
    const systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches
    setDark(stored ? stored === 'dark' : systemDark)
  }, [])

  if (!mounted) return null

  const toggle = () => {
    const next = !dark
    setDark(next)
    localStorage.setItem('eloquence_theme', next ? 'dark' : 'light')
    document.documentElement.classList.toggle('dark', next)
  }

  useEffect(() => {
    document.documentElement.classList.toggle('dark', dark)
  }, [dark])

  return (
    <button
      onClick={toggle}
      aria-label={dark ? 'Schimbă în mod clar' : 'Schimbă în mod întunecat'}
      aria-pressed={dark}
      className="p-2 rounded-md hover:bg-accent hover:text-accent-foreground transition-colors"
    >
      {dark ? <Sun size={16} /> : <Moon size={16} />}
    </button>
  )
}
