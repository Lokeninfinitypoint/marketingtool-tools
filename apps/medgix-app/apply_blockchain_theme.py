import os
import json
from bs4 import BeautifulSoup

def apply_theme():
    with open('themes/blockchain-saas/theme.json') as f:
        config = json.load(f)
    
    for page in glob.glob('marketingtool-pro/**/*.html', recursive=True):
        with open(page, 'r+') as f:
            soup = BeautifulSoup(f.read(), 'html.parser')
            
            # Add theme CSS
            if not soup.find('link', {'href': '/themes/blockchain-saas/css/main.css'}):
                css_link = soup.new_tag('link', 
                    rel='stylesheet', 
                    href='/themes/blockchain-saas/css/main.css')
                soup.head.append(css_link)
            
            # Update theme colors
            for el in soup.find_all(class_='theme-primary'):
                el['style'] = f'color: {config["colors"]["primary"]}'
            
            f.seek(0)
            f.write(str(soup))
            f.truncate()
