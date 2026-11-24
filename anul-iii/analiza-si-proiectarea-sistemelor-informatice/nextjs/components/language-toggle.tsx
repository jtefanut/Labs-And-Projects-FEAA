'use client'

import { useRouter, usePathname } from 'next/navigation'
import { useEffect, useState } from 'react'

export function LanguageToggle({ currentLocale }: { currentLocale: 'ro' | 'en' }) {
  const router = useRouter()
  const pathname = usePathname()
  const [mounted, setMounted] = useState(false)

  useEffect(() => {
    setMounted(true)
  }, [])

  if (!mounted) return null

  const other = currentLocale === 'ro' ? 'en' : 'ro'

  const switchLanguage = () => {
    document.cookie = `eloquence_locale=${other}; path=/; max-age=${60 * 60 * 24 * 365}; SameSite=Lax`
    router.refresh()
  }

  return (
    <a
      href="#"
      onClick={(e) => {
        e.preventDefault()
        switchLanguage()
      }}
      aria-label={currentLocale === 'ro' ? 'Switch to English' : 'Schimbă în română'}
      className="text-sm font-medium hover:underline"
    >
      {currentLocale === 'ro' ? 'EN' : 'RO'}
    </a>
  )
}
