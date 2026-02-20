#!/usr/bin/env python3
import os
import re

# Folder with all pages
folder = '/Users/loken/medgix-app--2/madgicx.com'

# Crypto to marketing replacements
replacements = {
    'Bitcoin': 'Facebook Ads',
    'Ethereum': 'Google Ads',
    'Chainlink': 'LinkedIn Ads',
    'Zcash': 'TikTok Ads',
    'Monero': 'Instagram Ads',
    'LEO Token': 'Twitter Ads',
    'WETH': 'YouTube Ads',
    'Pi Network': 'Snapchat Ads',
    'Aster': 'Pinterest Ads',
    'BlackRock': 'Microsoft Ads',
    'crypto': 'marketing',
    'Crypto': 'Marketing',
    'blockchain': 'automation',
    'Blockchain': 'Automation',
    'wallet': 'account',
    'Wallet': 'Account',
    'token': 'campaign',
    'Token': 'Campaign',
    'coin': 'ad',
    'Coin': 'Ad',
    'mining': 'optimizing',
    'Mining': 'Optimizing',
    'trading': 'advertising',
    'Trading': 'Advertising',
    'exchange': 'platform',
    'Exchange': 'Platform',
    'DeFi': 'AI Marketing',
    'NFT': 'Creative',
    'staking': 'scaling',
    'Staking': 'Scaling',
    'yield': 'ROI',
    'Yield': 'ROI'
}

# Process all HTML files
for filename in os.listdir(folder):
    if filename.endswith('.html') and '?' not in filename:
        filepath = os.path.join(folder, filename)
        
        # Read the file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace all crypto terms
        for old, new in replacements.items():
            content = content.replace(old, new)
        
        # Fix Services dropdown - remove it
        content = re.sub(
            r'<!-- Services -->.*?</div>\s*</div>\s*</div>\s*<!-- Resources -->',
            '<!-- Services - no dropdown -->\n      <div class="nav-item">\n        <a href="https://marketingtool.pro/optimization" class="nav-link">Services</a>\n      </div>\n      \n      <!-- Resources -->',
            content,
            flags=re.DOTALL
        )
        
        # Fix logo to be text only
        content = re.sub(
            r'<div class="logo">.*?</div>',
            '<div class="logo">\n      <a href="https://marketingtool.pro">\n        <span class="logo-text">MarketingTool Pro</span>\n      </a>\n    </div>',
            content,
            flags=re.DOTALL
        )
        
        # Remove footer scripts and add clean footer
        if '<footer' in content:
            content = re.sub(
                r'<footer.*?</footer>',
                '<footer style="background: #0a0a0a; padding: 40px 0; text-align: center; border-top: 1px solid #333; margin-top: 80px;">\n  <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">\n    <p style="color: #888; font-size: 14px;">© 2024 MarketingTool Pro. All rights reserved.</p>\n  </div>\n</footer>',
                content,
                flags=re.DOTALL
            )
        else:
            content += '\n<footer style="background: #0a0a0a; padding: 40px 0; text-align: center; border-top: 1px solid #333; margin-top: 80px;">\n  <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">\n    <p style="color: #888; font-size: 14px;">© 2024 MarketingTool Pro. All rights reserved.</p>\n  </div>\n</footer>'
        
        # Write back
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"Fixed {filename}")

print("All pages fixed!")
