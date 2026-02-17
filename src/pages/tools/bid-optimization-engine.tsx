import { useState } from 'preact/hooks';
import { useAuth } from '@/context/auth';
import { useSubscription } from '@/context/subscription';
import { useToast } from '@/components/common/Toast';
import { databases, account } from '@/lib/appwrite';
import { ID } from 'appwrite';

const MEDIA_BASE = import.meta.env.VITE_MEDIA_BASE || 'https://media.marketingtool.pro';
const WINDMILL_URL = import.meta.env.VITE_WINDMILL_URL || 'https://wm.marketingtool.pro';

// Tool Data
const TOOL_DATA = {
  slug: 'bid-optimization-engine',
  name: 'Bid Optimization Engine',
  badge: 'Budget',
  badgeColor: '#48bb78',
  description: 'AI-powered bid optimization engine',
  heroVideo: '/media/videos/Digital%20Marketing%20Tips%20for%20Beginners%20Social%20Media%20Growth%20Strateg.mp4',
  tabs: [{"id":"overview","name":"Overview","icon":"📊"},{"id":"analysis","name":"Analysis","icon":"🔎"},{"id":"optimize","name":"Optimize","icon":"📈"},{"id":"report","name":"Report","icon":"📋"}],
  tabImages: {"overview":["d57fefa16782040d0e89a675e36df06f.png","d5a46f09fe801afa4888c8252a255abf.png","d72bf484d39cfaf76a050830b7f02662.png"],"analysis":["d7e3dfb7fbb679e5b0e98dde03540827.png","d8c503e3ab5b7fb5970734b2d4ed0fad.png","d9f1c7bf3721c92da9e5b4457ca06689.png"],"optimize":["dark-light.png","dashboard.png","db3a5475704287bfb8e2fc9d06b918d7.png"],"report":["dc996bdec61c6ae5079546c030cba081.png","dcfa1ff216de6ad0b54a74b642f96a68.png","demo.png"]},
  tabDescriptions: {"overview":[{"title":"Budget Overview","desc":"Spend summary"},{"title":"Allocation","desc":"Channel split"},{"title":"Pacing","desc":"Daily rate"}],"analysis":[{"title":"Strategy","desc":"Bid approach"},{"title":"Targets","desc":"CPA/ROAS goals"},{"title":"Limits","desc":"Cap settings"}],"optimize":[{"title":"Forecast","desc":"Projected spend"},{"title":"Scenarios","desc":"What-if analysis"},{"title":"Trends","desc":"Historical data"}],"report":[{"title":"Savings","desc":"Cut waste"},{"title":"Efficiency","desc":"Cost per result"},{"title":"Scale Plan","desc":"Growth budget"}]},
  tips: [{"title":"Set Budget Amount","desc":"Include your total or daily budget figure."},{"title":"Define Goals","desc":"Specify target CPA, ROAS, or conversion goals."},{"title":"List Channels","desc":"Name the platforms for allocation recommendations."}],
};

// Form Fields
const FORM_FIELDS = [
  {
    "name": "mainInput",
    "label": "Describe what you need",
    "type": "textarea",
    "placeholder": "Describe your bid optimization engine requirements...",
    "required": true
  }
];

export function BidOptimizationEnginePage() {
  const { user } = useAuth();
  const { hasAccess } = useSubscription();
  const { toast } = useToast();
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState('');
  const [activeTab, setActiveTab] = useState(TOOL_DATA.tabs[0]?.id || 'overview');
  const [formData, setFormData] = useState<Record<string, string>>({});

  const locked = !hasAccess(TOOL_DATA.slug);
  const currentImages = TOOL_DATA.tabImages[activeTab] || [];
  const currentDescriptions = TOOL_DATA.tabDescriptions[activeTab] || [];

  const handleInputChange = (name: string, value: string) => {
    setFormData(prev => ({ ...prev, [name]: value }));
  };

  const handleGenerate = async () => {
    if (!user || locked) return;
    const mainInput = formData.mainInput?.trim();
    if (!mainInput) {
      toast('Please describe what you need', 'warning');
      return;
    }
    if (mainInput.length > 5000) {
      toast('Input too long. Maximum 5000 characters.', 'warning');
      return;
    }

    setLoading(true);
    setResult('');

    try {
      const jwt = await account.createJWT();
      const response = await fetch(`${WINDMILL_URL}/api/w/marketingtool/jobs/run_wait_result/f/marketingtool/generate`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${jwt.jwt}` },
        body: JSON.stringify({
          toolSlug: TOOL_DATA.slug,
          toolName: TOOL_DATA.name,
          input: mainInput.substring(0, 5000),
          additionalInputs: formData,
          userId: user.$id,
        }),
      });

      if (!response.ok) throw new Error('Generation failed');
      const data = await response.json();
      const output = typeof data === 'string' ? data : data.result || data.output || JSON.stringify(data);
      setResult(output);

      // Save to database
      try {
        await databases.createDocument('main', 'generations', ID.unique(), {
          userId: user.$id,
          toolSlug: TOOL_DATA.slug,
          toolName: TOOL_DATA.name,
          platform: TOOL_DATA.badge.toLowerCase().includes('google') ? 'google'
            : TOOL_DATA.badge.toLowerCase().includes('facebook') || TOOL_DATA.badge.toLowerCase().includes('meta') ? 'facebook'
            : TOOL_DATA.badge.toLowerCase().includes('instagram') ? 'instagram' : 'website',
          input: mainInput.substring(0, 500),
          output: output.substring(0, 2000),
          status: 'completed',
        });
      } catch {}

      toast('Generated successfully!', 'success');
    } catch (err) {
      console.error('Generation error:', err);
      toast('Generation failed. Please try again.', 'error');
    }
    setLoading(false);
  };

  const copyResult = () => {
    navigator.clipboard.writeText(result);
    toast('Copied to clipboard', 'success');
  };

  const downloadResult = () => {
    const blob = new Blob([result], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${TOOL_DATA.slug}-output.txt`;
    a.click();
    URL.revokeObjectURL(url);
    toast('Downloaded', 'success');
  };

  return (
    <div class="animate-fade-in">
      <style>{`
        @keyframes slideUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
        @keyframes spin{to{transform:rotate(360deg)}}
        .animate-fade-in{animation:slideUp 400ms ease-out both}
      `}</style>

      {/* Header */}
      <div class="relative overflow-hidden rounded-xl mb-6 p-5 md:p-6"
        style={{
          background: `linear-gradient(135deg, rgba(6,11,40,0.95) 0%, ${TOOL_DATA.badgeColor}08 100%)`,
          border: `1px solid ${TOOL_DATA.badgeColor}15`,
        }}>
        <div class="absolute -top-16 -right-16 w-[200px] h-[200px] rounded-full pointer-events-none"
          style={{ background: `radial-gradient(circle, ${TOOL_DATA.badgeColor}10 0%, transparent 70%)`, filter: 'blur(40px)' }} />

        <div class="relative flex items-start gap-4">
          <a href="/dashboard/tools" class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 no-underline"
            style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(226,232,240,0.08)', color: '#a0aec0' }}>
            <i class="ri-arrow-left-line" style={{ fontSize: '16px' }} />
          </a>
          <div class="w-14 h-14 rounded-xl flex items-center justify-center shrink-0"
            style={{ background: `linear-gradient(135deg, ${TOOL_DATA.badgeColor}15, ${TOOL_DATA.badgeColor}05)`, border: `1px solid ${TOOL_DATA.badgeColor}18` }}>
            <img src={`${MEDIA_BASE}/media/icons/ai-3d/15_Robot AI.png`} alt="" class="w-11 h-11" loading="lazy" />
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-center flex-wrap gap-2.5 mb-1.5">
              <h1 class="text-xl font-extrabold tracking-tight" style={{ color: '#fff' }}>{TOOL_DATA.name}</h1>
              <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md"
                style={{ background: `${TOOL_DATA.badgeColor}15`, color: TOOL_DATA.badgeColor, border: `1px solid ${TOOL_DATA.badgeColor}25` }}>
                {TOOL_DATA.badge}
              </span>
              {locked && (
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md flex items-center gap-1"
                  style={{ background: 'rgba(255,181,71,0.1)', color: '#ffb547', border: '1px solid rgba(255,181,71,0.2)' }}>
                  <i class="ri-lock-line" style={{ fontSize: '11px' }} /> Locked
                </span>
              )}
            </div>
            <p class="text-[13px]" style={{ color: '#718096' }}>{TOOL_DATA.description}</p>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        {/* Left: Form + Result */}
        <div class="lg:col-span-3">
          <div class="rounded-xl overflow-hidden" style={{ background: 'linear-gradient(127.09deg,rgba(6,11,40,.94) 19.41%,rgba(10,14,35,.49) 76.65%)', border: '1px solid rgba(226,232,240,.08)' }}>
            <div class="flex items-center gap-3 px-5 py-4" style={{ borderBottom: '1px solid rgba(226,232,240,0.08)' }}>
              <div class="w-1 h-4 rounded-full" style={{ background: TOOL_DATA.badgeColor }} />
              <h6 class="text-[13px] font-bold" style={{ color: '#fff' }}>Generate Content</h6>
              {result && (
                <span class="ml-auto text-[10px] font-semibold px-2 py-0.5 rounded-md"
                  style={{ background: 'rgba(1,181,116,0.1)', color: '#01b574', border: '1px solid rgba(1,181,116,0.15)' }}>
                  <i class="ri-check-line" /> Generated
                </span>
              )}
            </div>

            <div class="p-5">
              {/* Form Fields */}
              {FORM_FIELDS.map((field: any) => (
                <div key={field.name} class="mb-4">
                  <label class="block text-[11px] font-bold uppercase tracking-[0.08em] mb-2" style={{ color: '#a0aec0' }}>
                    {field.label} {field.required && <span style={{ color: '#e53e3e' }}>*</span>}
                  </label>
                  {field.type === 'textarea' ? (
                    <textarea
                      value={formData[field.name] || ''}
                      onInput={(e) => handleInputChange(field.name, (e.target as HTMLTextAreaElement).value)}
                      placeholder={field.placeholder}
                      rows={5}
                      class="w-full px-4 py-3 rounded-lg text-sm resize-none"
                      style={{ background: 'rgba(6,11,40,0.6)', border: '1px solid rgba(226,232,240,0.1)', color: '#e2e8f0', outline: 'none' }}
                    />
                  ) : field.type === 'select' ? (
                    <select
                      value={formData[field.name] || ''}
                      onChange={(e) => handleInputChange(field.name, (e.target as HTMLSelectElement).value)}
                      class="w-full px-4 py-3 rounded-lg text-sm"
                      style={{ background: 'rgba(6,11,40,0.6)', border: '1px solid rgba(226,232,240,0.1)', color: '#e2e8f0', outline: 'none' }}
                    >
                      <option value="">Select...</option>
                      {field.options?.map((opt: string) => <option key={opt} value={opt}>{opt}</option>)}
                    </select>
                  ) : (
                    <input
                      type={field.type}
                      value={formData[field.name] || ''}
                      onInput={(e) => handleInputChange(field.name, (e.target as HTMLInputElement).value)}
                      placeholder={field.placeholder}
                      class="w-full px-4 py-3 rounded-lg text-sm"
                      style={{ background: 'rgba(6,11,40,0.6)', border: '1px solid rgba(226,232,240,0.1)', color: '#e2e8f0', outline: 'none' }}
                    />
                  )}
                </div>
              ))}

              {/* Generate Button */}
              <button
                onClick={handleGenerate}
                disabled={loading || locked}
                class="w-full h-12 rounded-lg text-sm font-bold flex items-center justify-center gap-2"
                style={{
                  background: locked ? 'rgba(255,255,255,0.04)' : loading ? 'rgba(0,117,255,0.6)' : `linear-gradient(135deg, ${TOOL_DATA.badgeColor}, ${TOOL_DATA.badgeColor}cc)`,
                  color: locked ? '#718096' : '#fff',
                  border: locked ? '1px solid rgba(226,232,240,0.1)' : 'none',
                  cursor: locked ? 'not-allowed' : 'pointer',
                }}
              >
                {loading ? (
                  <>
                    <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full" style={{ animation: 'spin 0.8s linear infinite' }} />
                    Generating...
                  </>
                ) : locked ? (
                  <><i class="ri-lock-line" /> Upgrade to Use</>
                ) : (
                  <><i class="ri-sparkling-2-line" style={{ fontSize: '16px' }} /> Generate with AI</>
                )}
              </button>

              {/* Result */}
              {result && (
                <div class="mt-6" style={{ animation: 'slideUp 300ms ease-out' }}>
                  <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                      <div class="w-1 h-4 rounded-full" style={{ background: '#01b574' }} />
                      <h6 class="text-[13px] font-bold" style={{ color: '#fff' }}>Result</h6>
                    </div>
                    <div class="flex gap-1.5">
                      <button onClick={copyResult} class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold flex items-center gap-1"
                        style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(226,232,240,0.08)', color: '#a0aec0' }}>
                        <i class="ri-file-copy-line" /> Copy
                      </button>
                      <button onClick={downloadResult} class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold flex items-center gap-1"
                        style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(226,232,240,0.08)', color: '#a0aec0' }}>
                        <i class="ri-download-line" /> Save
                      </button>
                      <button onClick={() => { setResult(''); setFormData({}); }} class="px-2.5 py-1.5 rounded-lg text-[11px] font-semibold flex items-center gap-1"
                        style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(226,232,240,0.08)', color: '#a0aec0' }}>
                        <i class="ri-refresh-line" /> New
                      </button>
                    </div>
                  </div>
                  <div class="rounded-xl p-5 text-sm leading-relaxed whitespace-pre-wrap max-h-[500px] overflow-y-auto"
                    style={{ background: 'rgba(6,11,40,0.6)', border: '1px solid rgba(226,232,240,0.08)', color: '#e2e8f0' }}>
                    {result}
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Right: Info Panel */}
        <div class="lg:col-span-2 flex flex-col gap-4">
          {/* Hero Video */}
          {TOOL_DATA.heroVideo && (
            <div class="rounded-xl overflow-hidden" style={{ background: 'linear-gradient(127.09deg,rgba(6,11,40,.94) 19.41%,rgba(10,14,35,.49) 76.65%)', border: '1px solid rgba(226,232,240,.08)' }}>
              <video
                src={`${MEDIA_BASE}${TOOL_DATA.heroVideo}`}
                class="w-full" style={{ aspectRatio: '16/9', objectFit: 'cover' }}
                autoplay muted loop playsInline
              />
            </div>
          )}

          {/* Tabs + Gallery */}
          {TOOL_DATA.tabs.length > 0 && (
            <div class="rounded-xl overflow-hidden" style={{ background: 'linear-gradient(127.09deg,rgba(6,11,40,.94) 19.41%,rgba(10,14,35,.49) 76.65%)', border: '1px solid rgba(226,232,240,.08)' }}>
              <div class="flex overflow-x-auto" style={{ borderBottom: '1px solid rgba(226,232,240,0.08)' }}>
                {TOOL_DATA.tabs.map((tab: any) => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    class="px-4 py-3 text-[12px] font-semibold whitespace-nowrap"
                    style={{
                      color: activeTab === tab.id ? TOOL_DATA.badgeColor : '#64748b',
                      background: 'transparent',
                      border: 'none',
                      borderBottom: activeTab === tab.id ? `2px solid ${TOOL_DATA.badgeColor}` : '2px solid transparent',
                      cursor: 'pointer',
                    }}
                  >
                    <span class="mr-1">{tab.icon}</span> {tab.name}
                  </button>
                ))}
              </div>

              {/* Tab Descriptions */}
              {currentDescriptions.length > 0 && (
                <div class="p-3 flex gap-2 overflow-x-auto" style={{ borderBottom: '1px solid rgba(226,232,240,0.06)' }}>
                  {currentDescriptions.map((item: any, i: number) => (
                    <div key={i} class="flex-shrink-0 px-3 py-2 rounded-lg" style={{ background: 'rgba(255,255,255,0.03)', border: '1px solid rgba(226,232,240,0.06)' }}>
                      <div class="text-[11px] font-semibold" style={{ color: '#e2e8f0' }}>{item.title}</div>
                      <div class="text-[10px]" style={{ color: '#718096' }}>{item.desc}</div>
                    </div>
                  ))}
                </div>
              )}

              {/* Images */}
              {currentImages.length > 0 && (
                <div class="p-3">
                  <div class="grid grid-cols-2 gap-2">
                    {currentImages.slice(0, 4).map((img: string, i: number) => (
                      <div key={i} class="rounded-lg overflow-hidden" style={{ aspectRatio: '16/10', background: 'rgba(0,0,0,0.2)' }}>
                        <img
                          src={`${MEDIA_BASE}/media/images/${img}`}
                          alt={`${TOOL_DATA.name} ${activeTab} ${i + 1}`}
                          class="w-full h-full object-cover"
                          loading="lazy"
                        />
                      </div>
                    ))}
                  </div>
                </div>
              )}
            </div>
          )}

          {/* Tips */}
          {TOOL_DATA.tips.length > 0 && (
            <div class="rounded-xl overflow-hidden" style={{ background: 'linear-gradient(127.09deg,rgba(6,11,40,.94) 19.41%,rgba(10,14,35,.49) 76.65%)', border: '1px solid rgba(226,232,240,.08)' }}>
              <div class="flex items-center gap-3 px-5 py-4" style={{ borderBottom: '1px solid rgba(226,232,240,0.08)' }}>
                <div class="w-1 h-4 rounded-full" style={{ background: '#ffb547' }} />
                <i class="ri-lightbulb-line" style={{ color: '#ffb547', fontSize: '15px' }} />
                <h6 class="text-[13px] font-bold" style={{ color: '#fff' }}>Pro Tips</h6>
              </div>
              <div class="p-4 flex flex-col gap-3">
                {TOOL_DATA.tips.map((tip: any, i: number) => (
                  <div key={i} class="flex gap-3">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0"
                      style={{ background: `${TOOL_DATA.badgeColor}12`, border: `1px solid ${TOOL_DATA.badgeColor}18` }}>
                      <span class="text-[10px] font-bold" style={{ color: TOOL_DATA.badgeColor }}>{i + 1}</span>
                    </div>
                    <div>
                      <p class="text-[13px] font-semibold" style={{ color: '#e2e8f0' }}>{tip.title}</p>
                      <p class="text-[11px] mt-0.5" style={{ color: '#718096' }}>{tip.desc}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Quick Actions */}
          <div class="rounded-xl overflow-hidden" style={{ background: 'linear-gradient(127.09deg,rgba(6,11,40,.94) 19.41%,rgba(10,14,35,.49) 76.65%)', border: '1px solid rgba(226,232,240,.08)' }}>
            <div class="flex items-center gap-3 px-5 py-4" style={{ borderBottom: '1px solid rgba(226,232,240,0.08)' }}>
              <div class="w-1 h-4 rounded-full" style={{ background: '#0075ff' }} />
              <h6 class="text-[13px] font-bold" style={{ color: '#fff' }}>Quick Actions</h6>
            </div>
            <div class="p-4 flex flex-col gap-2">
              <a href="/dashboard/tools" class="w-full px-4 py-2.5 rounded-lg text-[12px] font-medium flex items-center gap-2 no-underline"
                style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(226,232,240,0.08)', color: '#a0aec0' }}>
                <i class="ri-apps-2-line" style={{ fontSize: '15px' }} /> Browse All Tools
              </a>
              <a href="/dashboard/ai-chat" class="w-full px-4 py-2.5 rounded-lg text-[12px] font-medium flex items-center gap-2 no-underline"
                style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(226,232,240,0.08)', color: '#a0aec0' }}>
                <i class="ri-robot-2-line" style={{ fontSize: '15px' }} /> AI Chat Assistant
              </a>
              <a href="/dashboard/ads" class="w-full px-4 py-2.5 rounded-lg text-[12px] font-medium flex items-center gap-2 no-underline"
                style={{ background: 'rgba(255,255,255,0.04)', border: '1px solid rgba(226,232,240,0.08)', color: '#a0aec0' }}>
                <i class="ri-advertisement-line" style={{ fontSize: '15px' }} /> Ads Creator
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default BidOptimizationEnginePage;
