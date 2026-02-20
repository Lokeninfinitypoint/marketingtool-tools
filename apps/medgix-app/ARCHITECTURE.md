# MarketingTool Pro Architecture

## Overview
Complete clone of Madgicx with:
- **Website**: marketingtool.pro (Lia theme)
- **App**: app.marketingtool.pro (React/Next.js)
- **Backend**: api.marketingtool.pro (FastAPI)
- **Auth**: auth.marketingtool.pro (Appwrite)
- **Automation**: wm.marketingtool.pro (Windmill)

## Core Components

### 1. Appwrite (Authentication & Database)
- **Purpose**: User management, authentication, basic data storage
- **Services**:
  - User signup/login
  - Session management
  - Database for user settings/plans
  - File storage for reports/exports

### 2. Windmill (Automation Engine)
- **Purpose**: All marketing automation workflows
- **Key Workflows**:
  - Daily campaign optimization
  - Weekly reporting
  - AI comment responder
  - Budget rules automation
  - Meta API syncing

### 3. Frontend (Next.js)
- **Pages**:
  - AI Chat
  - AI Marketer
  - Optimization Dashboard
  - Analytics
  - Creatives Hub
  - Automation Tools
- **Features**:
  - Real-time dashboards
  - Interactive charts
  - Campaign management
  - Ad creation wizard

### 4. Backend (FastAPI)
- **Endpoints**:
  - Campaign management
  - Reporting
  - Analytics processing
  - Integration with Meta APIs

## Data Flow
1. User logs in via Appwrite
2. Frontend requests data from FastAPI backend
3. Backend processes requests and stores data in Appwrite
4. Windmill runs scheduled automation workflows
5. Results stored in Appwrite DB and displayed in frontend

## Security Setup
- **Public Facing**:
  - marketingtool.pro
  - app.marketingtool.pro
  - api.marketingtool.pro
- **Private/Internal**:
  - wm.marketingtool.pro (password protected)
  - Appwrite admin console (restricted)
  - Postgres/Redis (internal only)

## Deployment
- All services containerized with Docker
- Nginx reverse proxy with SSL termination
- Automated deployment via deploy.sh script
