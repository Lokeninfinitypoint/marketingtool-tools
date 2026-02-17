import Router from 'preact-router';
import { AuthProvider } from './context/auth';
import { SubscriptionProvider } from './context/subscription';
import { ToastProvider } from './components/common/Toast';

// Import all tool pages
import * as ToolPages from './pages/tools';

// Tool slugs for routing
const toolSlugs = [
  'academic-writer', 'ad-audit-tool', 'ad-copy-analyzer', 'ad-copy-sentiment-analyzer',
  'ad-fatigue-detector', 'ad-group-audit', 'ad-intelligence-software', 'ad-testing-tools',
  'ads-launcher', 'ads-rotation-agent', 'advanced-ad-analyzer', 'affiliate-marketing-copy',
  'ai-ad-management-tools', 'ai-ads-generator', 'ai-advertising-suite', 'ai-campaign-manager',
  'ai-creative-director', 'ai-media-buyer', 'ai-paid-social-manager', 'ai-performance-marketer',
  'ai-task-prioritizer', 'api-documentation-generator', 'article-generator', 'article-rewriter',
  'article-summarizer', 'audience-optimization-tool', 'auto-scaling-budget-tool',
  'automated-ad-launch-tool', 'automated-reporting-suite', 'autonomous-marketing-manager',
  'backlink-outreach-email-generator', 'bid-optimization-engine', 'bid-suggestions-tool',
  'blog-conclusion-writer', 'blog-intro-writer', 'blog-outline-writer', 'blog-post-ideas',
  'blog-writer', 'brand-identity-builder', 'brand-safety-monitor', 'brand-voice-generator',
  'budget-manager', 'budget-performance-analyzer', 'campaign-ab-test-manager',
  'campaign-audit-tool', 'campaign-auto-optimizer', 'campaign-budget-allocator',
  'campaign-cloner-pro', 'campaign-compliance-checker', 'campaign-health-monitor',
  'campaign-optimization-engine', 'campaign-performance-predictor', 'campaign-scaling-assistant',
  'campaign-template-library', 'campaign-tools-suite', 'caption-creator', 'cart-recovery-ads',
  'case-study-writer', 'client-reporting-portal', 'code-comments-generator',
  'cold-outreach-email', 'comparison-chart-creator', 'competitive-benchmarking-ai',
  'competitor-analysis-tool', 'content-gap-finder', 'content-rewriter',
  'conversion-path-analyzer', 'copy-generator', 'creative-fatigue-predictor',
  'creative-intelligence-platform', 'creative-optimization-ai', 'creative-performance-analyzer',
  'creative-refresh-agent', 'cross-sell-ad-generator', 'cta-writer', 'ctr-predictor',
  'customer-journey-intelligence', 'dayparting-optimizer', 'device-optimization',
  'dynamic-product-ads', 'ecommerce-ad-platform', 'email-writer', 'engagement-calculator',
  'exam-prep-content', 'explainer-generator', 'facebook-ads-manager',
  'facebook-ads-orchestrator', 'facebook-ads-performance-grader', 'facebook-marketing-suite',
  'facebook-performance-dashboard', 'faq-generator', 'faq-schema-writer',
  'free-keyword-tools', 'geo-targeting-optimizer', 'google-ads-budget-calculator',
  'google-ads-performance-grader', 'google-analytics-grader', 'hashtag-generator',
  'image-resizer', 'instagram-ai-automation', 'instagram-caption-generator',
  'instagram-content-scheduler', 'instagram-engagement-analyzer', 'instagram-influencer-finder',
  'instagram-management-tools', 'instagram-marketing-platform', 'instagram-reels-optimizer',
  'instagram-shopping-ads', 'instagram-story-ads-manager', 'intelligent-automation-platform',
  'internal-linking-suggestions', 'inventory-based-ad-manager', 'keyword-audit-tool',
  'keyword-cluster-generator', 'keyword-intent-detector', 'keyword-research-tool',
  'landing-page-audit', 'lead-magnet-creator', 'linkedin-ad-copy-generator',
  'long-tail-keyword-generator', 'market-research-summary', 'marketing-ai-agents-hub',
  'marketing-asset-library', 'marketing-budget-planner', 'marketing-calendar',
  'marketing-copy-generator', 'marketing-efficiency-software', 'marketing-kpi-dashboard',
  'marketing-proposal-generator', 'marketing-software-hub', 'marketing-team-collaboration',
  'meta-ai-comment-responder', 'meta-attribution-tool', 'meta-audience-builder',
  'meta-budget-optimizer', 'meta-campaign-analyzer', 'meta-comment-manager',
  'meta-conversion-tracker', 'meta-creative-studio', 'meta-description-generator',
  'meta-placement-optimizer', 'microsoft-ads-grader', 'multi-channel-attribution',
  'multi-platform-campaign-hub', 'negative-keyword-audit', 'on-page-seo-checker',
  'optimization-software-suite', 'paragraph-expander', 'paragraph-improver',
  'paragraph-rewriter', 'paragraph-shortener', 'paragraph-simplifier',
  'performance-auto-alerts', 'performance-forecasting', 'performance-intelligence-dashboard',
  'performance-monitor', 'pinterest-ad-generator', 'placement-audit', 'placement-optimization',
  'podcast-script-writer', 'post-scheduler', 'press-release-generator',
  'product-description-writer', 'product-feed-optimizer', 'product-launch-email-sequence',
  'product-performance-tracker', 'product-recommendation-engine', 'quality-score-audit',
  'quality-score-checker', 'real-time-meta-optimizer', 'reporting-tools',
  'roas-prediction-platform', 'roi-calculator', 'roi-intelligence-platform',
  'rule-based-campaign-manager', 'sales-page-copy-writer', 'schema-generator',
  'search-term-audit', 'seasonal-ecommerce-planner', 'sentence-expander', 'sentence-rewriter',
  'sentence-shortener', 'sentence-simplifier', 'seo-audit-tool', 'seo-title-generator',
  'serp-analyzer', 'shopify-campaign-manager', 'shopify-marketing-tools',
  'shopping-campaign-intelligence', 'smart-scheduling-engine', 'social-analytics-dashboard',
  'social-hashtag-generator', 'social-media-post-generator', 'study-notes-generator',
  'technical-explanation-writer', 'testimonial-generator', 'tiktok-ad-creator',
  'twitter-thread-generator', 'unified-analytics-platform', 'visual-performance-optimizer',
  'webinar-script-writer', 'website-grader', 'whitepaper-generator',
  'workflow-automation-builder', 'youtube-ad-script-writer', 'youtube-description-generator',
  'youtube-title-generator'
];

// Convert slug to PascalCase component name
function toPascalCase(slug: string) {
  return slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('') + 'Page';
}

// Home page with tools listing
function HomePage() {
  return (
    <div style={{ padding: '40px 20px', maxWidth: '1400px', margin: '0 auto' }}>
      <h1 style={{ fontSize: '32px', fontWeight: 800, marginBottom: '8px', color: '#fff' }}>
        AI Marketing Tools
      </h1>
      <p style={{ color: '#718096', marginBottom: '32px' }}>207 tools to supercharge your marketing</p>

      <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))', gap: '16px' }}>
        {toolSlugs.map(slug => (
          <a
            key={slug}
            href={`/tool/${slug}`}
            style={{
              display: 'block',
              padding: '20px',
              background: 'linear-gradient(127.09deg,rgba(6,11,40,.94) 19.41%,rgba(10,14,35,.49) 76.65%)',
              border: '1px solid rgba(226,232,240,0.08)',
              borderRadius: '12px',
              textDecoration: 'none',
              transition: 'all 200ms',
            }}
          >
            <div style={{ fontSize: '15px', fontWeight: 600, color: '#fff', marginBottom: '4px' }}>
              {slug.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')}
            </div>
            <div style={{ fontSize: '12px', color: '#718096' }}>
              /tool/{slug}
            </div>
          </a>
        ))}
      </div>
    </div>
  );
}

// 404 page
function NotFound() {
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', minHeight: '60vh', textAlign: 'center' }}>
      <h1 style={{ fontSize: '48px', fontWeight: 800, color: '#fff', marginBottom: '8px' }}>404</h1>
      <p style={{ color: '#718096', marginBottom: '24px' }}>Tool not found</p>
      <a href="/" class="glass-btn glass-btn-primary">Back to Home</a>
    </div>
  );
}

// Dynamic tool route component
function ToolRoute({ slug }: { slug?: string }) {
  if (!slug || !toolSlugs.includes(slug)) {
    return <NotFound />;
  }

  const componentName = toPascalCase(slug);
  const ToolComponent = (ToolPages as any)[componentName];

  if (!ToolComponent) {
    return <NotFound />;
  }

  return <ToolComponent />;
}

export function App() {
  return (
    <AuthProvider>
      <SubscriptionProvider>
        <ToastProvider>
          <div style={{ minHeight: '100vh' }}>
            <Router>
              <HomePage path="/" />
              <ToolRoute path="/tool/:slug" />
              <NotFound default />
            </Router>
          </div>
        </ToastProvider>
      </SubscriptionProvider>
    </AuthProvider>
  );
}
