import os
import glob
from bs4 import BeautifulSoup

# Tool page enhancements
def enhance_tool_pages():
    """Add advanced functionality to all tool pages"""
    pages = glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/tools/**/*.html', recursive=True)
    
    for page in pages:
        with open(page, 'r+') as f:
            soup = BeautifulSoup(f.read(), 'html.parser')
            
            # 1. Add template galleries
            if 'template' in str(soup).lower() and not soup.find(class_='template-gallery'):
                gallery = soup.new_tag('div', class_='template-gallery')
                for i in range(1, 7):
                    template = soup.new_tag('div', class_='template-card')
                    img = soup.new_tag('img', src=f'/images/templates/template-{i}.jpg', alt=f'Template {i}')
                    template.append(img)
                    gallery.append(template)
                soup.main.append(gallery)
            
            # 2. Add live preview functionality
            if 'preview' in str(soup).lower() and not soup.find(class_='live-preview'):
                preview_div = soup.new_tag('div', class_='live-preview')
                preview_div['data-interactive'] = 'true'
                soup.main.append(preview_div)
            
            # 3. Add collaboration features
            if not soup.find(class_='collaboration-widget'):
                widget = soup.new_tag('div', class_='collaboration-widget')
                widget.append(soup.new_tag('h4', string='Team Collaboration'))
                widget.append(soup.new_tag('button', string='Share with Team'))
                soup.footer.insert(0, widget)
            
            # Write back changes
            f.seek(0)
            f.write(str(soup))
            f.truncate()
