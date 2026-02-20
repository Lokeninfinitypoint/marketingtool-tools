from meta_api import MetaAPI
from appwrite_api import AppwriteDB
import datetime

def weekly_reports():
    """Generate and send weekly performance reports"""
    meta = MetaAPI(access_token='YOUR_META_TOKEN')
    db = AppwriteDB()
    users = db.get_all_users()
    
    for user in users:
        campaigns = meta.get_user_campaigns(user['id'])
        report = {
            'date': datetime.datetime.now().strftime('%Y-%m-%d'),
            'total_spend': sum(c['spend'] for c in campaigns),
            'total_conversions': sum(c['conversions'] for c in campaigns),
            'top_performers': sorted(campaigns, key=lambda x: -x['roas'])[:3]
        }
        db.store_report(user['id'], report)
        send_email(user['email'], 'Weekly Report', format_report(report))
