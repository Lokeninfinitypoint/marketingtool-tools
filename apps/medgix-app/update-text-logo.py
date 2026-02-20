#!/usr/bin/env python3
import os
import re

# Text-only logo HTML
text_logo = '''<div class="logo">
      <a href="https://marketingtool.pro">
        <span class="logo-text">MarketingTool Pro</span>
      </a>
    </div>'''

# Folder with madgicx.com pages
folder = '/Users/loken/medgix-app--2/madgicx.com'

# Process all HTML files in the folder
for filename in os.listdir(folder):
    if filename.endswith('.html') and '?' not in filename:
        filepath = os.path.join(folder, filename)
        
        # Read the file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace any logo with text-only logo
        logo_pattern = r'(<div class="logo">.*?</div>)'
        content = re.sub(logo_pattern, text_logo, content, flags=re.DOTALL)
        
        # Update logo CSS to be text-only
        content = re.sub(
            r'\.pxl-header-custom \.logo-text \{[^}]+\}',
            '''.pxl-header-custom .logo-text {
      font-size: 24px;
      font-weight: 800;
      color: #fff;
      margin-left: 0;
      letter-spacing: -0.5px;
      font-family: 'Inter', sans-serif;''',
            content
        )
        
        # Remove icon CSS
        content = re.sub(r'\.pxl-header-custom \.logo-icon[^}]+\}', '', content)
        
        # Write back
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"Updated {filename}")

print("Done!")
