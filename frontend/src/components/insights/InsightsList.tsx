import Link from 'next/link';
import { Calendar, User, Sparkles } from 'lucide-react';
import {
  graphqlClient,
  GET_INSIGHTS,
  type InsightsResponse,
  type Insight,
} from '@/lib/graphql-client';

interface InsightsListProps {
  limit?: number;
}

async function getInsights(limit: number): Promise<Insight[]> {
  try {
    const data = await graphqlClient.request<InsightsResponse>(GET_INSIGHTS, {
      first: limit,
    });
    return data.insights.nodes;
  } catch (error) {
    console.error('Error fetching insights:', error);
    // Return mock data for demo when WordPress isn't available
    return getMockInsights(limit);
  }
}

export async function InsightsList({ limit = 10 }: InsightsListProps) {
  const insights = await getInsights(limit);

  if (insights.length === 0) {
    return (
      <div className="text-center py-12">
        <p className="text-xebia-gray-500">No insights found.</p>
      </div>
    );
  }

  return (
    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
      {insights.map((insight) => (
        <InsightCard key={insight.id} insight={insight} />
      ))}
    </div>
  );
}

function InsightCard({ insight }: { insight: Insight }) {
  const formattedDate = new Date(insight.date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });

  return (
    <article className="xebia-card group">
      {/* Featured Image */}
      <div className="relative h-48 bg-gradient-to-br from-xebia-purple-600 to-xebia-purple-800 overflow-hidden">
        {insight.featuredImage?.node.sourceUrl ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={insight.featuredImage.node.sourceUrl}
            alt={insight.featuredImage.node.altText || insight.title}
            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
          />
        ) : (
          <div className="w-full h-full flex items-center justify-center">
            <div className="text-white/20 text-6xl font-bold">X</div>
          </div>
        )}

        {/* AI Badge */}
        {insight.hasAISummary && (
          <div className="absolute top-4 right-4">
            <span className="xebia-badge-ai">
              <Sparkles className="w-3 h-3 mr-1" />
              AI Summary
            </span>
          </div>
        )}

        {/* Category */}
        {insight.insightCategories?.nodes[0] && (
          <div className="absolute bottom-4 left-4">
            <span className="px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-medium text-xebia-gray-700">
              {insight.insightCategories.nodes[0].name}
            </span>
          </div>
        )}
      </div>

      {/* Content */}
      <div className="p-6">
        <h3 className="text-xl font-semibold text-xebia-gray-900 mb-3 line-clamp-2 group-hover:text-xebia-purple-700 transition-colors">
          <Link href={`/insights/${insight.slug}`}>{insight.title}</Link>
        </h3>

        {/* AI Summary or Excerpt */}
        <p className="text-xebia-gray-600 text-sm mb-4 line-clamp-3">
          {insight.aiSummary || stripHtml(insight.excerpt || '')}
        </p>

        {/* Meta */}
        <div className="flex items-center justify-between text-xs text-xebia-gray-500">
          <div className="flex items-center gap-4">
            <span className="flex items-center gap-1">
              <Calendar className="w-3.5 h-3.5" />
              {formattedDate}
            </span>
            {insight.author?.node.name && (
              <span className="flex items-center gap-1">
                <User className="w-3.5 h-3.5" />
                {insight.author.node.name}
              </span>
            )}
          </div>
        </div>
      </div>
    </article>
  );
}

function stripHtml(html: string): string {
  return html.replace(/<[^>]*>/g, '').trim();
}

// Mock data for demo purposes when WordPress isn't running
function getMockInsights(limit: number): Insight[] {
  const mockInsights: Insight[] = [
    {
      id: '1',
      databaseId: 1,
      title: 'The Future of AI-Native Software Architecture',
      slug: 'future-ai-native-software-architecture',
      excerpt:
        'Exploring how AI is fundamentally changing how we design and build software systems.',
      date: new Date().toISOString(),
      aiSummary:
        'This insight explores the paradigm shift in software architecture driven by AI integration. Key takeaways include the importance of designing for AI-first workflows, implementing proper observability for AI components, and maintaining human oversight in critical decision paths.',
      hasAISummary: true,
      insightCategories: {
        nodes: [{ id: 'cat1', name: 'Architecture', slug: 'architecture' }],
      },
      author: {
        node: { name: 'Kevin Champlin' },
      },
    },
    {
      id: '2',
      databaseId: 2,
      title: 'Quality Without Compromise: Testing in the AI Era',
      slug: 'quality-without-compromise-testing-ai-era',
      excerpt:
        'How to maintain rigorous testing standards when integrating AI components into your applications.',
      date: new Date(Date.now() - 86400000).toISOString(),
      aiSummary:
        'A comprehensive guide to testing AI-integrated applications, covering unit testing for AI service layers, integration testing with mock AI responses, and strategies for validating AI output quality at scale.',
      hasAISummary: true,
      insightCategories: {
        nodes: [{ id: 'cat2', name: 'Testing', slug: 'testing' }],
      },
      author: {
        node: { name: 'Kevin Champlin' },
      },
    },
    {
      id: '3',
      databaseId: 3,
      title: 'Decoupled Architecture: WordPress as a Headless CMS',
      slug: 'decoupled-architecture-wordpress-headless-cms',
      excerpt:
        'Leveraging WordPress with WPGraphQL for enterprise content management in modern applications.',
      date: new Date(Date.now() - 172800000).toISOString(),
      aiSummary:
        'This insight demonstrates how to effectively use WordPress as a headless CMS, combining the editorial experience users know with the performance and flexibility of modern frontend frameworks like Next.js.',
      hasAISummary: true,
      insightCategories: {
        nodes: [{ id: 'cat3', name: 'WordPress', slug: 'wordpress' }],
      },
      author: {
        node: { name: 'Kevin Champlin' },
      },
    },
  ];

  return mockInsights.slice(0, limit);
}
