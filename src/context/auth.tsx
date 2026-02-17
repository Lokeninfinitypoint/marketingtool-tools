import { createContext } from 'preact';
import { useContext, useState, useEffect } from 'preact/hooks';
import { account } from '../lib/appwrite';

interface User {
  $id: string;
  name: string;
  email: string;
}

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  logout: () => Promise<void>;
  register: (email: string, password: string, name: string) => Promise<void>;
}

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: preact.ComponentChildren }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    try {
      const session = await account.get();
      setUser(session as User);
    } catch {
      setUser(null);
    }
    setLoading(false);
  };

  const login = async (email: string, password: string) => {
    await account.createEmailPasswordSession(email, password);
    await checkAuth();
  };

  const logout = async () => {
    await account.deleteSession('current');
    setUser(null);
  };

  const register = async (email: string, password: string, name: string) => {
    await account.create('unique()', email, password, name);
    await login(email, password);
  };

  return (
    <AuthContext.Provider value={{ user, loading, login, logout, register }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
}
