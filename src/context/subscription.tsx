import { createContext } from 'preact';
import { useContext, useState } from 'preact/hooks';

interface SubscriptionContextType {
  plan: 'free' | 'pro' | 'enterprise';
  credits: number;
  hasAccess: (toolSlug: string) => boolean;
}

const SubscriptionContext = createContext<SubscriptionContextType | null>(null);

// Free tools (no subscription required)
const FREE_TOOLS = [
  'blog-writer',
  'article-generator',
  'hashtag-generator',
  'caption-creator',
  'meta-description-generator',
  'seo-title-generator',
];

export function SubscriptionProvider({ children }: { children: preact.ComponentChildren }) {
  const [plan] = useState<'free' | 'pro' | 'enterprise'>('pro'); // Default to pro for demo
  const [credits] = useState(100);

  const hasAccess = (toolSlug: string) => {
    if (plan === 'enterprise') return true;
    if (plan === 'pro') return true;
    return FREE_TOOLS.includes(toolSlug);
  };

  return (
    <SubscriptionContext.Provider value={{ plan, credits, hasAccess }}>
      {children}
    </SubscriptionContext.Provider>
  );
}

export function useSubscription() {
  const context = useContext(SubscriptionContext);
  if (!context) {
    throw new Error('useSubscription must be used within SubscriptionProvider');
  }
  return context;
}
