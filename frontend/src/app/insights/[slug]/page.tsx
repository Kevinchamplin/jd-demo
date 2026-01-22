import { notFound } from 'next/navigation';
import Link from 'next/link';
import { ArrowLeft, Calendar, User, Sparkles, Clock, Brain } from 'lucide-react';
import {
  graphqlClient,
  GET_INSIGHT_BY_SLUG,
  GET_ALL_INSIGHT_SLUGS,
  type InsightResponse,
  type Insight,
} from '@/lib/graphql-client';

// ISR: Revalidate every 60 seconds
export const revalidate = 60;

// Generate static params for all insights
export async function generateStaticParams() {
  try {
    const data = await graphqlClient.request<{ insights: { nodes: { slug: string }[] } }>(
      GET_ALL_INSIGHT_SLUGS
    );
    return data.insights.nodes.map((insight) => ({
      slug: insight.slug,
    }));
  } catch {
    // Return empty array if WordPress isn't available
    return [
      { slug: 'future-ai-native-software-architecture' },
      { slug: 'quality-without-compromise-testing-ai-era' },
      { slug: 'decoupled-architecture-wordpress-headless-cms' },
    ];
  }
}

// Generate metadata for SEO
export async function generateMetadata({ params }: { params: { slug: string } }) {
  const insight = await getInsight(params.slug);

  if (!insight) {
    return {
      title: 'Insight Not Found',
    };
  }

  return {
    title: insight.title,
    description: insight.aiSummary || insight.excerpt,
  };
}

async function getInsight(slug: string): Promise<Insight | null> {
  try {
    const data = await graphqlClient.request<InsightResponse>(GET_INSIGHT_BY_SLUG, {
      slug,
    });
    return data.insight;
  } catch {
    // Return mock data for demo
    return getMockInsight(slug);
  }
}

export default async function InsightPage({ params }: { params: { slug: string } }) {
  const insight = await getInsight(params.slug);

  if (!insight) {
    notFound();
  }

  const formattedDate = new Date(insight.date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });

  return (
    <article className="min-h-screen py-12">
      <div className="xebia-container">
        {/* Back Link */}
        <Link
          href="/insights"
          className="inline-flex items-center gap-2 text-xebia-gray-500 hover:text-xebia-purple-700 mb-8 transition-colors"
        >
          <ArrowLeft className="w-4 h-4" />
          Back to Insights
        </Link>

        {/* Header */}
        <header className="max-w-4xl mb-12">
          {/* Categories */}
          {insight.insightCategories?.nodes[0] && (
            <div className="mb-4">
              <span className="xebia-badge-purple">
                {insight.insightCategories.nodes[0].name}
              </span>
            </div>
          )}

          {/* Title */}
          <h1 className="text-4xl lg:text-5xl font-bold text-xebia-gray-900 mb-6 text-balance">
            {insight.title}
          </h1>

          {/* Meta */}
          <div className="flex flex-wrap items-center gap-6 text-sm text-xebia-gray-500">
            <span className="flex items-center gap-2">
              <Calendar className="w-4 h-4" />
              {formattedDate}
            </span>
            {insight.author?.node.name && (
              <span className="flex items-center gap-2">
                <User className="w-4 h-4" />
                {insight.author.node.name}
              </span>
            )}
            <span className="flex items-center gap-2">
              <Clock className="w-4 h-4" />
              5 min read
            </span>
          </div>
        </header>

        <div className="grid lg:grid-cols-[1fr,320px] gap-12">
          {/* Main Content */}
          <div>
            {/* AI Summary Box */}
            {insight.aiSummary && (
              <div className="mb-12 p-6 rounded-xl bg-gradient-to-br from-xebia-purple-50 to-white border border-xebia-purple-100">
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 rounded-lg xebia-gradient-bg flex items-center justify-center">
                    <Sparkles className="w-5 h-5 text-white" />
                  </div>
                  <div>
                    <h2 className="font-semibold text-xebia-gray-900">
                      Executive Summary
                    </h2>
                    <span className="text-xs text-xebia-purple-600">
                      AI Generated with GPT-4o
                    </span>
                  </div>
                </div>
                <p className="text-xebia-gray-700 leading-relaxed">
                  {insight.aiSummary}
                </p>

                {/* AI Metadata */}
                {insight.aiSummaryMeta && (
                  <div className="mt-4 pt-4 border-t border-xebia-purple-100">
                    <div className="flex flex-wrap gap-4 text-xs text-xebia-gray-500">
                      {insight.aiSummaryMeta.model && (
                        <span className="flex items-center gap-1">
                          <Brain className="w-3 h-3" />
                          {insight.aiSummaryMeta.model}
                        </span>
                      )}
                      {insight.aiSummaryMeta.tokensUsed && (
                        <span>Tokens: {insight.aiSummaryMeta.tokensUsed}</span>
                      )}
                      {insight.aiSummaryMeta.generatedAt && (
                        <span>
                          Generated:{' '}
                          {new Date(insight.aiSummaryMeta.generatedAt).toLocaleDateString()}
                        </span>
                      )}
                    </div>
                  </div>
                )}
              </div>
            )}

            {/* Article Content */}
            <div
              className="xebia-prose max-w-none"
              dangerouslySetInnerHTML={{ __html: insight.content || '' }}
            />
          </div>

          {/* Sidebar */}
          <aside className="hidden lg:block">
            <div className="sticky top-24 space-y-6">
              {/* Author Card */}
              {insight.author?.node && (
                <div className="xebia-card p-6">
                  <h3 className="text-sm font-semibold text-xebia-gray-500 uppercase tracking-wide mb-4">
                    Author
                  </h3>
                  <div className="flex items-center gap-3">
                    <div className="w-12 h-12 rounded-full bg-xebia-purple-100 flex items-center justify-center text-xebia-purple-600 font-semibold">
                      {insight.author.node.name.charAt(0)}
                    </div>
                    <div>
                      <p className="font-medium text-xebia-gray-900">
                        {insight.author.node.name}
                      </p>
                      <p className="text-sm text-xebia-gray-500">
                        Software Craftsman
                      </p>
                    </div>
                  </div>
                </div>
              )}

              {/* Related Topics */}
              <div className="xebia-card p-6">
                <h3 className="text-sm font-semibold text-xebia-gray-500 uppercase tracking-wide mb-4">
                  Topics
                </h3>
                <div className="flex flex-wrap gap-2">
                  {insight.insightCategories?.nodes.map((category) => (
                    <span
                      key={category.id}
                      className="px-3 py-1 bg-xebia-gray-100 text-xebia-gray-700 rounded-full text-sm"
                    >
                      {category.name}
                    </span>
                  ))}
                  <span className="px-3 py-1 bg-xebia-gray-100 text-xebia-gray-700 rounded-full text-sm">
                    AI
                  </span>
                  <span className="px-3 py-1 bg-xebia-gray-100 text-xebia-gray-700 rounded-full text-sm">
                    Architecture
                  </span>
                </div>
              </div>
            </div>
          </aside>
        </div>
      </div>
    </article>
  );
}

// Mock data function
function getMockInsight(slug: string): Insight | null {
  const mockInsights: Record<string, Insight> = {
    'future-ai-native-software-architecture': {
      id: '1',
      databaseId: 1,
      title: 'The Future of AI-Native Software Architecture',
      slug: 'future-ai-native-software-architecture',
      content: `
        <p>As we enter 2026, the integration of AI into software systems has moved from experimental to essential. This article explores how forward-thinking organizations are rethinking their architecture to be truly AI-native.</p>

        <h2>The Paradigm Shift</h2>
        <p>Traditional software architecture treats AI as an external service—a bolt-on capability. AI-native architecture, by contrast, designs systems from the ground up with AI as a first-class citizen. This means considering AI capabilities in every architectural decision, from data flow to user experience.</p>

        <h2>Key Principles</h2>
        <p>1. <strong>Observable AI:</strong> Every AI interaction should be traceable and debuggable. This is why the Xebia Authority Hub includes full traceability for all OpenAI API calls.</p>
        <p>2. <strong>Graceful Degradation:</strong> Systems should function even when AI services are unavailable. Content should be valuable with or without AI enhancement.</p>
        <p>3. <strong>Human in the Loop:</strong> AI augments human capabilities rather than replacing them. Editorial control remains paramount.</p>

        <h2>Implementation Patterns</h2>
        <p>The Authority Hub demonstrates several patterns for AI integration:</p>
        <ul>
          <li>Service abstraction with dependency injection</li>
          <li>Asynchronous generation with status feedback</li>
          <li>Structured metadata storage for AI outputs</li>
          <li>GraphQL mutations for on-demand generation</li>
        </ul>

        <h2>Looking Ahead</h2>
        <p>As AI capabilities continue to evolve, architectures must be flexible enough to incorporate new models and features. The decoupled approach demonstrated here allows for easy upgrades and experimentation without disrupting the core system.</p>
      `,
      excerpt: 'Exploring how AI is fundamentally changing how we design and build software systems.',
      date: new Date().toISOString(),
      aiSummary:
        'This insight explores the paradigm shift in software architecture driven by AI integration. Key takeaways include the importance of designing for AI-first workflows, implementing proper observability for AI components, and maintaining human oversight in critical decision paths. The article demonstrates practical implementation patterns through the Xebia Authority Hub example.',
      hasAISummary: true,
      aiSummaryMeta: {
        model: 'gpt-4o-2024-01-01',
        generatedAt: new Date().toISOString(),
        contentLength: 2156,
        summaryLength: 312,
        tokensUsed: 423,
        finishReason: 'stop',
      },
      insightCategories: {
        nodes: [{ id: 'cat1', name: 'Architecture', slug: 'architecture' }],
      },
      author: {
        node: { name: 'Kevin Champlin' },
      },
    },
  };

  return mockInsights[slug] || mockInsights['future-ai-native-software-architecture'];
}
