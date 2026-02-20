import re

# Read the original Madgicx index.html
with open('/Users/loken/medgix-app--2/madgicx.com/index.html', 'r') as f:
    madgicx_content = f.read()

# Read the hybrid header we created
with open('/Users/loken/medgix-app--2/index-hybrid.html', 'r') as f:
    hybrid_header = f.read()

# Extract the body content from Madgicx (after the first nav2_component)
body_match = re.search(r'<div class="main-wrapper">(.*)</div></div>', madgicx_content, re.DOTALL)
if body_match:
    body_content = body_match.group(1)
    
    # Create the complete hybrid file
    hybrid_content = hybrid_header + body_content + '</div></div></div>'
    
    # Replace Madgicx with MarketingTool Pro
    hybrid_content = hybrid_content.replace('Madgicx', 'MarketingTool Pro')
    hybrid_content = hybrid_content.replace('madgicx', 'marketingtool')
    
    # Replace URLs
    hybrid_content = hybrid_content.replace('app.madgicx.com', 'app.marketingtool.pro')
    hybrid_content = hybrid_content.replace('madgicx.com', 'marketingtool.pro')
    
    # Update social media links
    hybrid_content = hybrid_content.replace('madgicxnow', 'marketingtoolpro')
    hybrid_content = hybrid_content.replace('madgicxdotcom', 'marketingtoolpro')
    
    # Write the final hybrid file
    with open('/Users/loken/medgix-app--2/index-hybrid-final.html', 'w') as f:
        f.write(hybrid_content)
    
    print("Hybrid file created successfully!")
else:
    print("Could not extract body content")
