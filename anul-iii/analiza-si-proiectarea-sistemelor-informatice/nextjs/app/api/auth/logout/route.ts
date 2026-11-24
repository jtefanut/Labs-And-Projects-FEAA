import { NextResponse } from 'next/server'
import { destroySession, clearSessionCookie } from '@/lib/auth-cookies'

export async function POST() {
  await destroySession()
  clearSessionCookie()
  return NextResponse.json({ ok: true })
}
