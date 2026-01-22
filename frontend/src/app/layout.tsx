import type { Metadata } from 'next';
import { Inter } from 'next/font/google';
import './globals.css';
import { Providers } from '@/components/Providers';
import { Header } from '@/components/ui/Header';
import { DevConsole } from '@/components/dashboard/DevConsole';

const inter = Inter({
  subsets: ['latin'],
  display: 'swap',
  variable: '--font-inter',
});

export const metadata: Metadata = {
  title: {
    default: 'Xebia Authority Hub',
    template: '%s | Xebia Authority Hub',
  },
  description:
    'AI-powered insights platform demonstrating Quality without Compromise and Sharing Knowledge through modern software craftsmanship.',
  keywords: [
    'Xebia',
    'Authority',
    'AI',
    'Software Engineering',
    'Next.js',
    'WordPress',
    'GraphQL',
  ],
  authors: [{ name: 'Kevin Champlin' }],
  openGraph: {
    type: 'website',
    locale: 'en_US',
    siteName: 'Xebia Authority Hub',
  },
  robots: {
    index: true,
    follow: true,
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={inter.variable}>
      <body className="min-h-screen bg-white">
        <Providers>
          <Header />
          <main className="relative">{children}</main>
          <DevConsole />
        </Providers>
      </body>
    </html>
  );
}
