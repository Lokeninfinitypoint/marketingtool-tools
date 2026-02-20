import os
import glob
from bs4 import BeautifulSoup

# Marketing page enhancements
def enhance_marketing_pages():
    """Add rich content and animations to all marketing pages"""
    pages = glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/*.html') + \
            glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/blog/*.html') + \
            glob.glob('/Users/loken/medgix-app--2/marketingtool-pro/case-studies/*.html')
    
    for page in pages:
        with open(page, 'r+') as f:
            soup = BeautifulSoup(f.read(), 'html.parser')
            
            # 1. Add client logos section
            if not soup.find('div', class_='client-logos'):
                logos_div = soup.new_tag('div', class_='client-logos')
                logos_div.append(soup.new_tag('h3', string='Trusted By'))
                logos_grid = soup.new_tag('div', class_='logos-grid')
                for i in range(1, 6):
                    logo = soup.new_tag('img', src=f'/images/clients/logo-{i}.png', alt=f'Client {i}')
                    logos_grid.append(logo)
                logos_div.append(logos_grid)
                soup.body.insert(1, logos_div)
            
            # 2. Enhance CTAs with animations
            for cta in soup.find_all(class_=['btn', 'button']):
                cta['class'].append('animated-btn')
                cta['data-aos'] = 'fade-up'
            
            # 3. Add testimonial videos
            if 'testimonial' in str(soup).lower() and not soup.find('video'):
                testimonial_section = soup.find(string=re.compile('testimonial', re.I)).find_parent('section')
                if testimonial_section:
                    video = soup.new_tag('video', src='/videos/testimonial.mp4', controls=True, class_='testimonial-video')
                    testimonial_section.append(video)
            
            # Write back changes
            f.seek(0)
            f.write(str(soup))
            f.truncate()
