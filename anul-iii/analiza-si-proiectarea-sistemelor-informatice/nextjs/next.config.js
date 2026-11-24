/** @type {import('next').NextConfig} */
const nextConfig = {
  output: process.env.NEXT_OUTPUT === 'export' ? 'export' : 'standalone',
  basePath: process.env.NEXT_OUTPUT === 'export' ? '/Labs-And-Projects-FEAA' : '',
  assetPrefix: process.env.NEXT_OUTPUT === 'export' ? '/Labs-And-Projects-FEAA/' : '',
  trailingSlash: true,
  images: {
    unoptimized: true,
  },
  experimental: {
    serverActions: {
      bodySizeLimit: '2mb',
    },
  },
}

export default nextConfig
