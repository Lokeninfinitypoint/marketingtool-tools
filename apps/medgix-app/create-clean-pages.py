#!/usr/bin/env python3
import os

# Read the clean blog theme
with open('/Users/loken/medgix-app--2/brighthub-full/brighthub.casethemes.net/blog/index.html', 'r') as f:
    clean_template = f.read()

# Pages to create
pages = ['about-us', 'services', 'pricing', 'blog', 'contact', 'ai-ads', 'optimization', 'tracking']

# Create each page
for page in pages:
    filepath = f'/Users/loken/medgix-app--2/clean-pages/{page}.html'
    os.makedirs('/Users/loken/medgix-app--2/clean-pages', exist_ok=True)
    
    # Write the clean template
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(clean_template)
    
    print(f"Created {page}.html")

print("All clean pages created using BrightHub blog theme!")
