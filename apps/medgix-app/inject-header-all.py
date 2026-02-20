#!/usr/bin/env python3
import os
import re

# Read the custom header
with open('/Users/loken/medgix-app--2/brighthub-clone/brighthub.casethemes.net/blockchain-web3-saas/header-custom.html', 'r') as f:
    custom_header = f.read()

# Read the CSS to hide original header
hide_header_css = '''<style>
#pxl-header-elementor { display: none !important; }
#pxl-header-mobile { display: none !important; }
body { padding-top: 80px; }
</style>'''

# Folder with madgicx.com pages
folder = '/Users/loken/medgix-app--2/madgicx.com'

# Process all HTML files in the folder
for filename in os.listdir(folder):
    if filename.endswith('.html') and '?' not in filename:
        filepath = os.path.join(folder, filename)
        
        # Read the file
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Check if already has custom header
        if 'pxl-header-custom' in content:
            print(f"Skipping {filename} - already has custom header")
            continue
        
        # Find where to inject header (after <div id="pxl-wapper">)
        wrapper_match = re.search(r'<div id="pxl-wapper"[^>]*>', content)
        if wrapper_match:
            # Insert header after the wrapper div
            insert_pos = wrapper_match.end()
            new_content = content[:insert_pos] + hide_header_css + custom_header + content[insert_pos:]
            
            # Update favicon if needed
            new_content = new_content.replace('favicon.png', 'favicon.png')
            
            # Write back
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            
            print(f"Updated {filename}")
        else:
            print(f"Could not find wrapper in {filename}")

print("Done!")
