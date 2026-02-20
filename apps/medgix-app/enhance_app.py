import os
import glob
from bs4 import BeautifulSoup
import re

# App page enhancements
def enhance_app_pages():
    """Add interactive elements to all app pages"""
    pages = glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/app/**/*.html', recursive=True)
    
    for page in pages:
        with open(page, 'r+') as f:
            soup = BeautifulSoup(f.read(), 'html.parser')
            
            # 1. Add tooltips to all form elements
            for input_field in soup.find_all(['input', 'select', 'textarea']):
                if input_field.get('name') and not input_field.get('title'):
                    input_field['title'] = f"Enter your {input_field['name'].replace('_', ' ')}"
                    input_field['class'] = input_field.get('class', []) + ['has-tooltip']
            
            # 2. Enhance data visualizations
            if 'chart' in str(soup).lower() and not soup.find('canvas'):
                for chart_div in soup.find_all(class_=re.compile('chart', re.I)):
                    canvas = soup.new_tag('canvas', class_='interactive-chart')
                    chart_div.append(canvas)
            
            # 3. Add real-time update indicators
            if 'dashboard' in page.lower() and not soup.find(class_='live-indicator'):
                header = soup.find('h1') or soup.find('h2')
                if header:
                    indicator = soup.new_tag('span', class_='live-indicator')
                    indicator.string = 'Live Data'
                    indicator['data-aos'] = 'pulse'
                    header.insert_after(indicator)
            
            # Write back changes
            f.seek(0)
            f.write(str(soup))
            f.truncate()
