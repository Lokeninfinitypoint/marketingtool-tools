import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { Appwrite } from 'appwrite';

// Initialize Appwrite
const appwrite = new Appwrite();
appwrite
    .setEndpoint('https://auth.marketingtool.pro/v1')
    .setProject('marketingtool-pro');

// Main App Component
export default function App() {
  return (
    <Router>
      <div className="app dark-theme">
        <Navbar />
        <div className="main-content">
          <Sidebar />
          <Routes>
            <Route path="/" element={<Dashboard />} />
            <Route path="/ai-chat" element={<AIChat />} />
            <Route path="/ai-marketer" element={<AIMarketer />} />
            <Route path="/optimization" element={<Optimization />} />
            <Route path="/analytics" element={<Analytics />} />
            <Route path="/creatives" element={<Creatives />} />
            <Route path="/automation" element={<Automation />} />
          </Routes>
        </div>
      </div>
    </Router>
  );
}

// Navbar Component
function Navbar() {
  return (
    <nav className="navbar dark-nav">
      <div className="logo">
        <i className="fas fa-chart-line"></i>
        <span>MarketingTool Pro</span>
      </div>
      <div className="nav-links">
        <a href="/ai-chat">AI Chat</a>
        <a href="/optimization">Optimization</a>
        <a href="/analytics">Analytics</a>
        <a href="/creatives">Creatives</a>
        <a href="/automation">Automation</a>
      </div>
      <div className="user-menu">
        <img src="user-avatar.jpg" alt="User" />
      </div>
    </nav>
  );
}

// Sidebar Component
function Sidebar() {
  return (
    <div className="sidebar dark-sidebar">
      <div className="sidebar-section">
        <h4>AI Tools</h4>
        <ul>
          <li className="active">AI Chat</li>
          <li>AI Marketer</li>
          <li>AI Ad Generator</li>
        </ul>
      </div>
      
      <div className="sidebar-section">
        <h4>Optimization</h4>
        <ul>
          <li>Ads Manager 2.0</li>
          <li>360° Meta Audit</li>
          <li>Top Audiences</li>
        </ul>
      </div>
      
      <div className="sidebar-section">
        <h4>Analytics</h4>
        <ul>
          <li>Custom Reports</li>
          <li>Business Dashboard</li>
        </ul>
      </div>
    </div>
  );
}

// Dashboard Component
function Dashboard() {
  return (
    <div className="dashboard">
      <h2>Marketing Dashboard</h2>
      <div className="stats-grid">
        <StatCard title="ROAS" value="4.2" trend="up" />
        <StatCard title="Spend" value="$12,450" trend="down" />
        <StatCard title="Conversions" value="1,245" trend="up" />
        <StatCard title="CPA" value="$23.45" trend="down" />
      </div>
      
      <div className="charts">
        <div className="chart-container">
          <h4>Performance Trends</h4>
          <LineChart />
        </div>
        <div className="chart-container">
          <h4>Top Campaigns</h4>
          <BarChart />
        </div>
      </div>
    </div>
  );
}

// AI Chat Component
function AIChat() {
  const [messages, setMessages] = React.useState([]);
  const [input, setInput] = React.useState('');
  
  const handleSend = () => {
    if (input.trim()) {
      setMessages([...messages, { text: input, sender: 'user' }]);
      // Simulate AI response
      setTimeout(() => {
        setMessages(prev => [...prev, { text: "I'm analyzing your ad performance...", sender: 'ai' }]);
      }, 1000);
      setInput('');
    }
  };
  
  return (
    <div className="ai-chat">
      <h2>AI Marketing Assistant</h2>
      <div className="chat-container">
        {messages.map((msg, i) => (
          <div key={i} className={`message ${msg.sender}`}>
            {msg.text}
          </div>
        ))}
      </div>
      <div className="chat-input">
        <input 
          type="text" 
          value={input} 
          onChange={(e) => setInput(e.target.value)} 
          placeholder="Ask about your campaigns..."
        />
        <button onClick={handleSend}>Send</button>
      </div>
    </div>
  );
}
