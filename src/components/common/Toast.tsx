import { createContext } from 'preact';
import { useContext, useState } from 'preact/hooks';

type ToastType = 'success' | 'error' | 'warning' | 'info';

interface Toast {
  id: number;
  message: string;
  type: ToastType;
}

interface ToastContextType {
  toast: (message: string, type?: ToastType) => void;
}

const ToastContext = createContext<ToastContextType | null>(null);

export function ToastProvider({ children }: { children: preact.ComponentChildren }) {
  const [toasts, setToasts] = useState<Toast[]>([]);

  const toast = (message: string, type: ToastType = 'info') => {
    const id = Date.now();
    setToasts(prev => [...prev, { id, message, type }]);
    setTimeout(() => {
      setToasts(prev => prev.filter(t => t.id !== id));
    }, 3000);
  };

  const getColor = (type: ToastType) => {
    switch (type) {
      case 'success': return '#01b574';
      case 'error': return '#e53e3e';
      case 'warning': return '#ffb547';
      default: return '#0075ff';
    }
  };

  const getIcon = (type: ToastType) => {
    switch (type) {
      case 'success': return 'ri-check-line';
      case 'error': return 'ri-close-line';
      case 'warning': return 'ri-alert-line';
      default: return 'ri-information-line';
    }
  };

  return (
    <ToastContext.Provider value={{ toast }}>
      {children}
      <div style={{ position: 'fixed', bottom: '20px', right: '20px', zIndex: 9999, display: 'flex', flexDirection: 'column', gap: '8px' }}>
        {toasts.map(t => (
          <div
            key={t.id}
            style={{
              background: 'rgba(6,11,40,0.95)',
              border: `1px solid ${getColor(t.type)}40`,
              borderRadius: '10px',
              padding: '12px 16px',
              display: 'flex',
              alignItems: 'center',
              gap: '10px',
              animation: 'slideUp 200ms ease-out',
              backdropFilter: 'blur(12px)',
            }}
          >
            <i class={getIcon(t.type)} style={{ color: getColor(t.type), fontSize: '16px' }} />
            <span style={{ color: '#e2e8f0', fontSize: '13px', fontWeight: 500 }}>{t.message}</span>
          </div>
        ))}
      </div>
    </ToastContext.Provider>
  );
}

export function useToast() {
  const context = useContext(ToastContext);
  if (!context) {
    throw new Error('useToast must be used within ToastProvider');
  }
  return context;
}
