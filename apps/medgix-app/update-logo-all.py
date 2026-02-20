#!/usr/bin/env python3
import os
import re

# Custom logo HTML
custom_logo = '''<div class="logo">
      <a href="https://marketingtool.pro">
        <div class="logo-icon">
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
            <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <span class="logo-text">MarketingTool Pro</span>
      </a>
    </div>'''

# Custom logo CSS
logo_css = '''    .pxl-header-custom .logo-icon {
      width: 32px;
      height: 32px;
      background: linear-gradient(135deg, #01E5E5 0%, #008787 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 12px;
      box-shadow: 0 2px 8px rgba(1, 229, 229, 0.3);
    }
    .pxl-header-custom .logo-icon svg {
      width: 20px;
      height: 20px;
      fill: white;
    }'''

# Folder with madgicx.com pages
folder = '/Users/loken/medgix-app--2/madgicx.com'

# Process all HTML files in the folder
for filename in os.listdir(folder):
    if filename.endswith('.html') and '?' not in filename:
        filepath = os.path.join(folder, filename)
        
        # Read the file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace logo HTML
        logo_pattern = r'(<div class="logo">.*?</div>)'
        content = re.sub(logo_pattern, custom_logo, content, flags=re.DOTALL)
        
        # Add logo CSS after .pxl-header-custom .logo img CSS
        if 'display: none;' in content and logo_css not in content:
            content = content.replace(
                '    .pxl-header-custom .logo img {\n      display: none;\n    }',
                '    .pxl-header-custom .logo img {\n      display: none;\n    }' + logo_css
            )
        
        # Write back
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"Updated {filename}")

print("Done!")
