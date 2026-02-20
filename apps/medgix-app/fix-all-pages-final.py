#!/usr/bin/env python3
import os

# Read the correct homepage template
with open('/Users/loken/medgix-app--2/brighthub-clone/brighthub.casethemes.net/blockchain-web3-saas/index.html', 'r') as f:
    homepage_template = f.read()

# Pages that need to be fixed
pages = ['services', 'blog', 'about-us', 'pricing', 'optimization', 'ai-ads', 'tracking', 'ad-library', 'case-studies']

# Apply homepage template to all pages
for page in pages:
    filepath = f'/Users/loken/medgix-app--2/madgicx.com/{page}.html'
    
    # Write the homepage template
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(homepage_template)
    
    print(f"Fixed {page}.html")

print("All pages now use the correct template!")
