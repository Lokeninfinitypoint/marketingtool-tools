#!/usr/bin/env python3
import os
import re
import glob

# New navigation HTML
new_nav = '''<div class="nav2_component"><div data-animation="default" data-collapse="medium" data-duration="400" data-easing="ease" data-easing2="ease" role="banner" class="nav2_padding w-nav"><div class="nav2_container w-container"><div class="nav2_brand"><a href="index.html" class="nav2_brand-link w-nav-brand"><img src="https://cdn.prod.website-files.com/614069317241cba124a0dd3b/673514528c96cad5a7a1e69a_madgicx-web-logo.png" loading="lazy" alt="Logo" height="Auto" class="nav2_image"/></a></div><nav role="navigation" class="nav2_menuu w-nav-menu"><div class="nav2_linkss"><a href="index.html" class="nav2_link w-nav-link">Home</a><a href="services.html" class="nav2_link w-nav-link">Services</a><div data-hover="false" data-delay="0" class="nav2_dropdown w-dropdown"><div class="nav2_dropdown_toggle w-dropdown-toggle"><div>Resources</div><svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 10 6" fill="none" class="nav2_chevron"><path d="M1.66602 1.33325L4.70472 4.37196C4.86744 4.53468 5.13126 4.53468 5.29398 4.37196L8.33268 1.33325" stroke="currentColor" stroke-opacity="0.48" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div><nav class="nav2_dropdown_list w-dropdown-list"><div class="nav2_workflow"><a href="resources/ppc-research.html" class="nav2_workflow_link w-inline-block"><div class="nav2_workflow_title">PPC Research & Insights</div><div class="nav2_workflow_par">Read original PPC research and insights</div></a><a href="resources/account-audit.html" class="nav2_workflow_link w-inline-block"><div class="nav2_workflow_title">Account Audit Report</div><div class="nav2_workflow_par">Get your free PPC audit</div></a><a href="resources/free-reporting.html" class="nav2_workflow_link w-inline-block"><div class="nav2_workflow_title">Free Reporting</div><div class="nav2_workflow_par">Test-drive our free reporting</div></a><a href="resources/free-tools.html" class="nav2_workflow_link w-inline-block"><div class="nav2_workflow_title">Free Tools</div><div class="nav2_workflow_par">Discover our free tools</div></a></div></nav></div><a href="blog.html" class="nav2_link w-nav-link">Blog</a><div data-hover="false" data-delay="0" class="nav2_dropdown w-dropdown"><div class="nav2_dropdown_toggle w-dropdown-toggle"><div>Company</div><svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 10 6" fill="none" class="nav2_chevron"><path d="M1.66602 1.33325L4.70472 4.37196C4.86744 4.53468 5.13126 4.53468 5.29398 4.37196L8.33268 1.33325" stroke="currentColor" stroke-opacity="0.48" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg></div><nav class="nav2_dropdown_list w-dropdown-list"><div class="nav2_resources"><div class="nav_company-links"><a href="help-center.html" class="nav_sublink button w-button" style="margin-bottom:10px;">Help Center</a><a href="changelog.html" class="nav_sublink">Changelog</a><a href="affiliate-program.html" class="nav_sublink">Affiliate Program</a><a href="contact.html" class="nav_sublink">Contact Us</a><a href="pricing.html" class="nav_sublink">Pricing</a><a href="about-us.html" class="nav_sublink">About Us</a></div></div></nav></div><div class="nav2_mobile-buttons"><a href="free-trial.html" class="button is-100 w-button">Free Trial</a><a href="sign-in.html" class="nav2_link w-nav-link">Sign In</a></div></div></nav><div class="nav2_main-links"><a href="sign-in.html" class="nav2_link w-nav-link">Sign In</a><a href="free-trial.html" class="button w-button">Free Trial</a></div><div class="nav2_burger w-nav-button"><div class="button is-burger"><div class="nav2_lines"><div class="nav2_line"></div><div class="nav2_line"></div><div class="nav2_line"></div></div><div>Menu</div></div></div></div></div></div>'''

# Pattern to match the old nav
nav_pattern = r'<div class="nav2_component">.*?</div></div></div></div></div>'

def update_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
        
        # Check if nav exists
        if '<div class="nav2_component">' in content:
            # Find the nav section more precisely
            start = content.find('<div class="nav2_component">')
            if start == -1:
                return False
            
            # Count closing divs to find the end
            depth = 0
            end = start
            i = start
            while i < len(content):
                if content[i:i+4] == '<div':
                    depth += 1
                    i += 4
                elif content[i:i+6] == '</div>':
                    depth -= 1
                    i += 6
                    if depth == 0:
                        end = i
                        break
                else:
                    i += 1
            
            if end > start:
                new_content = content[:start] + new_nav + content[end:]
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                return True
        return False
    except Exception as e:
        print(f"Error processing {filepath}: {e}")
        return False

# Process all HTML files
html_files = glob.glob('/Users/loken/medgix-app-/madgicx.com/*.html')
updated = 0
failed = 0

for filepath in html_files:
    if update_file(filepath):
        updated += 1
    else:
        failed += 1
    
    if updated % 100 == 0:
        print(f"Processed {updated} files...")

print(f"\nDone! Updated: {updated}, Failed: {failed}")
