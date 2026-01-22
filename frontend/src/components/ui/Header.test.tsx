import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import { Header } from './Header';

describe('Header', () => {
  it('renders the Xebia Authority Hub logo', () => {
    render(<Header />);
    expect(screen.getByText('Xebia')).toBeInTheDocument();
    expect(screen.getByText('Authority Hub')).toBeInTheDocument();
  });

  it('renders navigation links', () => {
    render(<Header />);
    expect(screen.getByRole('link', { name: /home/i })).toBeInTheDocument();
    expect(screen.getByRole('link', { name: /insights/i })).toBeInTheDocument();
  });

  it('displays AI-Powered badge', () => {
    render(<Header />);
    expect(screen.getByText('AI-Powered')).toBeInTheDocument();
  });

  it('has correct link hrefs', () => {
    render(<Header />);
    const homeLink = screen.getByRole('link', { name: /home/i });
    const insightsLink = screen.getByRole('link', { name: /insights/i });

    expect(homeLink).toHaveAttribute('href', '/');
    expect(insightsLink).toHaveAttribute('href', '/insights');
  });
});
