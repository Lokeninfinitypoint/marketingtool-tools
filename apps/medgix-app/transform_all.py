#!/usr/bin/env python3
import os
import re
import shutil
import glob

# Define color replacements (Lia theme colors)
color_replacements = {
    # Primary colors - Change purple to cyan
    '#515FBC': '#00d4ff',
    '#6366f1': '#00d4ff',
    '#8b5cf6': '#0099cc',
    
    # Accent colors - Change pink to orange
    '#917aff': '#ff6b35',
    '#ec4899': '#ff6b35',
    '#a855f7': '#ff6b35',
    
    # Background colors
    '#0f0f0f': '#000000',
    '#1a1a1a': '#0a0a0a',
    '#111111': '#050505',
    '#2a2a2a': '#1a1a1a',
    
    # Text colors
    '#e5e5e5': '#ffffff',
    '#9ca3af': '#b0b0b0',
    '#6b7280': '#808080'
}

# Text replacements
text_replacements = {
    'Madgicx': 'MarketingTool Pro',
    'madgicx': 'marketingtool',
    'Madgicx.com': 'MarketingTool.pro',
    'madgicx.com': 'marketingtool.pro',
    'app.madgicx.com': 'app.marketingtool.pro',
    'Madgicxnow': 'MarketingToolPro',
    'madgicxnow': 'marketingtoolpro',
    'madgicxdotcom': 'marketingtoolpro'
}

# Add custom CSS for Lia theme styling
custom_css = """
<style>
/* Lia Digital Agency Theme Styling */
:root {
    --primary-color: #00d4ff !important;
    --secondary-color: #0099cc !important;
    --accent-color: #ff6b35 !important;
    --dark-bg: #000000 !important;
    --dark-card: #0a0a0a !important;
    --dark-section: #050505 !important;
    --dark-border: #1a1a1a !important;
    --text-primary: #ffffff !important;
    --text-secondary: #b0b0b0 !important;
    --text-muted: #808080 !important;
}

/* Small logo like Lia theme */
.nav2_logo-link img {
    display: none !important;
}

.nav2_logo-link::before {
    content: '';
    display: inline-block;
    width: 35px !important;
    height: 35px !important;
    margin-right: 10px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    border-radius: 8px !important;
    vertical-align: middle;
}

.nav2_logo-link::after {
    content: 'MarketingTool Pro' !important;
    display: inline-block;
    vertical-align: middle;
    font-weight: 800 !important;
    font-size: 1.2rem !important;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color)) !important;
    -webkit-background-clip: text !important;
    -webkit-text-fill-color: transparent !important;
    background-clip: text !important;
}

/* Dark theme header/footer */
.nav2_component {
    background: rgba(0, 0, 0, 0.95) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--dark-border);
}

.footer {
    background: var(--dark-card) !important;
    border-top: 1px solid var(--dark-border);
}

/* Button styling */
.button {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    border-radius: 50px !important;
}

/* Update all purple colors */
[class*="color-primary"],
[class*="text-primary"] {
    color: var(--primary-color) !important;
}

/* Update gradients */
[style*="gradient"] {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
}

/* Small footer styling */
.footer_logo {
    width: 35px !important;
    height: 35px !important;
}
</style>
"""

def process_html_file(input_path, output_path):
    """Process a single HTML file"""
    with open(input_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Apply text replacements
    for old, new in text_replacements.items():
        content = content.replace(old, new)
    
    # Apply color replacements
    for old, new in color_replacements.items():
        content = content.replace(old, new)
    
    # Add custom CSS after the first <style> tag
    if '<style>' in content and custom_css not in content:
        content = content.replace('<style>', '<style>\n' + custom_css, 1)
    
    # Write processed content
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"Processed: {input_path} -> {output_path}")

# Find all HTML files in madgicx.com directory
html_files = glob.glob('/Users/loken/medgix-app--2/madgicx.com/**/*.html', recursive=True)

# Process each file
for input_file in html_files:
    # Get relative path
    rel_path = os.path.relpath(input_file, '/Users/loken/medgix-app--2/madgicx.com')
    
    # Create output path
    output_file = os.path.join('/Users/loken/medgix-app--2/marketingtool-pro', rel_path)
    
    # Create directory if it doesn't exist
    os.makedirs(os.path.dirname(output_file), exist_ok=True)
    
    # Process the file
    process_html_file(input_file, output_file)

print("\nAll files processed successfully!")
print(f"Total files processed: {len(html_files)}")
