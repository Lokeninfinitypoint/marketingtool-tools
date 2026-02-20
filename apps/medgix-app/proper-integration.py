#!/usr/bin/env python3
import os
import re

# Read BrightHub theme structure (clean version)
with open('/Users/loken/medgix-app--2/brighthub-clone/brighthub.casethemes.net/blockchain-web3-saas/index.html', 'r') as f:
    brightHub_template = f.read()

# Read Madgicx content for structure reference
madgicx_files = {
    'home': '/Users/loken/medgix-app--2/madgicx.com/index.html',
    'about': '/Users/loken/medgix-app--2/madgicx.com/about-us.html',
    'pricing': '/Users/loken/medgix-app--2/madgicx.com/pricing.html',
    'blog': '/Users/loken/medgix-app--2/madgicx.com/blog.html',
    'services': '/Users/loken/medgix-app--2/madgicx.com/optimization.html'
}

# Content mapping from crypto to marketing
content_replacements = {
    # Crypto to Marketing
    'crypto': 'marketing',
    'Crypto': 'Marketing',
    'blockchain': 'automation',
    'Blockchain': 'Automation',
    'Bitcoin': 'Facebook Ads',
    'Ethereum': 'Google Ads',
    'wallet': 'ad account',
    'Wallet': 'Ad Account',
    'trading': 'advertising',
    'Trading': 'Advertising',
    'token': 'campaign',
    'Token': 'Campaign',
    'NFT': 'Creative Ad',
    'DeFi': 'AI Marketing',
    'mining': 'optimizing',
    'Mining': 'Optimizing',
    'staking': 'scaling',
    'Staking': 'Scaling',
    'yield': 'ROI',
    'Yield': 'ROI',
    'exchange': 'platform',
    'Exchange': 'Platform',
    'coin': 'ad',
    'Coin': 'Ad',
    'Chainlink': 'LinkedIn Ads',
    'Zcash': 'TikTok Ads',
    'Monero': 'Instagram Ads'
}

# Clean the BrightHub template
clean_template = brightHub_template

# Replace all crypto references with marketing
for old, new in content_replacements.items():
    clean_template = clean_template.replace(old, new)

# Update branding to MarketingTool Pro
clean_template = clean_template.replace('BrightHub', 'MarketingTool Pro')
clean_template = clean_template.replace('brighthub', 'marketingtool')

# Save the clean template
with open('/Users/loken/medgix-app--2/marketingtool-pro-clean/index.html', 'w') as f:
    f.write(clean_template)

print("Created clean MarketingTool Pro template using BrightHub theme structure")
print("No Madgicx theme code used - only BrightHub theme with marketing content")
