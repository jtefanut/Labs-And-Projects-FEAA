/** @type {import('next').NextConfig} */
const nextConfig = {
  output: 'standalone',
  serverComponentsExternalPackages: ['better-sqlite3'],
  experimental: {
    serverActions: {
      bodySizeLimit: '2mb',
    },
  },
}

export default nextConfig
