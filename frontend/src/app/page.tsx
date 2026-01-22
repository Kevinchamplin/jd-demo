import { Suspense } from 'react';
import Link from 'next/link';
import {
  Sparkles,
  Code2,
  Shield,
  Zap,
  ArrowRight,
  GitBranch,
} from 'lucide-react';
import { InsightsList } from '@/components/insights/InsightsList';

export const revalidate = 60; // ISR: Revalidate every 60 seconds

export default function HomePage() {
  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="relative overflow-hidden">
        {/* Background gradient */}
        <div className="absolute inset-0 xebia-gradient-bg opacity-[0.03]" />
        <div className="absolute top-20 right-20 w-96 h-96 bg-xebia-purple-400 rounded-full blur-3xl opacity-10" />
        <div className="absolute bottom-20 left-20 w-96 h-96 bg-xebia-purple-600 rounded-full blur-3xl opacity-10" />

        <div className="xebia-container relative py-24 lg:py-32">
          <div className="max-w-4xl mx-auto text-center">
            {/* Badge */}
            <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-xebia-purple-100 text-xebia-purple-800 text-sm font-medium mb-8 animate-fade-in">
              <Sparkles className="w-4 h-4" />
              <span>AI-Native Architecture Showcase</span>
            </div>

            {/* Headline */}
            <h1 className="text-5xl lg:text-7xl font-bold text-xebia-gray-900 mb-6 animate-slide-up">
              The Xebia
              <span className="xebia-gradient-text block">Authority Hub</span>
            </h1>

            {/* Subheadline */}
            <p className="text-xl lg:text-2xl text-xebia-gray-600 mb-10 max-w-3xl mx-auto leading-relaxed animate-slide-up">
              Demonstrating{' '}
              <strong className="text-xebia-purple-700">
                Quality without Compromise
              </strong>{' '}
              and{' '}
              <strong className="text-xebia-purple-700">
                Sharing Knowledge
              </strong>{' '}
              through modern software craftsmanship.
            </p>

            {/* CTA Buttons */}
            <div className="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up">
              <Link
                href="/insights"
                className="xebia-btn-primary px-8 py-4 text-lg"
              >
                Explore Insights
                <ArrowRight className="w-5 h-5" />
              </Link>
              <a
                href="#architecture"
                className="xebia-btn-secondary px-8 py-4 text-lg"
              >
                View Architecture
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Architecture Overview */}
      <section id="architecture" className="py-24 bg-xebia-gray-50">
        <div className="xebia-container">
          <div className="text-center mb-16">
            <h2 className="text-3xl lg:text-4xl font-bold text-xebia-gray-900 mb-4">
              Modern Architecture
            </h2>
            <p className="text-lg text-xebia-gray-600 max-w-2xl mx-auto">
              A decoupled architecture built for the AI era, combining the best
              of headless WordPress and modern frontend frameworks.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <ArchitectureCard
              icon={<GitBranch className="w-8 h-8" />}
              title="Decoupled"
              description="Headless WordPress with Roots Bedrock for professional dependency management and security."
            />
            <ArchitectureCard
              icon={<Code2 className="w-8 h-8" />}
              title="Type-Safe"
              description="TypeScript throughout, PSR-12 PHP standards, and strict typing for reliability."
            />
            <ArchitectureCard
              icon={<Sparkles className="w-8 h-8" />}
              title="AI-Native"
              description="OpenAI GPT-4o integration for intelligent content generation with full traceability."
            />
            <ArchitectureCard
              icon={<Zap className="w-8 h-8" />}
              title="Performance"
              description="ISR for sub-second speeds, optimized GraphQL queries, and edge-ready deployment."
            />
          </div>
        </div>
      </section>

      {/* Latest Insights */}
      <section className="py-24">
        <div className="xebia-container">
          <div className="flex items-center justify-between mb-12">
            <div>
              <h2 className="text-3xl font-bold text-xebia-gray-900 mb-2">
                Latest Insights
              </h2>
              <p className="text-xebia-gray-600">
                AI-enhanced content showcasing thought leadership
              </p>
            </div>
            <Link
              href="/insights"
              className="hidden sm:flex items-center gap-2 text-xebia-purple-700 hover:text-xebia-purple-900 font-medium transition-colors"
            >
              View all insights
              <ArrowRight className="w-4 h-4" />
            </Link>
          </div>

          <Suspense fallback={<InsightsSkeleton />}>
            <InsightsList limit={3} />
          </Suspense>
        </div>
      </section>

      {/* Tech Stack */}
      <section className="py-24 xebia-gradient-bg text-white">
        <div className="xebia-container">
          <div className="text-center mb-16">
            <h2 className="text-3xl lg:text-4xl font-bold mb-4">
              Built With Authority
            </h2>
            <p className="text-lg text-xebia-purple-200 max-w-2xl mx-auto">
              Enterprise-grade technologies demonstrating 2026 AI-era best
              practices.
            </p>
          </div>

          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            <TechItem name="Next.js 14" description="App Router + ISR" />
            <TechItem name="WordPress" description="Roots Bedrock" />
            <TechItem name="WPGraphQL" description="Type-safe API" />
            <TechItem name="OpenAI GPT-4o" description="AI Generation" />
            <TechItem name="TypeScript" description="Full Type Safety" />
            <TechItem name="Tailwind CSS" description="Utility-first" />
            <TechItem name="React Query" description="Server State" />
            <TechItem name="Vitest + Pest" description="Testing" />
          </div>
        </div>
      </section>

      {/* Security Section */}
      <section className="py-24 bg-xebia-gray-50">
        <div className="xebia-container">
          <div className="max-w-3xl mx-auto text-center">
            <Shield className="w-16 h-16 text-xebia-purple-600 mx-auto mb-6" />
            <h2 className="text-3xl font-bold text-xebia-gray-900 mb-4">
              Security First
            </h2>
            <p className="text-lg text-xebia-gray-600 mb-8">
              Every layer of this application has been built with security as a
              core principle, not an afterthought.
            </p>
            <div className="grid sm:grid-cols-2 gap-4 text-left">
              <SecurityItem text="Nonce verification on all API endpoints" />
              <SecurityItem text="Sanitized GraphQL queries" />
              <SecurityItem text="Bedrock environment isolation" />
              <SecurityItem text="HTTPS enforced in production" />
              <SecurityItem text="XSS protection headers" />
              <SecurityItem text="CSRF protection built-in" />
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}

function ArchitectureCard({
  icon,
  title,
  description,
}: {
  icon: React.ReactNode;
  title: string;
  description: string;
}) {
  return (
    <div className="xebia-card p-8 text-center">
      <div className="w-16 h-16 rounded-xl bg-xebia-purple-100 text-xebia-purple-600 flex items-center justify-center mx-auto mb-6">
        {icon}
      </div>
      <h3 className="text-xl font-semibold text-xebia-gray-900 mb-3">
        {title}
      </h3>
      <p className="text-xebia-gray-600">{description}</p>
    </div>
  );
}

function TechItem({
  name,
  description,
}: {
  name: string;
  description: string;
}) {
  return (
    <div className="text-center">
      <div className="text-xl font-semibold mb-1">{name}</div>
      <div className="text-sm text-xebia-purple-200">{description}</div>
    </div>
  );
}

function SecurityItem({ text }: { text: string }) {
  return (
    <div className="flex items-center gap-3 p-3 bg-white rounded-lg border border-xebia-gray-200">
      <Shield className="w-5 h-5 text-xebia-purple-600 flex-shrink-0" />
      <span className="text-sm text-xebia-gray-700">{text}</span>
    </div>
  );
}

function InsightsSkeleton() {
  return (
    <div className="grid md:grid-cols-3 gap-8">
      {[1, 2, 3].map((i) => (
        <div key={i} className="xebia-card">
          <div className="h-48 skeleton" />
          <div className="p-6 space-y-4">
            <div className="h-6 skeleton rounded w-3/4" />
            <div className="space-y-2">
              <div className="h-4 skeleton rounded" />
              <div className="h-4 skeleton rounded w-5/6" />
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
