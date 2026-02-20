#!/bin/bash

# 1. Install dependencies
apt-get update
apt-get install -y \
    nginx \
    docker.io \
    docker-compose \
    python3-pip \
    nodejs \
    npm

# 2. Configure Nginx
cat > /etc/nginx/sites-available/marketingtool.pro <<EOL
server {
    listen 80;
    server_name marketingtool.pro www.marketingtool.pro;
    
    location / {
        root /usr/share/nginx/html;
        index index.html;
    }
}

server {
    listen 80;
    server_name app.marketingtool.pro;
    
    location / {
        proxy_pass http://localhost:3000;
    }
}

server {
    listen 80;
    server_name api.marketingtool.pro;
    
    location / {
        proxy_pass http://localhost:8000;
    }
}

server {
    listen 80;
    server_name auth.marketingtool.pro;
    
    location / {
        proxy_pass http://localhost:8080;
    }
}

server {
    listen 80;
    server_name wm.marketingtool.pro;
    
    location / {
        proxy_pass http://localhost:8001;
        auth_basic "Restricted";
        auth_basic_user_file /etc/nginx/.htpasswd;
    }
}
EOL

# Enable sites
ln -s /etc/nginx/sites-available/marketingtool.pro /etc/nginx/sites-enabled/
systemctl restart nginx

# 3. Set up Appwrite (Auth)
docker run -d --name appwrite \
    -p 8080:80 \
    -v /var/lib/appwrite:/storage \
    appwrite/appwrite:latest

# 4. Set up Windmill (Automation)
docker run -d --name windmill \
    -p 8001:8000 \
    -v /var/lib/windmill:/data \
    windmilllabs/windmill:latest

# 5. Deploy Frontend (Next.js)
cd /var/www/app.marketingtool.pro
npm install
npm run build
npm run start &

# 6. Deploy Backend (FastAPI)
cd /var/www/api.marketingtool.pro
pip install -r requirements.txt
uvicorn main:app --host 0.0.0.0 --port 8000 &

# 7. Secure the server
# Basic auth for Windmill
apt-get install -y apache2-utils
htpasswd -cb /etc/nginx/.htpasswd admin yourpassword

# Firewall rules
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable

# 8. Add cron jobs for automation
(crontab -l 2>/dev/null; echo "0 8 * * * /usr/bin/docker exec windmill /app/workflows.py daily_optimization") | crontab -
(crontab -l 2>/dev/null; echo "0 9 * * 1 /usr/bin/docker exec windmill /app/workflows.py weekly_reports") | crontab -
(crontab -l 2>/dev/null; echo "*/15 * * * * /usr/bin/docker exec windmill /app/workflows.py ai_comment_responder") | crontab -

# 9. Initialize databases
curl -X POST http://localhost:8080/v1/database/collections \
    -H "Content-Type: application/json" \
    -H "X-Appwrite-Project: marketingtool-pro" \
    -H "X-Appwrite-Key: your-appwrite-key" \
    -d '{
        "name": "users",
        "permissions": {
            "read": [],
            "write": []
        }
    }'

curl -X POST http://localhost:8080/v1/database/collections \
    -H "Content-Type: application/json" \
    -H "X-Appwrite-Project: marketingtool-pro" \
    -H "X-Appwrite-Key: your-appwrite-key" \
    -d '{
        "name": "reports",
        "permissions": {
            "read": [],
            "write": []
        }
    }'

# 10. Update deployment for new package structure
rsync -avz /Users/loken/medgix-app--2/src/windmill/ root@31.220.107.19:/var/lib/windmill/scripts/core/
rsync -avz /Users/loken/medgix-app--2/windmill/ root@31.220.107.19:/var/lib/windmill/scripts/flows/

# 11. Deploy themes
# Deploy blockchain theme
rsync -avz themes/blockchain-saas/ root@31.220.107.19:/usr/share/nginx/html/themes/blockchain-saas/

# Deploy BrightHub theme
rsync -avz brighthub/ root@31.220.107.19:/usr/share/nginx/html/themes/brighthub/

echo "Deployment complete!"
