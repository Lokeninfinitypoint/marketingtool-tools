#!/usr/bin/env python3
import os
import re

# Folder with madgicx.com pages
folder = '/Users/loken/medgix-app--2/madgicx.com'

# Process all HTML files in the folder
for filename in os.listdir(folder):
    if filename.endswith('.html') and '?' not in filename:
        filepath = os.path.join(folder, filename)
        
        # Read the file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace footer logos
        content = re.sub(
            r'<img[^>]*logo[^>]*src="[^"]*"[^>]*>',
            '<span style="font-size: 18px; font-weight: 700; color: #fff;">MarketingTool Pro</span>',
            content
        )
        
        # Remove any script tags from footer
        content = re.sub(r'<footer[^>]*>.*?</footer>', '', content, flags=re.DOTALL)
        
        # Add simple footer
        if '<footer' not in content:
            content += '''
<footer style="background: #0a0a0a; padding: 40px 0; text-align: center; border-top: 1px solid #333;">
  <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    <p style="color: #888; font-size: 14px;">© 2024 MarketingTool Pro. All rights reserved.</p>
  </div>
</footer>'''
        
        # Write back
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"Fixed footer in {filename}")

print("Done!")
