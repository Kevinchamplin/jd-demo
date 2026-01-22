'use client';

import Link from 'next/link';
import { useState } from 'react';
import { Menu, X, Sparkles } from 'lucide-react';

export function Header() {
  const [isMenuOpen, setIsMenuOpen] = useState(false);

  return (
    <header className="sticky top-0 z-50 glass border-b border-xebia-gray-200">
      <nav className="xebia-container">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-3 group">
            <div className="w-10 h-10 rounded-lg xebia-gradient-bg flex items-center justify-center shadow-xebia group-hover:shadow-xebia-lg transition-shadow">
              <Sparkles className="w-5 h-5 text-white" />
            </div>
            <div>
              <span className="text-lg font-bold text-xebia-gray-900">
                Xebia
              </span>
              <span className="text-lg font-light text-xebia-purple-700 ml-1">
                Authority Hub
              </span>
            </div>
          </Link>

          {/* Desktop Navigation */}
          <div className="hidden md:flex items-center gap-8">
            <NavLink href="/">Home</NavLink>
            <NavLink href="/insights">Insights</NavLink>
            <NavLink href="#dashboard">Dashboard</NavLink>
          </div>

          {/* CTA Button */}
          <div className="hidden md:block">
            <span className="xebia-badge-ai">
              <Sparkles className="w-3 h-3 mr-1" />
              AI-Powered
            </span>
          </div>

          {/* Mobile Menu Button */}
          <button
            className="md:hidden p-2 rounded-lg hover:bg-xebia-gray-100 transition-colors"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
            aria-label="Toggle menu"
          >
            {isMenuOpen ? (
              <X className="w-6 h-6 text-xebia-gray-700" />
            ) : (
              <Menu className="w-6 h-6 text-xebia-gray-700" />
            )}
          </button>
        </div>

        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="md:hidden py-4 border-t border-xebia-gray-200 animate-slide-down">
            <div className="flex flex-col gap-2">
              <MobileNavLink href="/" onClick={() => setIsMenuOpen(false)}>
                Home
              </MobileNavLink>
              <MobileNavLink
                href="/insights"
                onClick={() => setIsMenuOpen(false)}
              >
                Insights
              </MobileNavLink>
              <MobileNavLink
                href="#dashboard"
                onClick={() => setIsMenuOpen(false)}
              >
                Dashboard
              </MobileNavLink>
            </div>
          </div>
        )}
      </nav>
    </header>
  );
}

function NavLink({
  href,
  children,
}: {
  href: string;
  children: React.ReactNode;
}) {
  return (
    <Link
      href={href}
      className="text-sm font-medium text-xebia-gray-600 hover:text-xebia-purple-700 transition-colors relative after:absolute after:left-0 after:bottom-0 after:h-0.5 after:w-0 after:bg-xebia-purple-600 after:transition-all hover:after:w-full"
    >
      {children}
    </Link>
  );
}

function MobileNavLink({
  href,
  children,
  onClick,
}: {
  href: string;
  children: React.ReactNode;
  onClick: () => void;
}) {
  return (
    <Link
      href={href}
      onClick={onClick}
      className="block px-4 py-2 text-base font-medium text-xebia-gray-700 hover:text-xebia-purple-700 hover:bg-xebia-purple-50 rounded-lg transition-colors"
    >
      {children}
    </Link>
  );
}
