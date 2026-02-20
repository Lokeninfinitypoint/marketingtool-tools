import React, { useState } from 'react';
import { Appwrite } from 'appwrite';

const AIChat = () => {
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  
  // Initialize Appwrite
  const appwrite = new Appwrite();
  appwrite
    .setEndpoint('https://auth.marketingtool.pro/v1')
    .setProject('marketingtool-pro');

  const handleSend = async () => {
    if (!input.trim()) return;
    
    // Add user message
    const userMsg = { text: input, sender: 'user' };
    setMessages(prev => [...prev, userMsg]);
    setInput('');
    setIsLoading(true);
    
    try {
      // Call Windmill workflow
      const response = await fetch('https://wm.marketingtool.pro/api/w/ai_chat', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${await appwrite.account.getSession('current')}`
        },
        body: JSON.stringify({ 
          message: input,
          context: 'marketing'
        })
      });
      
      const data = await response.json();
      setMessages(prev => [...prev, {
        text: data.response,
        sender: 'ai',
        actions: data.actions
      }]);
    } catch (error) {
      setMessages(prev => [...prev, {
        text: 'Sorry, I encountered an error. Please try again.',
        sender: 'ai',
        isError: true
      }]);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="ai-chat-container">
      <div className="chat-messages">
        {messages.map((msg, i) => (
          <div key={i} className={`message ${msg.sender}`}>
            <div className="message-content">
              {msg.text}
              {msg.actions && (
                <div className="message-actions">
                  {msg.actions.map((action, j) => (
                    <button key={j} onClick={() => handleAction(action)}>
                      {action.label}
                    </button>
                  ))}
                </div>
              )}
            </div>
          </div>
        ))}
        {isLoading && <div className="loading-indicator">AI is thinking...</div>}
      </div>
      
      <div className="chat-input">
        <input
          type="text"
          value={input}
          onChange={(e) => setInput(e.target.value)}
          placeholder="Ask about your campaigns..."
          disabled={isLoading}
        />
        <button onClick={handleSend} disabled={isLoading}>
          Send
        </button>
      </div>
    </div>
  );
};

export default AIChat;
