#!/usr/bin/env python3

# Read the copied file
with open('/Users/loken/medgix-app--2/index-hybrid-final.html', 'r') as f:
    content = f.read()

# Replace all Madgicx references with MarketingTool Pro
content = content.replace('Madgicx', 'MarketingTool Pro')
content = content.replace('madgicx', 'marketingtool')

# Replace URLs
content = content.replace('app.madgicx.com', 'app.marketingtool.pro')
content = content.replace('madgicx.com', 'marketingtool.pro')

# Replace social media
content = content.replace('madgicxnow', 'marketingtoolpro')
content = content.replace('madgicxdotcom', 'marketingtoolpro')

# Add dark theme styles at the beginning of the style section
dark_theme_styles = """
/* Dark Theme Header/Footer Styles */
:root {
    --primary-color: #6366f1;
    --secondary-color: #8b5cf6;
    --dark-bg: #0f0f0f;
    --dark-card: #1a1a1a;
    --dark-text: #e5e5e5;
    --accent-color: #ec4899;
}

/* Dark Header */
.nav2_component {
    background: rgba(15, 15, 15, 0.95) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.nav2_link {
    color: var(--dark-text) !important;
}

.nav2_link:hover {
    color: var(--primary-color) !important;
}

.nav2_button {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    color: white !important;
    border-radius: 50px !important;
}

/* Dark Footer */
.footer {
    background: var(--dark-card) !important;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.footer_link {
    color: #a0a0a0 !important;
}

.footer_link:hover {
    color: var(--primary-color) !important;
}

/* Hide original logo and show custom */
.nav2_logo-link img {
    display: none !important;
}

.nav2_logo-link::before {
    content: '';
    display: inline-block;
    width: 40px;
    height: 40px;
    margin-right: 10px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 10px;
    vertical-align: middle;
}

.nav2_logo-link::after {
    content: 'MarketingTool Pro';
    display: inline-block;
    vertical-align: middle;
    font-weight: 800;
    font-size: 1.5rem;
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

"""

# Insert dark theme styles after the first <style> tag
content = content.replace('<style>', '<style>\n' + dark_theme_styles)

# Write the updated content
with open('/Users/loken/medgix-app--2/index-hybrid-final.html', 'w') as f:
    f.write(content)

print("Hybrid website created successfully!")
print("Header and footer are now dark themed!")
print("All content, animations, and images from Madgicx are preserved!")
