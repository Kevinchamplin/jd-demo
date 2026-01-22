import { GraphQLClient } from 'graphql-request';

const GRAPHQL_URL =
  process.env.NEXT_PUBLIC_WORDPRESS_GRAPHQL_URL ||
  'http://localhost:8080/wp/graphql';

export const graphqlClient = new GraphQLClient(GRAPHQL_URL, {
  headers: {
    'Content-Type': 'application/json',
  },
});

/**
 * GraphQL Queries
 */

export const GET_INSIGHTS = `
  query GetInsights($first: Int = 10, $after: String) {
    insights(first: $first, after: $after) {
      pageInfo {
        hasNextPage
        endCursor
      }
      nodes {
        id
        databaseId
        title
        slug
        excerpt
        date
        aiSummary
        hasAISummary
        featuredImage {
          node {
            sourceUrl
            altText
          }
        }
        insightCategories {
          nodes {
            id
            name
            slug
          }
        }
        author {
          node {
            name
            avatar {
              url
            }
          }
        }
      }
    }
  }
`;

export const GET_INSIGHT_BY_SLUG = `
  query GetInsightBySlug($slug: ID!) {
    insight(id: $slug, idType: SLUG) {
      id
      databaseId
      title
      content
      excerpt
      date
      modified
      aiSummary
      hasAISummary
      aiSummaryMeta {
        model
        generatedAt
        contentLength
        summaryLength
        tokensUsed
        finishReason
      }
      featuredImage {
        node {
          sourceUrl
          altText
        }
      }
      insightCategories {
        nodes {
          id
          name
          slug
        }
      }
      author {
        node {
          name
          avatar {
            url
          }
        }
      }
    }
  }
`;

export const GET_ALL_INSIGHT_SLUGS = `
  query GetAllInsightSlugs {
    insights(first: 100) {
      nodes {
        slug
      }
    }
  }
`;

/**
 * Type definitions for GraphQL responses
 */
export interface Insight {
  id: string;
  databaseId: number;
  title: string;
  slug: string;
  content?: string;
  excerpt?: string;
  date: string;
  modified?: string;
  aiSummary?: string;
  hasAISummary?: boolean;
  aiSummaryMeta?: {
    model?: string;
    generatedAt?: string;
    contentLength?: number;
    summaryLength?: number;
    tokensUsed?: number;
    finishReason?: string;
  };
  featuredImage?: {
    node: {
      sourceUrl: string;
      altText?: string;
    };
  };
  insightCategories?: {
    nodes: Array<{
      id: string;
      name: string;
      slug: string;
    }>;
  };
  author?: {
    node: {
      name: string;
      avatar?: {
        url: string;
      };
    };
  };
}

export interface InsightsResponse {
  insights: {
    pageInfo: {
      hasNextPage: boolean;
      endCursor?: string;
    };
    nodes: Insight[];
  };
}

export interface InsightResponse {
  insight: Insight | null;
}
