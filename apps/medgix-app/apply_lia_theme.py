import os
import glob
from bs4 import BeautifulSoup

# Apply Lia theme to all marketing pages
def apply_lia_theme():
    pages = glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/*.html') + \
            glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/blog/*.html') + \
            glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/case-studies/*.html')
    
    for page in pages:
        with open(page, 'r+') as f:
            soup = BeautifulSoup(f.read(), 'html.parser')
            
            # Add theme CSS link if not present
            if not soup.find('link', {'href': '/css/lia-theme.css'}):
                theme_link = soup.new_tag('link', rel='stylesheet', href='/css/lia-theme.css')
                soup.head.append(theme_link)
            
            # Update logo styling
            logo = soup.find(class_=['logo', 'nav2_logo-link'])
            if logo:
                logo['class'] = logo.get('class', []) + ['lia-logo']
                
            # Update buttons
            for btn in soup.find_all(class_=['btn', 'button']):
                btn['class'] = btn.get('class', []) + ['lia-btn']
            
            # Write back changes
            f.seek(0)
            f.write(str(soup))
            f.truncate()
